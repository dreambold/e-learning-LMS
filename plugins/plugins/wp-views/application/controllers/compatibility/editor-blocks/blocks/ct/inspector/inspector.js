/**
 * The Content Template block inspector component.
 *
 * An "Inspector" component is created that is used inside the Toolset Content Template block to handle all the functionality related
 * to the controls on the Gutenberg editor sidebar.
 *
 * @since  2.6.0
 */

/**
 * Block dependencies
 */
import CTSelect from './ct-select';

/**
 * Internal block libraries
 */
const {
	__,
} = wp.i18n;

const {
	Component,
} = wp.element;

const {
	InspectorControls,
} = wp.editor;

const {
	PanelBody,
} = wp.components;

export default class Inspector extends Component {
	render() {
		const {
			attributes,
			onChangeCT,
		} = this.props;

		const {
			ct,
		} = attributes;

		return (
			<InspectorControls>
				<PanelBody title={ __( 'Content Template', 'wpv-views' ) }>
					<CTSelect
						attributes={
							{
								ct: ct,
							}
						}
						className="components-select-control__input"
						onChangeCT={ onChangeCT }
					/>
				</PanelBody>
			</InspectorControls>
		);
	}
}
