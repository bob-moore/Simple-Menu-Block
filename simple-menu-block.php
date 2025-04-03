<?php
/**
 * Plugin Name:       Simple Menu Block
 * Plugin URI:        https://github.com/bob-moore/Simple-Menu-Block
 * Author:            Bob Moore
 * Author URI:        https://www.bobmoore.dev
 * Description:       Simple, classic menu block.
 * Version:           0.1.2
 * Requires at least: 6.7
 * Tested up to:      6.7
 * Requires PHP:      8.2
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       simple-menu-block
 * Github URI:        https://github.com/bob-moore/Simple-Menu-Block
 *
 * @package           simple-menu-block
 */

namespace Rcm\SimpleMenuBlock;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

var_dump( __DIR__ . '/build' );
new Updater( __FILE__ );

if ( ! class_exists( 'Rcm\\SimpleMenuBlock\\Plugin' ) ) {
	/**
	 * Require function files
	 */
	require_once __DIR__ . '/inc/Functions.php';
	require_once __DIR__ . '/inc/Updater.php';
	/**
	 * Main Simple Menu Block Class.
	 */
	class Plugin {

		/**
		 * Initialize the plugin.
		 */
		public static function init(): void
		{
			add_action( 'init', [ __CLASS__, 'registerBlock' ] );

		}

		/**
		 * Registers the block using the metadata loaded from the `block.json` file.
		 * Behind the scenes, it registers also all assets so they can be enqueued
		 * through the block editor in the corresponding context.
		 *
		 * @see https://developer.wordpress.org/reference/functions/register_block_type/
		 */
		public static function registerBlock(): void
		{
			register_block_type_from_metadata( __DIR__ . '/build' );

		}
	}
	Plugin::init();
}