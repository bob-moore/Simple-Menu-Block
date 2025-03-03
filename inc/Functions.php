<?php
/**
 * Functions for the Simple Menu Block.
 *
 * PHP Version 8.2
 *
 * @package simple-menu-block
 * @author  Bob Moore <bob.moore@midwestfamilymadison.com>
 * @license GPL-2.0+ <http://www.gnu.org/licenses/gpl-2.0.txt>
 * @link    https://www.midwestfamilymadison.com
 * @since   1.0.0
 */

namespace Rcm\SimpleMenuBlock;
/**
 * Inject a wrapper around the menu item and submenu toggle button.
 *
 * @param string    $item_output The menu item’s starting HTML output.
 * @param \WP_Post  $item        Menu item data object.
 * @param int       $depth       Depth of menu item. Used for padding.
 * @param \stdClass $menu_args  An object of wp_nav_menu() arguments.
 *
 * @return string
 */
function inject_nav_item_wrapper( string $item_output, \WP_Post $item, int $depth, \stdClass $menu_args ): string
{
	if ( ! is_simple_menu_block( $menu_args ) ) {
		return $item_output;
	}

	$toggle_button = '
		<button 
			class="sub-menu-toggle" 
			data-wp-on--click="actions.handleClick"
			data-wp-bind--aria-expanded="context.isSubmenuActive"
		>
			<span class="button-icon"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 9l6 6l6 -6" /></svg></span>
			<span class="screen-reader-text">Toggle Submenu</span>
		</button>';

	return sprintf(
		'<span class="nav-item-container">%s%s</span>',
		$item_output,
		get_menu_layout( $menu_args ) === 'dropdown' && in_array( 'menu-item-has-children', $item->classes )
			? $toggle_button
			: ''
	);
}
add_filter( 'walker_nav_menu_start_el', __NAMESPACE__ . '\inject_nav_item_wrapper', 10, 4 );

/**
 * Add interactivity attributes to the menu item’s <li> element.
 *
 * @param array<string, string> $atts      The HTML attributes applied to the
 *                                         menu item’s <li> element.
 * @param \WP_Post              $menu_item The current menu item object.
 * @param \stdClass             $menu_args An object of wp_nav_menu() arguments.
 * @param int                   $depth     Depth of menu item. Used for padding.
 *
 * @return array<string, string>
 */
function add_li_interactivity_attributes( array $atts, \WP_Post $menu_item, \stdClass $menu_args, int $depth ): array
{
	if ( ! is_simple_menu_block( $menu_args ) ) {
		return $atts;
	}

	$layout = get_menu_layout( $menu_args );

	if ( ! in_array( 'menu-item-has-children', $menu_item->classes, true ) ) {
		return $atts;
	}

	if ( in_array( $layout, [ 'dropdown', 'horizontal' ], true ) ) {
		$atts['data-wp-interactive'] = 'rcm/simple-menu';
		$atts['data-wp-context'] = '{ "isSubmenuActive": false }';
	}
	if ( 'horizontal' === $layout ) {
		$atts['data-wp-on--mouseenter'] = 'actions.handleHover';
		$atts['data-wp-on--mouseleave'] = 'actions.handleHover';
	}
	return $atts;
}
add_filter( 'nav_menu_item_attributes', __NAMESPACE__ . '\add_li_interactivity_attributes', 10, 4 );
/**
 * Add interactivity attributes to the sub-menu.
 *
 * @param string    $html The HTML list content for the sub-menu items.
 * @param \stdClass $menu_args An object of wp_nav_menu() arguments.
 *
 * @return string
 */
function add_subnav_interactivity_attributes( string $html, \stdClass $menu_args ): string
{
	if ( ! is_simple_menu_block( $menu_args ) ) {
		return $html;
	}

	if ( 'vertical' === get_menu_layout( $menu_args ) ) {
		return $html;
	}

	$processor = new \WP_HTML_Tag_Processor( $html );

	while ( $processor->next_tag( [ 'class_name' => 'sub-menu', 'tag_name' => 'ul' ] ) ) {
		$processor->set_attribute( 'data-wp-bind--aria-expanded', 'context.isSubmenuActive' );

		if ( 'dropdown' === get_menu_layout( $menu_args ) ) {
			$processor->set_attribute( 'data-wp-bind--hidden', '!context.isSubmenuActive' );
		}
	}
	return $processor->get_updated_html();
}
add_filter( 'wp_nav_menu_items', __NAMESPACE__ . '\add_subnav_interactivity_attributes', 10, 2 );
/**
 * Add interactivity attributes to the menu item links.
 *
 * @param string    $html The HTML list content for the menu items.
 * @param \stdClass $menu_args An object of wp_nav_menu() arguments.
 *
 * @return string
 */
function add_link_interactivity_attributes( string $html, \stdClass $menu_args ): string
{
	if ( ! is_simple_menu_block( $menu_args ) ) {
		return $html;
	}

	$edit_mode = isset( $menu_args->in_editor ) && $menu_args->in_editor ? true : false;

	$layout = get_menu_layout( $menu_args );

	if ( $edit_mode || 'horizontal' === $layout ) {
		$processor = new \WP_HTML_Tag_Processor( $html );

		while ( $processor->next_tag( [ 'tag_name' => 'a' ] ) ) {
			if ( $edit_mode ) {
				$processor->set_attribute( 'onclick', 'return false;' );
			} else {
				$processor->set_attribute( 'data-wp-on--touchstart', 'actions.handleTouch' );
			}

			if ( 'horizontal' === $layout ) {
				$processor->set_attribute( 'data-wp-on--focus', 'actions.handleFocus' );
				$processor->set_attribute( 'data-wp-on--blur', 'actions.handleBlur' );
			}
		}
		$html = $processor->get_updated_html();
	}

	return $html;
}
add_filter( 'wp_nav_menu_items', __NAMESPACE__ . '\add_link_interactivity_attributes', 10, 2 );
/**
 * Check if the current menu is a simple menu block.
 *
 * @param \stdClass $menu_args An object containing wp_nav_menu() arguments.
 *
 * @return bool
 */
function is_simple_menu_block( \stdClass $menu_args ): bool
{
	return isset( $menu_args->generator )
		&& 'simple-menu-block' === $menu_args->generator
		&& isset( $menu_args->block_attributes );
}

/**
 * Get the current layout of a simple menu block menu.
 *
 * @param \stdClass $menu_args An object containing wp_nav_menu() arguments.
 *
 * @return string
 */
function get_menu_layout( \stdClass $menu_args ): string
{
	return match ( true ) {
		! empty( $menu_args->block_attributes['layout'] ?? '' ) => $menu_args->block_attributes['layout'],
		default => 'horizontal',
	};
}

