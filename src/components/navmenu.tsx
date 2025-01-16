import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import { Fragment } from '@wordpress/element';
import metadata from '../block.json';

interface Attributes {
	menu: string;
	layout: string;
}

/**
 * NavMenu component to render the selected menu.
 *
 * @param {Object} props - Component props.
 * @param {Attributes} props.attributes - Block attributes.
 * @returns {JSX.Element} The rendered component.
 */
export const NavMenu: React.FC<{ attributes: Attributes }> = ({ attributes }) => {
	const { menu, layout } = attributes;

	if (!menu) {
		return <p>{__( 'Select Menu', 'rcm-simple-menu' )}</p>;
	} else {
		return (
			<Fragment>
				<ServerSideRender
					block={metadata.name}
					attributes={{
						menu,
						layout,
						editMode: true,
					}}
				/>
			</Fragment>
		);
	}
};