<?php
/**
 * The main plugin function
 *
 * @package wp-fatal-handler
 */

namespace Alley\WP\WP_Fatal_Error_Handler;

use Alley\WP\Features\Group;

/**
 * Instantiate the plugin.
 */
function main(): void {
	// Add features here.
	$plugin = new Group();

	$plugin->boot();
}
