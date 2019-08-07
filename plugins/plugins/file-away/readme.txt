=== File Away ===
Contributors: thomstark
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2JHFN4UF23ARG
Tags: files, attachments, upload, statistics, tables, directory, monetize, lightbox, audio, video, file manager, encryption
Requires at least: 3.7
Tested up to: 5.1.1
Requires PHP: 5.4
Stable tag: 3.9.9.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Upload, manage, and display files from your server directories or page attachments in stylized lists or sortable data tables.

== Description ==
Upload, manage, and display files from your server directories or page attachments in stylized lists or sortable data tables. And much, much more. 

== Changelog ==
= 3.9.9.0.1 =
* Fixed download error when stats and encryption are enabled, was caused by previous sanitization update 3.9.7.
= 3.9.8.9 =
* Bugfix for intermittent bug with filename encryption
= 3.9.8.8 =
* Added show_wp_thumbs attribute to [fileaway] shortcode. Omit for default behavior (hide). show_wp_thumbs="true" to show them.
* Modified bulk action select all behavior to only select files on the current page if the file table is paginated
= 3.9.8.7 =
* Fixed bug with location nonce verification for setups where Site URL and WP Url are different
= 3.9.8.6 =
* Fixed Flightbox navigation which was broken by an earlier update
* Removed deprecated encryption method
= 3.9.8.5 =
* Forgot to include a file update for Windows compat in the last update. This time it's for real.
* Made alt pathinfo method the only pathinfo. Should fix most issues with multibyte filenames (e.g., Chinese, Russian, etc.)
= 3.9.8.4 =
* Full compat with Windows/iis/xampp
* Fixed maxsize bug with fileup shortcode
= 3.9.8.3 =
* Important bugfix for timezone handling. 
* Improved error handling for ajax functions: more descriptive for troubleshooting purposes.
* Increased speed of animations in manager mode. 
= 3.9.8.2 =
* Added the parentlabel attribute to the fileaway shortcode, allowing you to specify a pseudonym for the topmost directory in a Directory Tree Nav or Manager Mode table
= 3.9.8.1 =
* Fixed bug with symlinks validator that only validated one subdirectory deep
= 3.9.8 =
* Added option to allow symlinks in file paths. Disabled by default. 
= 3.9.7.9 =
* Update to allow symlinks to escape new path validation checks unscathed
= 3.9.7.8 =
* Fixed bug for PHP < 5.5 which prevents plugin activation
= 3.9.7.7 =
* Fixed issue with new path validation's incompatibility with wp installs in a subdirectory at different domain than front-end site
= 3.9.7.6 =
* Fixed dynamic paths not working
= 3.9.7.5 =
* important patch
= 3.9.7.4 =
* prettify set to "off" now applies to directory names as well
* one bugfix
* additional security improvements
= 3.9.7.3 =
* Timezone handling improvements
* Additional validation checks added
= 3.9.7.2 =
* Added filename sanitizer
= 3.9.7.1 =
* Moved downloader class to WP action
* added comments to several sanitization and validation methods
= 3.9.7 =
* Important Security Patches
* PHP 7 compat
* Introduced Windows-friendly Stripslashes method
* A completely new edition of File Away is in the works. Stay tuned.

== Upgrade Notice ==
= 3.9.7.7 = 
Security patches and validation improvements. Update immediately.