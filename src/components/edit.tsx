import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { NavMenu } from './navmenu';

interface Menu {
	id: string;
	name: string;
}

interface Attributes {
	menu: string;
	layout: string;
	editMode: boolean;
}

interface EditProps {
	attributes: Attributes;
	setAttributes: (attributes: Partial<Attributes>) => void;
}

/**
 * Edit component for the block.
 *
 * @param {Object} props - Component props.
 * @param {Attributes} props.attributes - Block attributes.
 * @param {Function} props.setAttributes - Function to set block attributes.
 * @returns {JSX.Element} The rendered component.
 */
export const Edit: React.FC<EditProps> = ({ attributes, setAttributes }) => {
	const blockProps = useBlockProps();
	const { menu, layout } = attributes;
	const [ menus, setMenus ] = useState<Menu[]>([]);
	const layouts = [
		{
			label: __('Horizontal', 'rcm-simple-menu'),
			value: 'horizontal',
		},
		{
			label: __('Vertical', 'rcm-simple-menu'),
			value: 'vertical',
		},
		{
			label: __('Dropdown', 'rcm-simple-menu'),
			value: 'dropdown',
		},
	];

	useEffect(() => {
		apiFetch( { path: '/wp/v2/menus' } )
			.then( (data: Menu[] ) => {
				setMenus(data);
			} )
			.catch( ( error: Error ) => {
				console.error('Failed to fetch menus:', error);
			} );
	}, [] );

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Menu Settings', 'rcm-simple-menu')} initialOpen={true}>
					<SelectControl
						label={__('Select Menu', 'rcm-simple-menu')}
						value={menu}
						options={[
							{ label: __('Select a Menu', 'rcm-simple-menu'), value: '' },
							...menus.map( ( menu : Menu ) => ({
								label: menu.name,
								value: menu.id,
							})),
						]}
						onChange={ ( value : string ) => setAttributes({ menu: value })}
					/>
					<SelectControl
						label={__('Menu Layout', 'rcm-simple-menu')}
						value={layout}
						options={[
							{ label: __('Select a Layout', 'rcm-simple-menu'), value: '' },
							...layouts.map((layout) => ({
								label: layout.label,
								value: layout.value,
							})),
						]}
						onChange={(value : string) => setAttributes({ layout: value })}
					/>
				</PanelBody>
			</InspectorControls>

			<NavMenu attributes={attributes} />
		</div>
	);
};