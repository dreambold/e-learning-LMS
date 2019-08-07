/**
 * Handles the creation and the behavior of the Toolset View block.
 *
 * @since  2.6.0
 */

/**
 * Block dependencies
 */
import icon from './icon';
import Inspector from './inspector/inspector';
import ViewSelect from './inspector/view-select';
import ViewPreview from './view-preview';
import classnames from 'classnames';
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
	toolset_view_block_strings: i18n,
} = window;

if ( i18n.locale ) {
	setLocaleData( i18n.locale, 'wpv-views' );
}

const name = i18n.blockName;

const settings = {
	// translators: The name of the View block that will appear in the editor's block inserter.
	title: __( 'View', 'wpv-views' ),
	description: __( 'Add a Post, User, or Taxonomy View to the editor.', 'wpv-views' ),
	category: i18n.blockCategory,
	icon: icon.blockIcon,
	keywords: [
		__( 'Toolset', 'wpv-views' ),
		__( 'View', 'wpv-views' ),
		__( 'Shortcode', 'wpv-views' ),
	],

	edit: props => {
		const onChangeLimit = ( value ) => {
			props.setAttributes( { limit: value } );
		};

		const onChangeOffset = value => {
			props.setAttributes( { offset: value } );
		};

		const onChangeOrderby = value => {
			props.setAttributes( { orderby: value } );
		};

		const onChangeOrder = value => {
			props.setAttributes( { order: value } );
		};

		const onChangeSecondaryOrderby = value => {
			props.setAttributes( { secondaryOrderby: value } );
			if ( '' === value ) {
				onChangeSecondaryOrder( '' );
			}
		};

		const onChangeSecondaryOrder = value => {
			props.setAttributes( { secondaryOrder: value } );
		};

		const onChangeView = ( event ) => {
			props.setAttributes( { view: event.target.value } );
		};

		const onChangeFormDisplay = ( value ) => {
			props.setAttributes( { formDisplay: value } );
		};

		const onChangeFormOnlyDisplay = ( value ) => {
			props.setAttributes( { formOnlyDisplay: value } );
		};

		const onChangeotherPage = value => {
			props.setAttributes( { otherPage: value } );
		};

		const onChangeQueryFilters = ( value, filterType ) => {
			const newQueryFilters = Object.assign( {}, props.attributes.queryFilters );
			newQueryFilters[ filterType ] = value;
			props.setAttributes( { queryFilters: newQueryFilters } );
		};

		const onPreviewStateUpdate = ( state ) => {
			props.setAttributes( { hasCustomSearch: state.hasCustomSearch } );
			props.setAttributes( { hasSubmit: state.hasSubmit } );
			if ( JSON.stringify( props.attributes.hasExtraAttributes ) !== JSON.stringify( state.hasExtraAttributes ) ) {
				props.setAttributes( { hasExtraAttributes: state.hasExtraAttributes } );
				if (
					'undefined' !== typeof state.hasExtraAttributes &&
					state.hasExtraAttributes.length <= 0 ) {
					props.setAttributes( { queryFilters: {} } );
				}
			}
		};

		const {
			posts,
			taxonomy,
			users,
		} = i18n.publishedViews;

		return [
			!! (
				props.focus ||
				props.isSelected
			) && (
				<Inspector
					key="wpv-gutenberg-view-block-render-inspector"
					className={ classnames( 'wp-block-toolset-view-inspector' ) }
					attributes={
						{
							view: props.attributes.view,
							hasCustomSearch: props.attributes.hasCustomSearch,
							hasSubmit: props.attributes.hasSubmit,
							hasExtraAttributes: props.attributes.hasExtraAttributes,
							formDisplay: props.attributes.formDisplay,
							formOnlyDisplay: props.attributes.formOnlyDisplay,
							otherPage: props.attributes.otherPage,
							limit: props.attributes.limit,
							offset: props.attributes.offset,
							orderby: props.attributes.orderby,
							order: props.attributes.order,
							secondaryOrderby: props.attributes.secondaryOrderby,
							secondaryOrder: props.attributes.secondaryOrder,
							queryFilters: props.attributes.queryFilters,
						}
					}
					onChangeView={ onChangeView }
					onChangeFormDisplay={ onChangeFormDisplay }
					onChangeFormOnlyDisplay={ onChangeFormOnlyDisplay }
					onChangeLimit={ onChangeLimit }
					onChangeOffset={ onChangeOffset }
					onChangeOrderby={ onChangeOrderby }
					onChangeOrder={ onChangeOrder }
					onChangeSecondaryOrderby={ onChangeSecondaryOrderby }
					onChangeSecondaryOrder={ onChangeSecondaryOrder }
					onChangeotherPage={ onChangeotherPage }
					onChangeQueryFilters={ onChangeQueryFilters }
				/>
			),
			( '' === props.attributes.view ?
				<Placeholder
					key="view-block-placeholder"
					className={ classnames( 'wp-block-toolset-view' ) }
				>
					<div className="wp-block-toolset-view-placeholder">
						{ icon.blockPlaceholder }
						<h2>{ __( 'Toolset View', 'wpv-views' ) }</h2>
						<ViewSelect
							attributes={
								{
									posts: posts,
									taxonomy: taxonomy,
									users: users,
									view: props.attributes.view,
								}
							}
							className={ classnames( 'components-select-control__input' ) }
							onChangeView={ onChangeView }
						/>
					</div>
				</Placeholder> :
				<ViewPreview
					key="toolset-view-gutenberg-block-preview"
					className={ classnames( props.className, 'wp-block-toolset-view-preview' ) }
					attributes={
						{
							view: {
								ID: isNaN( props.attributes.view ) ? JSON.parse( props.attributes.view ).ID : props.attributes.view,
							},
							hasCustomSearch: props.attributes.hasCustomSearch,
							formDisplay: props.attributes.formDisplay,
							limit: props.attributes.limit,
							offset: props.attributes.offset,
							orderby: props.attributes.orderby,
							order: props.attributes.order,
							secondaryOrderby: props.attributes.secondaryOrderby,
							secondaryOrder: props.attributes.secondaryOrder,
						}
					}
					onPreviewStateUpdate={ onPreviewStateUpdate }
				/>
			),
		];
	},
	save: ( props ) => {
		let view = isNaN( props.attributes.view ) ? JSON.parse( props.attributes.view ).post_name || '' : props.attributes.view,
			shortcodeStart = '[wpv-view',
			limit = '',
			offset = '',
			orderby = '',
			order = '',
			secondaryOrderby = '',
			secondaryOrder = '',
			target = '',
			queryFilters = '',
			viewDisplay = '';

		const shortcodeEnd = ']';

		// If there's no URL, don't save any inline HTML.
		if ( '' === view ) {
			return null;
		}

		if ( isNaN( view ) ) {
			view = ' name="' + view + '"';
		} else {
			view = ' id="' + view + '"';
		}

		if ( -1 < parseInt( props.attributes.limit ) ) {
			limit = ' limit="' + props.attributes.limit + '"';
		}

		if ( 0 < parseInt( props.attributes.offset ) ) {
			offset = ' offset="' + props.attributes.offset + '"';
		}

		if ( '' !== props.attributes.orderby ) {
			orderby = ' orderby="' + props.attributes.orderby + '"';
		}

		if ( '' !== props.attributes.order ) {
			order = ' order="' + props.attributes.order + '"';
		}

		if ( '' !== props.attributes.secondaryOrderby ) {
			secondaryOrderby = ' orderby_second="' + props.attributes.secondaryOrderby + '"';
		}

		if ( '' !== props.attributes.secondaryOrder ) {
			secondaryOrder = ' order_second="' + props.attributes.secondaryOrder + '"';
		}

		if (
			props.attributes.hasCustomSearch &&
			'form' === props.attributes.formDisplay
		) {
			shortcodeStart = '[wpv-form-view';
			if ( 'samePage' === props.attributes.formOnlyDisplay ) {
				target = ' target_id="self"';
			} else if (
				'otherPage' === props.attributes.formOnlyDisplay &&
				props.attributes.hasSubmit &&
				'' !== props.attributes.otherPage.value
			) {
				target = ' target_id="' + props.attributes.otherPage.value + '"';
			}
		}

		if (
			props.attributes.hasCustomSearch &&
			'results' === props.attributes.formDisplay
		) {
			target = '';
			viewDisplay = ' view_display="layout"';
		}

		props.attributes.hasExtraAttributes.forEach(
			function( item ) {
				if ( 0 < Object.keys( props.attributes.queryFilters ).length ) {
					queryFilters += ' ' + item.attribute + '="' + props.attributes.queryFilters[ item[ 'filter_type' ] ] + '"';
				}
			}
		);

		return <RawHTML>{ shortcodeStart + view + limit + offset + orderby + order + secondaryOrderby + secondaryOrder + target + viewDisplay + queryFilters + shortcodeEnd }</RawHTML>;
	},
};

registerBlockType( name, settings );
