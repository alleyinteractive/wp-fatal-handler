<?php
/**
 * The main plugin function
 *
 * @package wp-fatal-handler
 */

namespace Alley\WP\WP_Fatal_Error_Handler;

use ErrorException;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;

/**
 * Instantiate the plugin.
 */
function main(): void {
	add_filter( 'wp_php_error_message', __NAMESPACE__ . '\handle_wp_php_error_message', 10, 2 );
}

/**
 * Determine if the fatal error handler should intercept errors.
 *
 * @return bool True if the fatal error handler should intercept errors, false otherwise.
 */
function should_intercept_error(): bool {
	if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
		return false;
	}

	if ( defined( 'VIP_GO_APP_ENVIRONMENT' ) && 'local' !== VIP_GO_APP_ENVIRONMENT ) {
		return false;
	}

	return (bool) apply_filters( 'wp_fatal_handler_intercept_error', true );
}

/**
 * Handle the `wp_php_error_message` filter.
 *
 * @param string $message Default error message.
 * @param array  $error Error details.
 * @phpstan-param array{type?: int, message?: string, file?: string, line?: int} $error
 * @return string|never
 */
function handle_wp_php_error_message( string $message, array $error ): mixed {
	if ( ! should_intercept_error() ) {
		return $message;
	}

	if ( ! class_exists( HtmlErrorRenderer::class ) ) {
		_doing_it_wrong(
			'wp_fatal_handler',
			'The HtmlErrorRenderer class is not available. Please ensure the Symfony ErrorHandler component is installed.', // phpcs:ignore
			'0.1.0'
		);

		return $message;
	}

	$instance = new HtmlErrorRenderer(
		debug: true,
		charset: get_bloginfo( 'charset', 'display' ),
		projectDir: ABSPATH,
	);

	$exception = new ErrorException(
		$error['message'] ?? $message,
		0,
		$error['type'] ?? E_ERROR,
		$error['file'] ?? '',
		$error['line'] ?? 0,
	);

	echo $instance->render( $exception )->getAsString(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	exit( 1 );
}
