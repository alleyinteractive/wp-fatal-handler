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
		str_contains( $_SERVER['REQUEST_URI'] ?? '', '/wp-json/' ) => new JsonResponseHandler(),
		// Check if the request expects JSON.
		str_contains( $_SERVER['HTTP_ACCEPT'] ?? '', '/json' ) => new JsonResponseHandler(),
		defined( 'WP_CLI' ) && WP_CLI => new PlainTextHandler(),
		default => new PrettyPageHandler(),
	};

	$handler = apply_filters( 'wp_fatal_handler_whoops_handler', $handler );

	// If the handler is not an instance of Handler, default to
	// PrettyPageHandler. To unregister whoops error handling, use the
	// `wp_fatal_handler_whoops` filter to return `null`.
	if ( ! $handler instanceof Handler ) {
		$handler = new PrettyPageHandler();
	}

	$whoops  = new Whoops();
	$whoops->pushHandler( $handler );

	// Ignore non-fatal errors (warnings, notices, etc.).
	$whoops->silenceErrorsInPaths(
		// Ignore all non-fatal errors in WordPress.
		'/^' . preg_quote( ABSPATH, '/' ) . '.*/',
		E_WARNING | E_NOTICE | E_DEPRECATED | E_USER_DEPRECATED,
	);

	/**
	 * Filter the Whoops instance before it is registered.
	 *
	 * @param \Whoops\Run $whoops The Whoops instance.
	 */
	$whoops = apply_filters( 'wp_fatal_handler_whoops', $whoops );

	if ( $whoops instanceof Whoops ) {
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
 *
 * @return bool
 */
function should_register_handler(): bool {
	if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
		return false;
	}

	if ( defined( 'VIP_GO_APP_ENVIRONMENT' ) && 'local' !== VIP_GO_APP_ENVIRONMENT ) {
		return false;
	}

	return (bool) apply_filters( 'wp_fatal_handler_register', true );
}
