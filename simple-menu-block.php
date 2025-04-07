<?php
/**
 * Plugin Name:       Simple Menu Block
 * Plugin URI:        https://github.com/bob-moore/Simple-Menu-Block/
 * Author:            Bob Moore
 * Author URI:        https://www.bobmoore.dev
 * Description:       Simple, classic menu block.
 * Version:           0.1.3
 * Requires at least: 6.7
 * Tested up to:      6.7
 * Requires PHP:      8.2
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       simple-menu-block
 * Branch:		      main
 * Low Banner:        assets/banner-772x250.jpg
 * High Banner:       assets/banner-772x250.jpg
 *
 * @package           simple-menu-block
 */

namespace Rcm\SimpleMenuBlock;

use Rcm\SimpleMenuBlock\Deps\MarkedEffect\GHPluginUpdater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/scoped/autoload.php';
require_once __DIR__ . '/vendor/scoped/scoper-autoload.php';



if ( ! class_exists( 'Rcm\\SimpleMenuBlock\\Plugin' ) ) {
	/**
	 * Require function files
	 */
	require_once __DIR__ . '/inc/Functions.php';
	/**
	 * Main Simple Menu Block Class.
	 */
	class Plugin {
		/**
		 * Initialize the plugin.
		 */
		public function init(): void
		{
			add_action( 'init', [ $this, 'registerBlock' ] );
			/**
			* Config array for Github Updater.
			*/
			new GHPluginUpdater\Main( __FILE__, [
				'github.user' => 'bob-moore',
				'github.repo' => 'simple-menu-block',
				'github.branch' => 'main',
				'config.banners' => [
					'low' => trailingslashit( plugin_dir_url( __FILE__ ) ) . 'assets/banner-772x250.jpg',
					'high' => trailingslashit( plugin_dir_url( __FILE__ ) ) . 'assets/banner-1544x500.jpg',
				],
				'config.icons' => [
					'default' => trailingslashit( plugin_dir_url( __FILE__ ) ) . 'assets/icon.png',
				]
			] );
		}

		/**
		 * Registers the block using the metadata loaded from the `block.json` file.
		 * Behind the scenes, it registers also all assets so they can be enqueued
		 * through the block editor in the corresponding context.
		 *
		 * @see https://developer.wordpress.org/reference/functions/register_block_type/
		 */
		public function registerBlock(): void
		{
			register_block_type_from_metadata( __DIR__ . '/build' );
		}
	}
	$plugin = new Plugin();

	$plugin->init();
}