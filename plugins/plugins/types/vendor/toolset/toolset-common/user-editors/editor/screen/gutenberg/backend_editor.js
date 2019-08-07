/**
 * Backend script to be used when editing Content Templates using Gutenberg.
 *
 * @summary Content Template editor manager for Gutenberg.
 *
 * @since 2.6.9
 * @requires jquery.js
 * @requires underscore.js
 */

/* global toolset_user_editors_native */

var ToolsetCommon			= ToolsetCommon || {};
ToolsetCommon.UserEditor	= ToolsetCommon.UserEditor || {};

ToolsetCommon.UserEditor.GutenbergEditor = function( $ ) {

	var self = this;

	self.i18n = window.toolset_user_editors_gutenberg_script_i18n;

	self.init = function() {
		window.wp.data.dispatch( 'core/notices' ).createInfoNotice (
			self.i18n.doneEditingNoticeText,
			{
				isDismissible: false,
				actions: [
					{
						label: self.i18n.doneEditingNoticeActionText,
						url: self.i18n.doneEditingNoticeActionUrl
					}
				]
			}
		);
	};

	self.init();
};

jQuery( document ).ready( function( $ ) {
	ToolsetCommon.UserEditor.GutenbergEditorInstance = new ToolsetCommon.UserEditor.GutenbergEditor( $ );
});
