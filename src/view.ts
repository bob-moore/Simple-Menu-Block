import { store, getContext, getElement, withScope } from '@wordpress/interactivity';
import metadata from './block.json';

type Context = {
	isSubmenuActive: boolean;
};

type FocusedMenuItem = {
	ref: Element;
	context: Context;
}

type FocusedMenuItems = Map<Element, FocusedMenuItem>;

const { state, actions } = store(metadata.name, {
	state : {
		focusedMenuItems: new Map<Element, FocusedMenuItem>() as FocusedMenuItems
	},
	actions: {
		/**
		 * Toggles the submenu active state.
		 */
		toggle: () => {
			const context = getContext<Context>();
			context.isSubmenuActive = !context.isSubmenuActive;
		},

		/**
		 * Handles Touch events by toggling the submenu active state.
		 */
		handleTouch: ( event : TouchEvent ) => {
			const context = getContext<Context>();
			if ( ! context.isSubmenuActive ) {
				event.preventDefault();
				actions.toggle();
			}
		},

		/**
		 * Handles focus events by setting the submenu active state to true.
		 */
		handleFocus: ( event ) => {
			const context = getContext<Context>();
			const {ref} = getElement();
			const parent = ref.closest('[data-wp-context]');

			console.log( event );

			if ( parent && ! state.focusedMenuItems.has( parent ) ) {
				state.focusedMenuItems.set( parent, {
					ref: parent,
					context: context
				} );
			}
			context.isSubmenuActive = true;
		},

		/**
		 * Handles hover events by toggling the submenu active state.
		 */
		handleHover: ( event ) => {
			// actions.toggle();
		},

		/**
		 * Handles blur events.
		 */
		handleBlur: ( event ) => {
			console.log('blue', event.type);
			const context = getContext<Context>();
			setTimeout(() => {
				state.focusedMenuItems.forEach( ( item: FocusedMenuItem ) => {
					if ( ! item.ref.contains( document.activeElement ) && item.ref !== document.activeElement ) {
						item.context.isSubmenuActive = false
						state.focusedMenuItems.delete( item.ref );
					}
				} );
			}, 0);
		},
	},
});
