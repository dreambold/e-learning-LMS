/**
 * Handles the creation and the behavior of the Toolset Content Template block.
 *
 * @since  2.6.0
 */

/**
 * Block dependencies
 */
import icon from './icon';
import Inspector from './inspector/inspector';
import classnames from 'classnames';
import CTSelect from './inspector/ct-select';
import CTPreview from './ct-preview';
import './styles/editor.scss';

/**
 * Internal block libraries
 */
const {
	__,
	setLocaleData,
} = wp.i18n;

const {
	registerBlockType,
} = wp.blocks;

const {
	Placeholder,
} = wp.components;

const {
	RawHTML,
} = wp.element;

const {
	toolset_ct_block_strings: i18n,
} = window;

if ( i18n.locale ) {
	setLocaleData( i18n.locale, 'wpv-views' );
}

const name = i18n.blockName;

const settings = {
	title: __( 'Content Template', 'wpv-views' ),
	description: __( 'Add a Content Template to the editor.', 'wpv-views' ),
	category: i18n.blockCategory,
	icon: icon.blockIcon,
	keywords: [
		__( 'Toolset', 'wpv-views' ),
		__( 'Content Template', 'wpv-views' ),
		__( 'Shortcode', 'wpv-views' ),
	],

	edit: props => {
		const onChangeCT = ( event ) => {
			props.setAttributes( { ct: event.target.value } );
		};

		return [
			!! (
				props.focus ||
				props.isSelected
			) && (
				<Inspector
					key="wpv-gutenberg-ct-block-render-inspector"
					attributes={
						{
							ct: props.attributes.ct,
						}
					}
					onChangeCT={ onChangeCT }
				/>
			),
			(
				'' === props.attributes.ct ?
					<Placeholder
						key="ct-block-placeholder"
						className={ classnames( 'wp-block-toolset-ct' ) }
					>
						<div className="wp-block-toolset-ct-placeholder">
							{ icon.blockPlaceholder }
							<h2>{ __( 'Toolset Content Template', 'wpv-views' ) }</h2>
							<CTSelect
								attributes={
									{
										ct: props.attributes.ct,
									}
								}
								className={ classnames( 'components-select-control__input' ) }
								onChangeCT={ onChangeCT }
							/>
						</div>
					</Placeholder> :
					<CTPreview
						key="toolset-ct-gutenberg-block-preview"
						className={ classnames( props.className, 'wp-block-toolset-ct-preview' ) }
						attributes={
							{
								ct: {
									post_name: props.attributes.ct,
								},
							}
						}
					/>

			),
		];
	},
	save: ( props ) => {
		let ct = props.attributes.ct || '';
		const shortcodeStart = '[wpv-post-body',
			shortcodeEnd = ']';

		if ( ! ct.length ) {
			return null;
		}

		ct = ' view_template="' + ct + '"';

		return <RawHTML>{ shortcodeStart + ct + shortcodeEnd }</RawHTML>;
	},
};

registerBlockType( name, settings );
