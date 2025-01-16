import { registerBlockType } from '@wordpress/blocks';
import './styles/style.scss';
import './styles/editor.scss';
import { Edit } from './components/edit';
import { Save } from './components/save';
import metadata from './block.json';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/#registering-a-block
 */
registerBlockType(metadata.name, {
	edit: Edit
});
