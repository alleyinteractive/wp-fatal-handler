<?php
/**
 * Plugin Name: WP Fatal Error Handler
 * Plugin URI: https://github.com/alleyinteractive/wp-fatal-handler
 * Description: A better fatal error handler for WordPress.
 * Version: 0.1.0
 * Author: Sean Fisher
 * Author URI: https://github.com/alleyinteractive/wp-fatal-handler
 * Requires at least: 5.9
 * Requires PHP: 8.2
 * Tested up to: 6.7
 *
 * @package wp-fatal-handler
 */

namespace Alley\WP\WP_Fatal_Error_Handler;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Root directory to this plugin.
 */
define( 'WP_FATAL_HANDLER_DIR', __DIR__ );

// Check if Composer is installed (remove if Composer is not required for your plugin).
if ( ! file_exists( __DIR__ . '/vendor/wordpress-autoload.php' ) ) {
	// Will also check for the presence of an already loaded Composer autoloader
	// to see if the Composer dependencies have been installed in a parent
	// folder. This is useful for when the plugin is loaded as a Composer
	// dependency in a larger project.
	if ( ! class_exists( \Composer\InstalledVersions::class ) ) {
		\add_action(
			'admin_notices',
			function () {
				?>
				<div class="notice notice-error">
					<p><?php esc_html_e( 'Composer is not installed and wp-fatal-handler cannot load. Try using a `*-built` branch if the plugin is being loaded as a submodule.', 'wp-fatal-handler' ); ?></p>
				</div>
				<?php
			}
		);

		return;
	}
} else {
	// Load Composer dependencies.
	require_once __DIR__ . '/vendor/wordpress-autoload.php';
}

// Load the plugin's main files.
require_once __DIR__ . '/src/main.php';

main();
