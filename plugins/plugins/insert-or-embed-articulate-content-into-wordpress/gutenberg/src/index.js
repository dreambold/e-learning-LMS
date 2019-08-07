/**
 * Block dependencies
 */
import './style.scss';

import ArticulateBlock from './articulate-block.js';

/**
 * Internal block libraries
 */
const { __ } = wp.i18n;

const { registerBlockType } = wp.blocks;

/**
 * Register block
 */
export default registerBlockType( 'e-learning/block', {
	title: __( 'e-Learning' ),
	description: __( 'Quickly embed or insert e-learning content into a post or page. Supports Articulate, Captivate, iSpring, and more.' ),
	category: 'common',
	icon: 'welcome-learn-more',
	keywords: [
		__( 'e-learning' ),
		__( 'learn' ),
		__( 'course' )
	],
	attributes: {
		src: {
			type: 'string'
		},
		href: {
			type: 'string'
		},
		type: {
			type: 'string',
			default: 'iframe'
		},
		width: {
			type: 'string',
			default: '100%'
		},
		height: {
			type: 'string',
			default: '600'
		},
		ratio: {
			type: 'string',
			default: '4:3'
		},
		frameborder: {
			type: 'number',
			default: 0
		},
		scrolling: {
			type: 'string',
			default: 'no'
		},
		title: {
			type: 'string'
		},
		'link_text': {
			type: 'string'
		},
		button: {
			type: 'string'
		},
		scrollbar: {
			type: 'string'
		},
		'colorbox_theme': {
			type: 'string'
		},
		'size_opt': {
			type: 'string'
		}
	},

	edit: ArticulateBlock,

	save: () => {
		return null;
	}
});
