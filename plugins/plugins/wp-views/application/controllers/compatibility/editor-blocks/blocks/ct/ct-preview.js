/**
 * The Content Template block preview component.
 *
 * A "CTPreview" component is created that is used inside the Content Template block to handle the previewing of the
 * selected Content Template.
 *
 * @since  2.6.0
 */

import classnames from 'classnames';

const {
	__,
} = wp.i18n;

const {
	Component,
} = wp.element;

const {
	Spinner,
} = wp.components;

const {
	toolset_ct_block_strings: i18n,
} = window;

export default class CTPreview extends Component {
	// constructor( props ) {
	constructor() {
		super( ...arguments );
		this.getCTInfo = this.getCTInfo.bind( this );
		this.state = {
			fetching: false,
			error: false,
			errorMessage: '',
		};
	}

	render() {
		const {
			fetching,
			error,
			errorMessage,
			ctPostContent,
		} = this.state;

		if ( fetching ) {
			return <div key="fetching" className={ classnames( this.props.className ) } >
				<div key="loading" className={ classnames( 'wp-block-embed is-loading' ) }>
					<Spinner />
					<p>{ __( 'Loading the Content Template Preview…', 'wpv-views' ) }</p>
				</div>
			</div>;
		}

		if ( error ) {
			return <div key="error" className={ classnames( this.props.className ) } >
				<div className={ classnames( 'wpv-ct-info-warning' ) }>
					{ errorMessage }
				</div>

			</div>;
		}

		return (
			<div className={ classnames( this.props.className ) } >
				<div dangerouslySetInnerHTML={ { __html: ctPostContent } }></div>
			</div>
		);
	}

	static getDerivedStateFromProps( nextProps, prevState ) {
		if (
			nextProps.attributes.ct &&
			'undefined' === typeof prevState.ctPostContent &&
			prevState.fetching === false
		) {
			// If the View is already there, we're loading a saved block, so we need to render
			// a different thing, which is why this doesn't use 'fetching', as that
			// is for when the user is putting in a new url on the placeholder form
			prevState.fetching = true;
		}

		return prevState;
	}

	componentDidMount() {
		this.getCTInfo();
	}

	componentDidUpdate( prevProps ) {
		if ( prevProps.attributes.ct.post_name !== this.props.attributes.ct.post_name ) {
			this.setState( {
				fetching: true,
				error: false,
				errorMessage: '',
			} );
			this.getCTInfo( this.props.attributes.ct.post_name );
		}
	}

	getCTInfo( ctPostName ) {
		const data = new window.FormData();
		data.append( 'action', i18n.actionName );
		data.append( 'wpnonce', i18n.wpnonce );
		data.append( 'ct_post_name', 'undefined' === typeof ctPostName ? this.props.attributes.ct.post_name : ctPostName );

		window.fetch( window.ajaxurl, {
			method: 'POST',
			body: data,
			credentials: 'same-origin',
		} ).then( res => res.json() )
			.then( response => {
				const newState = {};
				if (
					0 !== response &&
					response.success &&
					'undefined' !== typeof response.data
				) {
					if ( '' === response.data ) {
						newState.error = true;
						newState.errorMessage = __( 'The selected Content Template has an empty "Template" section.', 'wpv-views' );
					} else {
						newState.ctPostContent = response.data;
					}
				} else {
					let message = '';
					if (
						'undefined' !== typeof response.data &&
						'undefined' !== typeof response.data.message ) {
						message = response.data.message;
					} else {
						message = __( 'An error occurred while trying to get the Content Template information.', 'wpv-views' );
					}
					newState.error = true;
					newState.errorMessage = message;
				}

				newState.fetching = false;

				this.setState( newState );
			} );
	}
}
