<?php
/**
 * LearnDash Settings Loader.
 *
 * @package LearnDash
 * @subpackage Settings
 */

if ( ! defined( 'LEARNDASH_SETTINGS_SECTION_TYPE' ) ) {
	define( 'LEARNDASH_SETTINGS_SECTION_TYPE', 'metabox' );
}

require_once 'class-ld-settings-fields.php';
require_once 'class-ld-settings-pages.php';
require_once 'class-ld-settings-sections.php';
require_once 'class-ld-settings-metaboxes.php';

require_once 'settings-fields/settings-fields-loader.php';
require_once 'settings-pages/settings-pages-loader.php';
require_once 'settings-sections/settings-sections-loader.php';
