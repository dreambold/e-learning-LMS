/**
 * API and helper functions for media fields management.
 *
 * @package Toolset
 * @since 3.3
 */

var Toolset = Toolset || {};

Toolset.Common = Toolset.Common || {};

Toolset.Common.MediaField = function( $ ) {

    this.i18n = toolset_media_field_i18n;

    this.mediaInstances = {};

    this.CONST = {
        SINGLE_CONTAINER_SELECTOR: '.js-wpt-field-items',
        REPEATING_CONTAINER_SELECTOR: '.js-wpt-field-item',
        INPUT_SELECTOR: '.js-toolset-media-field-trigger'
    };

};

/**
 * Init constants for selectors.
 *
 * Can be overriden by prototype implementations, for specific selectors.
 *
 * @since 3.3
 */
Toolset.Common.MediaField.prototype.initConstants = function() {
    return this;
};

/**
 * Init validation methods.
 *
 * Can be overriden by prototype implementations, for specific methods.
 *
 * @since 3.3
 */
Toolset.Common.MediaField.prototype.initValidationMethods = function() {
    return this;
};

/**
 * Init events.
 *
 * @since 3.3
 */
Toolset.Common.MediaField.prototype.initEvents = function() {
    var currentInstance = this;

    jQuery( document ).on( 'click', currentInstance.CONST.INPUT_SELECTOR, function( e ) {
        e.preventDefault();
        currentInstance.manageInputSelectorClick( jQuery( this ) );
    });

    return currentInstance;
};

/**
 * Input selector click: open the right media dialog.
 *
 * @since 3.3
 */
Toolset.Common.MediaField.prototype.manageInputSelectorClick = function( $mediaSelector ) {
    var currentInstance = this;

    var metaData = $mediaSelector.data( 'meta' );

    metaData = _.defaults( metaData, {
        metakey: '',
        parent: 0,
        type: '',
        multiple: false
    });

    // Make sure the post parent ID is an integer, force zero otherwise
    metaData.parent = parseInt( metaData.parent) || 0;

    // Maybe set the parent post to attach media;
    // backend does not need it as WP manages it by itself.
    metaData.parent = currentInstance.setParentId( metaData.parent, $mediaSelector );

    // Destroy media instances binded to an unknown parent:
    // needed for specific cases where this could lead to wrong fields caching
    // as containers might be wrongly set, when using templates for fields groups,
    // like in the Types dialogs to add a new related post,
    // or in frontend user forms.
    if (
        0 == metaData.parent
        && _.has( currentInstance.mediaInstances, metaData.parent )
    ) {
        currentInstance.mediaInstances = _.omit( currentInstance.mediaInstances, metaData.parent );
    }

    if ( ! _.has( currentInstance.mediaInstances, metaData.parent ) ) {
        currentInstance.mediaInstances[ metaData.parent ] = {};
    }

    // If the frame already exists, re-open it.
    if ( _.has( currentInstance.mediaInstances[ metaData.parent ], metaData.metakey ) ) {
        currentInstance.mediaInstances[ metaData.parent ][ metaData.metakey ].open();
        return;
    }

    var $innerContainer = $mediaSelector.closest( currentInstance.CONST.REPEATING_CONTAINER_SELECTOR ),
        $outerContainer = $mediaSelector.closest( currentInstance.CONST.SINGLE_CONTAINER_SELECTOR );

    if ( $innerContainer.length < 1 ) {
        $innerContainer = $outerContainer;
    }

    var mediaSettings = {
        // TODO Title should be the field title
        title: currentInstance.i18n.dialog.title,
        button: {
            // TODO Buton label might change per field type and multiple status
            text: currentInstance.i18n.dialog.button
        },
        className: 'media-frame js-toolset-forms-media-frame',
        frame: 'select',
        multiple: metaData.multiple,
        library: {
            'toolset_media_management_nonce': currentInstance.i18n.dialog.nonce,
            'toolset_media_management_filter': {
                // TODO support filtering by current author only
                //author: true
            }
        }
    };

    if ( _.contains( [ 'audio', 'image', 'video' ], metaData.type ) ) {
        mediaSettings.library.type = metaData.type;
    }

    currentInstance.mediaInstances[ metaData.parent ][ metaData.metakey ] = wp.media( mediaSettings );

    // As we include a custom query parameter, make sure the query panel
    // is updated when uploading a file.
    // See: https://core.trac.wordpress.org/ticket/34465
    currentInstance.mediaInstances[ metaData.parent ][ metaData.metakey ].states.get( 'library' ).get( 'library' ).observe( wp.Uploader.queue );

    // Set the upload custom nonce value, on dialog open, to ensure that
    // the uploder has been defined.
    // Note that there is no way of limiting upload per file type.
    currentInstance.mediaInstances[ metaData.parent ][ metaData.metakey ].on( 'open', function() {
        currentInstance.mediaInstances[ metaData.parent ][ metaData.metakey ].uploader.uploader.param( 'toolset_media_management_nonce', currentInstance.i18n.dialog.nonce );
    });

    currentInstance.mediaInstances[ metaData.parent ][ metaData.metakey ].on( 'select', function() {
        // Watch changes in wp-includes/js/media-editor.js
        var selectedMedia = currentInstance.mediaInstances[ metaData.parent ][ metaData.metakey ]
            .state()
            .get( 'selection' )
            .toJSON();

        // Set the value of the relevant input after getting at least one selected media item.
        var firstMediaItem = _.first( selectedMedia );

        /*
        // Repeat this for the repeating field below...
        // Set field value and update preview
        */
        currentInstance.setFieldValue( $innerContainer, firstMediaItem );
        currentInstance.manageFieldPreview( $innerContainer, firstMediaItem );

        // If more than one item is selected, create instances for all but first,
        // append them one after the other, and populate their values.
        if ( _.size( selectedMedia ) > 1 ) {
            var selectedMediaRest = _.rest( selectedMedia ),
                newInstancesNumber = _.size( selectedMediaRest ),
                $newInstancesTrigger = $outerContainer.find( '.js-wpt-repadd' ),
                $insertAfter = $innerContainer;
            _.times( newInstancesNumber, function( instanceIndex ) {
                var currentMediaItem = _.first( selectedMediaRest );

                $newInstancesTrigger.trigger( 'click', [ $insertAfter ] );
                var $currentInstance = $insertAfter.next( currentInstance.CONST.REPEATING_CONTAINER_SELECTOR );

                currentInstance.setFieldValue( $currentInstance, currentMediaItem );
                currentInstance.manageFieldPreview( $currentInstance, currentMediaItem );

                selectedMediaRest = _.rest( selectedMediaRest );
                $insertAfter = $currentInstance
            });
        }

    });

    currentInstance.mediaInstances[ metaData.parent ][ metaData.metakey ].open();

};

/**
 * Set the post ID where media will be attached, if needed.
 *
 * @since 3.3
 */
Toolset.Common.MediaField.prototype.setParentId = function( parentId ) {
    return parentId;
};

/**
 * Set the field value.
 *
 * @since 3.3
 */
Toolset.Common.MediaField.prototype.setFieldValue = function( $instance, mediaItem ) {
    return;
};

/**
 * Update the field preview.
 *
 * @since 3.3
 */
Toolset.Common.MediaField.prototype.manageFieldPreview = function( $instance, mediaItem ) {
    return;
};

/**
 * Initialize this prototype.
 *
 * @since 3.3
 */
Toolset.Common.MediaField.prototype.init = function() {
    this.initConstants()
        .initValidationMethods()
        .initEvents();
};
