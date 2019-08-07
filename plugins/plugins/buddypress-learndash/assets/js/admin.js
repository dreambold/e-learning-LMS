if ( typeof jq == "undefined" ) {
	var jq = jQuery;
}

/**
 * Course enrollment class
 * @param ld_vars
 * @returns {{init: init}}
 * @constructor
 */
var BuddyPress_Learndash_Group_Edit = function( ld_vars ) {

	var $enrollmentNotice  = jq('#enrollment-notice'),
		startPos =  0,
		endPos = 10,
		noticeMessage = 'BuddyPress for Learndash enrolling users to the course buddypress groups. This can take a while if you have many students(members). Do not navigate away from this page until this is done.';

	function init() {
		if ( ld_vars.courses != null && ld_vars.users!= null  ) {

			// Add enrollment progress notice in class editor
			$enrollmentNotice.toggleClass('hidden');

			// Add enrollment progress notice in gutenberg editor
			if ( 'undefined' !== typeof wp.data ) {
				wp.data.dispatch( 'core/editor' ).createInfoNotice( noticeMessage, {id : 'ldEnrollment' } );
			}
			user_enrollment(ld_vars.users.slice(startPos, endPos));
		}
	}

	function user_enrollment( users ) {

		if ( 0 === users.length ) {

			// Add enrollment progress notice in class editor
			$enrollmentNotice.toggleClass('hidden');

			// Remove enrollment progress notice in gutenberg editor
			if ( 'undefined' !== typeof wp.data ) {
				wp.data.dispatch( 'core/editor' ).removeNotice( 'ldEnrollment' );
			}
			return 0;
		}

		jq.ajax({
			type: 'POST',
			url: ajaxurl,
			data: { action: "mass_group_join", users: users, courses: ld_vars.courses },
			success: function( response ) {
				if ( response !== Object( response ) || ( typeof response.success === "undefined" && typeof response.error === "undefined" ) ) {
					response = new Object;
					response.success = false;
					return;
				}

				startPos = endPos;
				endPos = endPos + 10;
				user_enrollment( ld_vars.users.slice( startPos, endPos ) );
			}
		});
	}

	return {
		init: init
	}
};

jq(function($) {

	// Run enrollment after course update from classic editor
	if ( buddypress_learndash_vars.proceed_enrollment ) {
		new BuddyPress_Learndash_Group_Edit(buddypress_learndash_vars).init();
	}

	// Run enrollment after course update from gutenberg editor
	jq( document ).ajaxComplete(function( event, xhr, settings ) {

		if ( settings.url.indexOf('/sfwd-courses/') === -1
			&& settings.url.indexOf('/groups/') === -1  ) {
			return false;
		}

		// The condition runs only on the Course Edit Screen:
		// No need to go further if, course has not been attached with buddypress group
		if ( settings.url.indexOf('/sfwd-courses/') !== -1
			&& document.getElementById('bp-course-group').value === '-1' ) {
			return false;
		}

		jq.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: "get_enrollment_data",
				screen_id: buddypress_learndash_vars.screen_id,
				post_ID: $('#post_ID')[0].value
			},
			success: function (response) {
				var ld_vars = response.data;
				new BuddyPress_Learndash_Group_Edit(ld_vars).init();
			}
		});
	});
});