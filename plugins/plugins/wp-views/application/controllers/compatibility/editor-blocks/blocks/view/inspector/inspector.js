/**
 * The View block inspector component.
 *
 * An "Inspector" component is created that is used inside the Toolset View block to handle all the functionality related
 * to the controls on the Gutenberg editor sidebar.
 *
 * @since  2.6.0
 */

/**
 * Block dependencies
 */
import ViewSelect from './view-select';
import QueryFilters from './query-filters';
import interpolateComponents from 'interpolate-components';
import Select2 from 'ToolsetBlocks/blocks/common/select2';

/**
 * Internal block libraries
 */
const {
	__,
	sprintf,
} = wp.i18n;

const {
	Component,
} = wp.element;

const {
	InspectorControls,
} = wp.editor;

const {
	BaseControl,
	PanelBody,
	PanelRow,
	Notice,
	TextControl,
	RangeControl,
	SelectControl,
	RadioControl,
} = wp.components;

const {
	toolset_view_block_strings: i18n,
} = window;

export default class Inspector extends Component {
	render() {
		const {
			attributes,
			className,
			onChangeView,
			onChangeLimit,
			onChangeOffset,
			onChangeOrderby,
			onChangeOrder,
			onChangeSecondaryOrderby,
			onChangeSecondaryOrder,
			onChangeFormDisplay,
			onChangeFormOnlyDisplay,
			onChangeotherPage,
			onChangeQueryFilters,
		} = this.props;

		const {
			view,
			hasCustomSearch,
			hasSubmit,
			hasExtraAttributes,
			limit,
			offset,
			orderby,
			order,
			secondaryOrderby,
			secondaryOrder,
			formDisplay,
			formOnlyDisplay,
			otherPage,
			queryFilters,
		} = attributes;

		const formOnlyDisplayOptions = hasSubmit ?
			[
				{ value: 'samePage', label: __( 'In other place on this same page', 'wpv-views' ) },
				{ value: 'otherPage', label: __( 'On another page', 'wpv-views' ) },
			] :
			[
				{ value: 'samePage', label: __( 'In other place on this same page', 'wpv-views' ) },
			];

		const {
			posts,
			taxonomy,
			users,
		} = i18n.publishedViews;

		return (
			<InspectorControls>
				<div className={ className }>
					<PanelBody title={ __( 'View', 'wpv-views' ) }>
						<ViewSelect
							attributes={
								{
									posts: posts,
									taxonomy: taxonomy,
									users: users,
									view: view,
								}
							}
							className="components-select-control__input"
							onChangeView={ onChangeView }
						/>
						{
							(
								'undefined' !== typeof posts &&
								'undefined' !== typeof taxonomy &&
								'undefined' !== typeof users
							) &&
							(
								posts.length > 0 ||
								taxonomy.length > 0 ||
								users.length > 0
							) &&
							'' !== view ?
								( [
									hasCustomSearch ?
										<PanelBody
											title={ __( 'Custom Search Settings', 'wpv-views' ) }
											key="custom-search-settings-panel"
										>
											<PanelRow>
												<RadioControl
													label={ __( 'What do you want to include here?', 'wpv-views' ) }
													selected={ formDisplay }
													onChange={ onChangeFormDisplay }
													help={
														__( 'The first option will display the full View.', 'wpv-views' ) +
														' ' +
														__( 'The second option will display just the form, you can then select where to display the results.', 'wpv-views' ) +
														' ' +
														__( 'Finally, the third option will display just the results, you need to add the form elsewhere targeting this page.', 'wpv-views' )
													}
													options={
														[
															{ value: 'full', label: __( 'Both the search form and results', 'wpv-views' ) },
															{ value: 'form', label: __( 'Only the search form', 'wpv-views' ) },
															{ value: 'results', label: __( 'Only the search results', 'wpv-views' ) },
														]
													}
												/>
											</PanelRow>

											{
												! hasSubmit &&
												'form' === formDisplay ?
													(
														<PanelRow>
															<Notice
																status="warning"
																isDismissible={ false }
															>
																<div className="wpv-has-submit-notice">
																	<p>
																		{ __( 'The form in this View does not have a submit button, so you can only display the results on this same page.', 'wpv-views' ) }
																	</p>
																</div>
															</Notice>
														</PanelRow>
													) : null
											}

											{
												'form' === formDisplay ? (
													<PanelRow>
														<RadioControl
															label={ __( 'Where do you want to display the results?', 'wpv-views' ) }
															selected={ formOnlyDisplay }
															onChange={ onChangeFormOnlyDisplay }
															options={ formOnlyDisplayOptions }
														/>
													</PanelRow>
												) : null
											}

											{
												'results' === formDisplay ? (
													<PanelRow>
														<Notice
															status="warning"
															isDismissible={ false }
														>
															<div className="results-notice">
																<p>
																	{
																		interpolateComponents( {
																			mixedString: sprintf(
																				'You are only displaying the %s in this block.',
																				'{{strong}}' + __( 'search results', 'wpv-views' ) + '{{/strong}}'
																			) +
																			' ' +
																			sprintf(
																				'A custom search should have the %s and %s.',
																				'{{strong}}' + __( 'search results', 'wpv-views' ) + '{{/strong}}',
																				'{{strong}}' + __( 'search form', 'wpv-views' ) + '{{/strong}}'
																			) +
																			' ' +
																			sprintf(
																				'To display the %s you need to:',
																				'{{strong}}' + __( 'search form', 'wpv-views' ) + '{{/strong}}'
																			),
																			components: { strong: <strong /> },
																		} )
																	}
																</p>
																<ol>
																	<li>{ __( 'Create a different View block and display this View.', 'wpv-views' ) }</li>
																	<li>
																		{
																			interpolateComponents( {
																				mixedString: sprintf( 'Choose to display the %s.', '{{strong}}' + __( 'search form', 'wpv-views' ) + '{{/strong}}' ),
																				components: { strong: <strong /> },
																			} )
																		}
																	</li>
																</ol>
															</div>
														</Notice>
													</PanelRow>
												) : null
											}

											{
												'form' === formDisplay && 'otherPage' === formOnlyDisplay ? (
													<PanelRow>
														{
															// @todo Switch the custom endpoint with the native one, once it's available.
															// The endpoint used on the autocomplete component below to get the post according to a
															// search keyword, needs to be changed with the native WP REST API endpoint (as soon as
															// it's available).
															// https://github.com/WordPress/gutenberg/issues/2084
															// https://core.trac.wordpress.org/ticket/39965
														}
														<BaseControl label={ __( 'Page', 'wpv-views' ) } key="page-select" className="another-page-select">
															<Select2
																onChange={ onChangeotherPage }
																restInfo={
																	{
																		base: '/toolset/v2/search-posts',
																		args: {
																			search: '%s',
																		},
																	}
																}
																value={ otherPage }
															/>
														</BaseControl>
													</PanelRow>
												) : null
											}
										</PanelBody> :
										null,
									'undefined' !== typeof hasExtraAttributes &&
									hasExtraAttributes.length > 0 ?
										<PanelBody
											title={ __( 'Query filters', 'wpv-views' ) }
											key="query-filters-settings-panel"
										>
											<QueryFilters
												attributes={
													{
														hasExtraAttributes: hasExtraAttributes,
														queryFilters: queryFilters,
													}
												}
												onChangeQueryFilters={ onChangeQueryFilters }
											/>
										</PanelBody> :
										null,
									<PanelBody
										title={ __( 'Override View basic settings', 'wpv-views' ) }
										key="view-settings-override-panel"
									>
										{ /**
										 * Limit View setting
										 **/ }
										<PanelRow>
											<RangeControl
												label={ __( 'Limit', 'wpv-views' ) }
												value={ limit }
												onChange={ onChangeLimit }
												min={ -1 }
												max={ 999 }
												help={ __( 'Get only some results. -1 means no limit.', 'wpv-views' ) }
												// allowReset={ true }
											/>
										</PanelRow>

										<PanelRow>
											<RangeControl
												label={ __( 'Offset', 'wpv-views' ) }
												value={ offset }
												onChange={ onChangeOffset }
												min={ 0 }
												max={ 999 }
												help={ __( 'Skip some results. 0 means skip nothing', 'wpv-views' ) }
												// allowReset={ true }
											/>
										</PanelRow>

										<PanelRow>
											<TextControl
												label={ __( 'Order by', 'wpv-views' ) }
												value={ orderby }
												onChange={ onChangeOrderby }
												help={
													__( 'Change how the results will be ordered.', 'wpv-views' ) +
													' ' +
													__( 'You can sort by a custom field simply using the value field-xxx where xxx is the custom field slug.', 'wpv-views' )
												}
												placeholder={ __( 'ID, date, author, title, post_type or field-slug', 'wpv-views' ) }
											/>
										</PanelRow>

										<PanelRow>
											<SelectControl
												label={ __( 'Order', 'wpv-views' ) }
												help={ __( 'Change the order of the results.', 'wpv-views' ) }
												onChange={ onChangeOrder }
												value={ order }
												options={ [
													{ value: '', label: __( 'Default setting', 'wpv-views' ) },
													{ value: 'asc', label: __( 'Ascending', 'wpv-views' ) },
													{ value: 'desc', label: __( 'Descending', 'wpv-views' ) },
												] }
											/>
										</PanelRow>
									</PanelBody>,

									<PanelBody
										title={ __( 'Secondary sorting', 'wpv-views' ) }
										initialOpen={ '' !== secondaryOrderby }
										key="secondary-sorting-panel"
									>
										<PanelRow>
											<SelectControl
												label={ __( 'Secondary Order by', 'wpv-views' ) }
												help={ __( 'Change how the results that share the same value on the orderby setting will be ordered.', 'wpv-views' ) }
												onChange={ onChangeSecondaryOrderby }
												value={ secondaryOrderby }
												options={ [
													{ value: '', label: __( 'No secondary sorting', 'wpv-views' ) },
													{ value: 'post_date', label: __( 'Post date', 'wpv-views' ) },
													{ value: 'post_title', label: __( 'Post title', 'wpv-views' ) },
													{ value: 'ID', label: __( 'ID', 'wpv-views' ) },
													{ value: 'post_author', label: __( 'Post author', 'wpv-views' ) },
													{ value: 'post_type', label: __( 'Post type', 'wpv-views' ) },
												] }
											/>
										</PanelRow>

										<PanelRow>
											<SelectControl
												label={ __( 'Secondary Order', 'wpv-views' ) }
												help={ __( 'Change the secondary order of the results.', 'wpv-views' ) }
												onChange={ onChangeSecondaryOrder }
												value={ secondaryOrder }
												options={ [
													{ value: '', label: __( 'Default setting', 'wpv-views' ) },
													{ value: 'asc', label: __( 'Ascending', 'wpv-views' ) },
													{ value: 'desc', label: __( 'Descending', 'wpv-views' ) },
												] }
												disabled={ '' === secondaryOrderby ? 'disabled' : null }
											/>
										</PanelRow>
									</PanelBody>,
								] ) : null
						}
					</PanelBody>
				</div>
			</InspectorControls>
		);
	}
}
