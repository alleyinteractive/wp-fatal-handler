<?php
/**
 * The main plugin function
 *
 * @package wp-fatal-handler
 */

namespace Alley\WP\WP_Fatal_Error_Handler;

use Whoops\Handler\Handler;
use Whoops\Run as Whoops;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;

/**
 * Instantiate the plugin.
 */
function main(): void {
	if ( ! should_register_handler() ) {
		return;
	}

	$handler = match ( true ) {
		// @phpstan-ignore-next-line argument.type
		str_contains( $_SERVER['REQUEST_URI'] ?? '', '/wp-json/' ) => new JsonResponseHandler(),  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		// @phpstan-ignore-next-line argument.type
		str_contains( $_SERVER['HTTP_ACCEPT'] ?? '', '/json' ) => new JsonResponseHandler(), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		defined( 'WP_CLI' ) && WP_CLI => new PlainTextHandler(),
		default => new PrettyPageHandler(),
	};

	/**
	 * Filter the Whoops handler before it is registered.
	 *
	 * This allows you to replace the default handler with a custom one.
	 *
	 * @param \Whoops\Handler\Handler $handler The Whoops handler.
	 */
	$handler = apply_filters( 'wp_fatal_handler_whoops_handler', $handler );

	// If the handler is not an instance of Handler, default to
	// PrettyPageHandler. To unregister whoops error handling, use the
	// `wp_fatal_handler_whoops` filter to return `null`.
	if ( ! $handler instanceof Handler ) { // @phpstan-ignore-line instanceof.alwaysTrue
		$handler = new PrettyPageHandler();
	}

	// Add some CSS to the PrettyPageHandler.
	if ( $handler instanceof PrettyPageHandler ) {
		$handler->setPageTitle( 'Fatal WordPress Error' );
		$handler->addResourcePath( dirname( __DIR__ ) . '/css' );
		$handler->addCustomCss( 'whoops.css' );
	}

	$whoops = new Whoops();
	$whoops->pushHandler( $handler );

	// Ignore non-fatal errors (warnings, notices, etc.).
	$whoops->silenceErrorsInPaths(
		// Ignore all non-fatal errors in WordPress.
		'/^' . preg_quote( ABSPATH, '/' ) . '.*/',
		E_WARNING | E_NOTICE | E_DEPRECATED | E_USER_DEPRECATED | E_USER_WARNING | E_USER_ERROR | E_USER_NOTICE,
	);

	// Ignore E_STRICT in PHP <= 8.3 since it is deprecated in 8.4.
	if ( version_compare( PHP_VERSION, '8.4', '<' ) ) {
		$whoops->silenceErrorsInPaths(
			'/^' . preg_quote( ABSPATH, '/' ) . '.*/',
			E_STRICT,
		);
	}

	/**
	 * Filter the Whoops instance before it is registered.
	 *
	 * @param \Whoops\Run $whoops The Whoops instance.
	 */
	$whoops = apply_filters( 'wp_fatal_handler_whoops', $whoops );

	if ( $whoops instanceof Whoops ) { // @phpstan-ignore-line instanceof.alwaysTrue
		$whoops->register();
	}
}

/**
 * Determine whether the fatal error handler should be registered.
 *
 * This function checks if the `WP_DEBUG` constant is defined and set to true,
 * and if the environment is set to 'local' in the `VIP_GO_APP_ENVIRONMENT
 * constant`. It also allows for filtering the registration
 * through the `wp_fatal_handler_register` filter.
 */
function should_register_handler(): bool {
	$should_run = true;

	if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
		$should_run = false;
	}

	if ( defined( 'VIP_GO_APP_ENVIRONMENT' ) && 'local' !== VIP_GO_APP_ENVIRONMENT ) {
		$should_run = false;
	}

	/**
	 * Filter to determine whether the fatal error handler should be registered.
	 */
	return (bool) apply_filters( 'wp_fatal_handler_register', $should_run );
}
