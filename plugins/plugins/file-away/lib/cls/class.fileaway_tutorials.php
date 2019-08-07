<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(class_exists('fileaway_admin') && !class_exists('fileaway_tutorials'))
{
	class fileaway_tutorials
	{
		public $optioninfo;
		public $helplinks;
		public function __construct()
		{
			$this->optioninfo = array();
			$this->optioninfo();
			$this->helplinks = array();
			$this->helplinks();
		}
		public function optioninfo()
		{
			$get = new fileaway_definitions;
			$optioninfo = array();
			$optioninfo['stats'] = array(
				'heading' => 'Download Statistics',
				'info' => "Default: true (if Statistics are enabled in the global settings). To disable on a per-shortcode basis, use <code>stats=\"false\"</code>. If Download Statistics is enabled in the global settings, and not explicitly disabled here, file download statistics will be gathered for each download event, unless the specific user has been excluded from statistics gathering. The user's ID, email address, ip address, and user agent string will be recorded, along with the file downloaded, its location, and the precise time of download. The data will be stored in an SQL table for retrieval at your convenience and display at your discretion.<br><br>Note: in <code>[stataway]</code> statistics tables, this attribute is disabled by default and must be expressly set to true to collect stats from downloads on statistics tables. In <code>[stataway]</code> lists, stats are enabled by default as normal."
			);
			$optioninfo['password'] = array(
				'heading' => 'Manager Mode: Override Password',
				'info' => "Enter the Override Password here, and if it matches the Override Password established in the File Away Options page, then any user IDs or user roles/capabilities specified in the prior fields (in addition to the roles and users set in the permanent settings) will have Manager Mode privileges for this shortcode only."
			);
			$optioninfo['color'] = array(
				'heading' => 'Link Color',
				'info' => "The color of primary links and styles. Default for lists: Random. Default for tables: Classic."
			);			
			$optioninfo['accent'] = array(
				'heading' => 'Accent Color',
				'info' => "Defaults to random if left blank."
			);			
			$optioninfo['iconcolor'] = array(
				'heading' => 'Icon Color',
				'info' => "Default for lists: Random. Default for tables: Classic."
			);			
			$optioninfo['icons'] = array(
				'heading' => 'Icons',
				'info' => "Defaults to File Type icons if left blank."
			);			
			$optioninfo['display'] = array(
				'heading' => 'Display Style',
				'info' => "Alphabetical Lists default to vertical layout by default."
			);
			$optioninfo['prettify'] = array(
				'heading' => 'Prettify Filenames',
				'info' => "Default: Enabled. To disable, add <code>prettify=\"off\"</code> to your shortcode. To enable, omit this attribute from your shortcode. Gets rid of various special characters and adds a sentence title to your filename."
			);			
			$optioninfo['debug'] = array(
				'heading' => 'Debug Mode',
				'info' => "If nothing is showing up on the page when you insert the shortcode, it's either because there are no files in the directory/attached to the page that you're pointing to, or because you've excluded anything that's in the directory/attached to the page that you're pointing to. Activating the debug feature will display a box in the page content that will tell you the directory or the attachment page to which your shortcode is pointing."
			);			
			$optioninfo['search'] = array(
				'heading' => 'Filtering',
				'info' => "By default, a search icon will be placed at the top-right of the table, which allows users to filter out table content to find what they're looking for. You can disable it if desired."
			);
			$optioninfo['searchlabel'] = array(
				'heading' => 'Search Label',
				'info' => "Optional. If omitted, just the search icon will show. If using a label, KEEP IT SHORT. The icon + label have to fit within the width of the hidden input field."
			);						
			$optioninfo['paginate'] = array(
				'heading' => 'Pagination',
				'info' => "By default, pagination on tables is disabled. Recommended only for large file directories/csv files."
			);			
			$optioninfo['pagesize'] = array(
				'heading' => 'Number per Page',
				'info' => "If pagination is enabled, you can set the number of files to show per page. Default is 15."
			);			
			$optioninfo['textalign'] = array(
				'heading' => 'Text Alignment',
				'info' => "For tables. Defaults to Center."
			);			
			$optioninfo['customdata'] = array(
				'heading' => 'Custom Column(s)',
				'info' => "You can add multiple custom columns to your table and add custom data to any file you want. Name the columns here, e.g., <code>Artist</code>, then to add data to your files, you have two options: If Metadata Storage is set to 'In Database', just use Manager Mode's 'Rename' function to add the metadata. It will be stored in a custom database table. If Metadata Storage is set to 'In Filename', just put the data in between square brackets [ ] at the *end* of your file name, *before* the extension. If you want to add more than one column, separate the column names here with a comma (e.g., <code>Artist, Album, Label, Year</code>), and separate the corresponding data in the fileneames with a comma. You can do this with Manager Mode's 'Rename' function, and it will format it for you automatically. Example filenames: <br /><br /><code>My Funny Valentine [Chet Baker, My Funny Valentine, Blue Note, 1994].mp3</code><br /><code>So What [Miles Davis, Kind of Blue, Columbia, 1959].mp3</code><br /><code>Birdland [Weather Report, Heavy Weather, Columbia, 1977].mp3</code><br /><br />The data in square brackets will be automatically added to the column(s) that you create here. This feature can be used for any purpose you like.<br /><br />Note that anything in square brackets will only show up in Data Tables, and, in that case, only if you name your custom column(s) here."
			);
			$optioninfo['metadata'] = array(
				'heading' => 'Metadata Storage',
				'info' => "You have two options for storing metadata for your files: 1) In Database, or 2) In Filename. See the info box for Custom Columns Name(s) (i.e., customdata) for more information. To enable Database storage, just add <code>metadata=\"database\"</code> to your File Away table shortcode. For Filename storage, just omit the <code>metadata</code> attribute, as it is the default behavior. Note, the In Filename option, do not use commas ( , ) in your descriptions, as commas are used to separate multiple columns. You may use commas as normal with the In Database option."
			);							
			$optioninfo['postid'] = array(
				'heading' => 'Post / Page ID',
				'info' => "If left blank, by default the shortcode will grab the attachments from the page or post where the shortcode is inserted (the current page). Alternatively, you can specify a post/page ID here, and the shortcode will grab the attachments from that one instead. <br /><br />If you don't know the ID, Attach Away has added an 'ID' column to your 'All Pages' and 'All Posts' pages. This column can be enabled or disabled in the File Away settings page."
			);			
			$optioninfo['capcolumn'] = array(
				'heading' => 'Caption Column',
				'info' => "You can add a custom column to your table and add custom data to any attachment file you want. For this particular column, the data will be pulled from the attachment's 'Caption' field. Name the column here, anything you want, e.g., <code>Artist</code>. Then just add the specific data to the Caption field for each attachment file. Example:<br /><br /><code>Caption Column Name: Artist</code><br /><code>Attachment 1 Caption: Jon Bon Jovi</code><br /><code>Attachment 2 Caption: Michael J. Iafrate</code><br /><br />For easy management of your attachments without leaving the page editor, File Away recommends the <a href='https://wordpress.org/plugins/wp-better-attachments/' class='inner-link' target='_blank'>WP Better Attachments</a> plugin by Dan Holloran."
			);			
			$optioninfo['descolumn'] = array(
				'heading' => 'Description Column',
				'info' => "You can add a second custom column to your table and add custom data to any attachment file you want. For this column, the data will be pulled from the attachment's 'Description' field. Name the column here, anything you want, e.g., <code>Author</code>. Then just add the specific data to the Description field for each attachment file. Example:<br /><br /><code>Description Column Name: Author</code><br /><code>Attachment 1 Description: Vaclav Havel</code><br /><code>Attachment 2 Description: Terry Eagleton</code><br /><br />For easy management of your attachments without leaving the page editor, File Away recommends the <a href='https://wordpress.org/plugins/wp-better-attachments/' class='inner-link' target='_blank'>WP Better Attachments</a> plugin by Dan Holloran."
			);			
			$optioninfo['sortfirst'] = array(
				'heading' => 'Initial Sorting',
				'info' => "If you want to disable column sorting altogether, choose 'Disable Sorting' and the files will be output in a natural alphabetical order. Otherwise, choose the column by which to sort your table on initial page load. You can choose to sort in ascending or descending order for each column. Defaults to Filename (Asc) if left blank.<br /><br />Note: If you are using multiple custom columns in a <code>[fileaway]</code> table, and you wish to sort initially by one of those custom columns, set your Initial Sorting to either Custom Column (Asc) or Custom Column (Desc) here, then in the Custom Column Name(s) field (i.e., the <code>customdata</code> attribute), put an asterisk(*) next to the name of the column by which you wish to sort initially. Don't worry. The asterisk will be removed before it gets to the page."
			);			
			$optioninfo['nolinks'] = array(
				'heading' => 'Disable Links',
				'info' => "Defaults to false. If Disable Links is set to 'True', the hypertext reference is removed from the &#60;a&#62; tag. This is in case you want, for instance, to display successful uploads without providing links to the files. You'll still want to style your links using the shortcode options, but the link functionality will be removed."
			);
			$optioninfo['redirect'] = array(
				'heading' => 'Guest Redirect',
				'info' => "If you have specified a redirect URl on your File Away settings page, and have enabled redirection here, logged-out users will be redirect to the page you specify when they click on a download link. To disable redirection of logged-out users, simply omit this attribute from the shortcode. It is, thus, disabled by default."
			);			
			$optioninfo['showrss'] = array(
				'heading' => 'Show RSS Links',
				'info' => "Defaults to false. If set to true, and your Feed Storage Directory is set, and your table is not in recursive mode, and a feed file exists for the directory, then an RSS link to that directory's feed will be displayed for user subscriptions."
			);			
			$optioninfo['showto'] = array(
				'heading' => 'Show to Roles / Capabilities',
				'info' => "Takes a comma-separated list of user roles / capabilities. If used, only those users with one of the user roles or capabilities specified in this field will have access to the file/attachment display or file upload form."
			);			
			$optioninfo['hidefrom'] = array(
				'heading' => 'Hide from Roles / Capabilities',
				'info' => "Takes a comma-separated list of user roles / capabilities. If used, those users with one of the user roles or capabilities specified in this field will <em>not</em> have access to the file/attachment display or file upload form. If this attribute is used, logged-out users are also prevented from seeing the file/attachment display or file upload form."
			);	
			$optioninfo['devices'] = array(
				'heading' => 'Device Visibility',
				'info' => "If omitted, will default to All Devices. Otherwise, select whether to display the output to desktops/notebooks, or to tablets/mobiles. This will allow you to create separate shortcodes tailored to different sized screens."
			);
			$optioninfo['maxsize'] = array(
				'heading' => 'Max Size',
				'info' => "The maximum allowed file size for each individual uploaded file. Enter an integer (e.g., 20). You will specify MB, KB, or GB under Max Size Type. If left blank, the default will be 10MB. Note that the system will also check the post_max_size and upload_max_filesize settings from your php.ini file, and if either is smaller than the size you specify here, that one will override your specification. Here are your current php.ini settings for your reference:<br /><br />post_max_size: ".fileaway_utility::ini('post_max_size', false, 'Not Set')."<br />upload_max_filesize: ".fileaway_utility::ini('upload_max_filesize', false, 'Not Set'),
			);			
			$optioninfo['maxsizetype'] = array(
				'heading' => 'Max Size Type',
				'info' => "Defaults to MB if left blank. Complements your Max Size setting. Note that the system will also check the post_max_size and upload_max_filesize settings from your php.ini file, and if either is smaller than the size you specify here, that one will override your specification. Here are your current php.ini settings for your reference:<br /><br />post_max_size: ".fileaway_utility::ini('post_max_size', false, 'Not Set')."<br />upload_max_filesize: ".fileaway_utility::ini('upload_max_filesize', false, 'Not Set'),
			);			
			$optioninfo['name'] = array(
				'heading' => 'Unique Name',
				'info' => "For File Away tables: Required only if in use with a corresponding iframe shortcode, otherwise optional. Assign a unique name. One word, no spaces. You will assign the same unique name to the corresponding File Away iframe shortcode. This will (1) enable the iframe to scroll to the top of the table when a new page is clicked, and (2) allow for easier reference when multiple iframed tables are on the same page. (Note that you can now have multiple Directory Tree/Manager Mode tables on the same page without use of the iframe, using the <code>drawerid</code> attribute.<br><br>For File Away iframes: Required. Assign a unique name. One word, no spaces. You will assign the same unique name to the corresponding File Away shortcode. This will (1) enable the iframe to scroll to the top of the table when a new page is clicked, and (2) allow for easier reference when multiple iframed tables are on the same page. <br><br>For File-Up shortcodes: Completely optional. Assign a unique name. One word, no spaces. If no name is assigned, a random unique name will be generated on each pageload."
			);
			$optioninfo['source'] = array(
				'heading' => 'Source Slug/URL',
				'info' => "Required. Enter the full URL, or just the page slug (like this: <code>/my-page-slug/</code>, of the iframed-templated page where you put your <code>[fileaway]</code> shortcode. To apply the File Away iframe template to that page, select 'File Away iframe' from the Page Template dropdown on the WordPress page editor."
			);			
			$optioninfo['scroll'] = array(
				'heading' => 'Scrolling',
				'info' => "Defaults to 'Off' if left blank. You will want to set your height attribute to a sufficient integer, and compensate by activating pagination on your <code>[fileaway]</code> table, and setting the pagination pagesize to a small number such as 10 or 20."
			);			
			$optioninfo['height'] = array(
				'heading' => 'Height',
				'info' => "Required. Enter an integer. The height attribute does not permit percentages. It is automatically in pixels so only the number is required. It is recommended to set it to a sufficient height such as 1000. If the height attribute is not set, well, your thing will look funny."
			);			
			$optioninfo['width'] = array(
				'heading' => 'Width',
				'info' => "For File Away, Attach Away, and Form Away Open shortcodes: Optional: If left blank, will default to auto-width if the type is set as 'Alphabetical List,' and to 100% if the type is set as 'Sortable Data Table.' If less than 100%, text will wrap around your list or table to the left or right, depending upon your alignment setting.<br><br>For File Away iframe shortcodes: Defaults to 100% if left blank. Otherwise, specify a pixel width by entering only the number desired. E.g., 800.<br><br>For File-Up shortcodes: Optional: If left blank, will default to 100%. If less than 100%, text will wrap around your upload form to the left or right, depending upon your alignment setting."
			);			
			$optioninfo['perpx'] = array(
				'heading' => 'Width Type',
				'info' => "Specify whether your width integer should be processed as a percentage or in pixels. Default: %"
			);
			$optioninfo['mheight'] = array(
				'heading' => 'Margin Height',
				'info' => "Defaults to 0 if left blank."
			);			
			$optioninfo['mwidth'] = array(
				'heading' => 'Margin Width',
				'info' => "Defaults to 0 if left blank."
			);			
			$optioninfo['base'] = array(
				'heading' => 'Base Directory',
				'info' => "Begin with one of the base directories you set up in the Configuration page. You can extend this path using the Sub Directory option. Defaults to Base 1 if left blank.<br><br>For File-Up shortcodes: This is the initial folder to which a user may upload files. If uploads are not set to a fixed location, they will be able to upload to any subdirectories, but not to any parent directories of the initial directory specified. You can extend this initial path using the Sub Directory option."
			);			
			$optioninfo['sub'] = array(
				'heading' => 'Sub-Directory',
				'info' => "Optional: Define a sub-directory to extend the path of your selected base directory. It can be one or more levels deep. You can leave out leading and trailing slashes. I.e., <code>uploads/2010</code> rather than <code>/uploads/2010/</code><br /><br />You can also use one or more of the five dynamic path codes: <code>fa-firstlast</code> <code>fa-userid</code> <code>fa-username</code> <code>fa-userrole</code> and <code>fa-usermeta(metakey)</code> (replacing \"metakey\" with the user meta key name of your choice). If you've created directories that are named for your users' first and last names (e.g., jackhandy), userid (e.g., 15), username (e.g., admin), user role (e.g., subscriber), or for any user meta value from any user meta key they may have, the codes will dynamically point whoever is logged in to their appropriate folder. <br /><br />For example: <code>uploads/fa-userrole/fa-firstlastfa-userid</code> will point dynamically, depending on who is logged in, to directories like: <code>uploads/editor/jackhandy15</code> or <code>uploads/subscriber/joanjett58</code>.<br><br>The directories you create for your users must be all lowercase with no spaces, with the exception of the <code>fa-username</code> code. On the Basic Configuration tab of the File Away settings page, you may choose whether to render <code>fa-username</code> as forced lowercase or strict case-sensitive. <code>NOTE:</code> Rather than creating the directories manually, you can use the <code>makedir='true'</code> attribute and File Away will create static and dynamic directories for you, recursively, if the directory does not already exist."
			);	
			$optioninfo['makedir'] = array(
				'heading' => 'Make Directories On Page Load',
				'info' => "For both <code>[fileaway]</code> and <code>[fileup]</code> shortcodes, you don't have to worry about whether the directory you're pointing to exists. Adding <code>makedir='true'</code> to either shortcode will create the specified directory if it doesn't already exist, and in it, an empty index.php file to prevent direct browser access to the directory. You can do this for both static and dynamic directories. You can do this for just your base directories, or for your base+sub attributes combined. <br><br>Static Example: If Base 1 equals <code>wp-content/uploads</code> and your sub attribute equals <code>files/images/jpegs</code>, then the directory path of <code>wp-content/uploads/files/images/jpegs</code> will be created for you, recursively, if it doesn't already exist.<br><br>Dynamic Example: If Base 1 equals <code>wp-content/uploads/fa-userrole</code> and your sub attribute equals <code>personal/fa-username</code>, then the dynamic directory path for each user will be created the first time they hit the page, something like this: <code>wp-content/uploads/editor/personal/magicjohnson</code>.<br><br>You can do this with File Away and File Up shortcodes together in tandem, so that users can upload files to their own directories. We recommend a basic setup like this (though the path here is just an example): <code>[fileup base=\"1\" sub=\"personal/fa-username\" makedir=\"true\"]&lt;br&gt;&lt;br&gt;[fileaway base=\"1\" sub=\"personal/fa-username\" directories=\"on\"]</code><br><br>You can have <code>makedir=\"true\"</code> in both shortcodes if you want, but you only really need it in the first one that appears on the page. If it's in the second shortcode and not the first, then the first shortcode will return blank because the directory does not yet exist. Adding <code>directories=\"on\"</code> (or <code>manager=\"on\"</code>, your preference) to your File Away shortcode will ensure that the user sees their File Away table even though there are not initially any files in it. If not in Directory Tree or Manager Mode, File Away will return blank if there are no files. <br><br>Penultimately, if Dynamic Path codes are used, a directory will only be created if the user is logged in. Finally, leaving <code>makedir=\"true\"</code> in your shortcode will not slow anything down. File Away and File Up have always checked to make sure the directory exists before proceeding with the rest of their business. Creating a directory takes a split second anyway, but if it already exists, the make-directory command is simply skipped over."
			);						
			$optioninfo['images'] = array(
				'heading' => 'Images',
				'info' => "Optional: If left blank, the default behavior is to list image files along with all other files. You can alternatively choose to exclude all image types from your display, or to show only image types in your display. Image types are: ".implode(', ', $get->imagetypes)
			);			
			$optioninfo['code'] = array(
				'heading' => 'Code Documents',
				'info' => "By default, and for security, web code documents are excluded from file displays. If you have a directory or attachment page with some code docs that you want to include in your display, you can choose to include them along with any/all other file types. Code file types excluded by default are: ".implode(', ', $get->codexts).". The one exception is index.htm/l and index.php files, which are always excluded, and will not be included if Code Docs are enabled."
			);
			$optioninfo['show_wp_thumbs'] = array(
				'heading' => 'Show WordPress Thumbnail Images',
				'info' => "Optional: If left blank or omitted, the default behavior is to hide WordPress-generated thumbnail images. You can alternatively choose to include them in your file list/table.",
			);						
			$optioninfo['only'] = array(
				'heading' => 'Show Only Specific',
				'info' => "If you'd like, you can enter a comma-separated list of filenames and/or file extensions here. Doing this will filter out anything not here entered. Do not use quotation marks. Just separate each item with a comma. <br /><br />Example: <br /><br /><code>My Polished Essay, .mp3, Gertrude Stein Essay, .jpg</code><br /><br />This will tell the shortcode only to ouput files that have the string 'My Polished Essay' or 'Gertrude Stein Essay', and any file with the extension .mp3 or .jpg"
			);			
			$optioninfo['exclude'] = array(
				'heading' => 'Exclude Specific',
				'info' => "Here you can enter a comma-separated list of filenames or file extensions to exclude from your list. Example: <br /><br /><code>.doc, .ppt, My Unfinished Draft Essay, Embarrassing Photo Name</code> <br /><br />This will exclude all .doc and .ppt files from your list, as well as your ugly first draft and that photo of you after that party."
			);
			$optioninfo['include'] = array(
				'heading' => 'Include Specific',
				'info' => "This option also takes a comma-separated list of files or file extensions, but it is primarily for correcting / fine tuning. For instance, if you excluded '.doc' in the above field, you may want to include '.docx' here, so it isn't filtered out, if that's your fancy."
			);						
			$optioninfo['action'] = array(
				'heading' => 'File Type Action',
				'info' => "If you specify any file types or file groups, the action you select here will determine whether the specified file types are prohibited, or the only permitted file types. If left blank, the default option will be permit."
			);			
			$optioninfo['filetypes'] = array(
				'heading' => 'File Types',
				'info' => "This option takes a comma-separated list of file extensions (do not precede the extension with a period). These file types will be either permitted or prohibited, depending on the action you select. If you also specify file groups, the file types associated with the selected groups will be added to the list here."
			);			
			$filegroups = '';
			foreach($get->filegroups as $group => $discard)
			{
				if($group == 'unknown') continue;
				$filegroups .= '<span style="color:red;">'.$group.':</span> ['.implode(', ', $get->filegroups[$group][2]).']<br>';
			}
			$optioninfo['filegroups'] = array(
				'heading' => 'File Type Groups',
				'info' => "You may select multiple groups from the list of available file groups. All file types associated with the selected file groups will be either permitted or prohibited, depending on the action you select. If you also specify a list of individual file types, they will be added to the list here.<br /><br />$filegroups"
			);			
			$optioninfo['heading'] = array(
				'heading' => 'Heading',
				'info' => "Optional: Give your list or table a nice title."
			);			
			$optioninfo['hcolor'] = array(
				'heading' => 'Heading Color',
				'info' => "Defaults to random color if left blank."
			);			
			$optioninfo['single'] = array(
				'heading' => 'Single or Multiple Uploads',
				'info' => "Optional: If left blank, will default to multiple. If single is selected, a user may only upload one file at a time."
			);			
			$optioninfo['align'] = array(
				'heading' => 'Alignment',
				'info' => "Use in combination with the width setting to float your list, table, or upload form to the left or right of the page, to allow other page content to wrap around it. Choose 'None' to prevent wrapping. For lists and tables, defaults to 'Left.' For File-Up shortcodes, defaults to 'None.'"
			);			
			$optioninfo['size'] = array(
				'heading' => 'File Size',
				'info' => "Will show the file size by default if left blank. In tables, you'll be able to sort by file size."
			);			
			$optioninfo['corners'] = array(
				'heading' => 'Corners',
				'info' => "Defaults to all corners rounded if not used. Does not apply to the minimal-list theme, or to tables."
			);			
			$optioninfo['mod'] = array(
				'heading' => 'Date Modified',
				'info' => "If left blank, will show by default in tables, as a sortable column, and will hide by default in lists. (Note: This option is not available for Post / Page Attachments.)"
			);			
			$optioninfo['bulkdownload'] = array(
				'heading' => 'Bulk Download',
				'info' => "If enabled, users will be able to select specific files, or select all files, in a table, then click on the download button at the bottom of the table in order to download a zip file containing their selections. Note that Bulk Downloads are automatically enabled in Manager Mode, but can be enabled here for any other table type (regular, recursive, directory tree, or audio playback). Default: Disabled."
			);
			$optioninfo['flightbox'] = array(
				'heading' => 'Flightbox',
				'info' => "File Away's answer to the Lightbox, designed from the ground up for use with File Away and Attach Away lists and tables. Choose whether to use the Flightbox for images (jpg, gif, png), video files (flv, mp4, m4v, ogv, webm, YouTube and Vimeo), PDF files, or for all three, choose multi. The size of the Flightbox window is dynamic based on the current size of the broseer window. Every time a new file is called up into the Flightbox, the box resizes to fit the dimensions of your file as well as the screen size."
			);
			$optioninfo['boxtheme'] = array(
				'heading' => 'Flightbox Theme',
				'info' => "Choose from one of four themes, or design your own. The icon colors will be matched according to your Icon Color selection for your list or table."
			);
			$optioninfo['maximgwidth'] = array(
				'heading' => 'Max Image Width',
				'info' => "An integer only. E.g., <code>1000</code>. Optional. If omitted, the box window will never be larger than the user's screen, or than the default max width of 1920 (pixels). The window size will be proportional to images, and images will not be stretched."
			);
			$optioninfo['maximgheight'] = array(
				'heading' => 'Max Image Height',
				'info' => "An integer only. E.g., <code>600</code>. Optional. If omitted, the box window will never be larger than the user's screen, or than the default max height of 1080 (pixels). The window size will be proportional to images, and images will not be stretched."
			);
			$optioninfo['videowidth'] = array(
				'heading' => 'Video Width',
				'info' => "An integer only. E.g., <code>1000</code>. Optional. If omitted, the box window will never be larger than the user's screen, or than the default max video width of 1920 (pixels). The video player will always maintain a 16:9 ratio."
			);
			$optioninfo['recursive'] = array(
				'heading' => 'Recursive Directory Iteration',
				'info' => "If disabled (the default), only the files in the single directory specified will be displayed. If enabled, the files from all subdirectories will be displayed as well. If Directory Tree mode is enabled, Recursive Directory Iteration will be disabled."
			);			
			$optioninfo['directories'] = array(
				'heading' => 'Directory Tree Mode',
				'info' => "If disabled (the default), your File Away table will display only the single directory specified in your Base and Sub attributes. If Directory Tree mode is enabled, the directory specified will be the starting-off point, but the user will be able to navigate through any subsequent directories as well. It is recommended that you use this mode in conjunction with the File Away iframe shortcode (see instructions under that shortcode option)."
			);			
			$optioninfo['manager'] = array(
				'heading' => 'Manager Mode',
				'info' => "If enabled, users with access privileges will be able to manage files from the front-end. Users without access privileges will still see the table, but the management features will not be output to the page. Manager Mode currently includes the ability to rename and delete files individually, and to copy, move, and delete files in bulk.<br /><br />If custom columns are included in the table, the Rename feature will provide additional fields for each visible custom column, and will automatically format the filename for use with File Away custom columns.<br /><br />See the Manager Mode tab on the File Away options page to set access privileges and/or use the Manager Mode options below to fine tune privileges on a per-shortcode basis. If Manager Mode is enabled, Directory Tree Mode will also be enabled automatically. Default: Disabled."
			);			
			$optioninfo['role_override'] = array(
				'heading' => 'Manager Mode: User Role / Capability Access Override',
				'info' => "If the Override Password is provided in the password field, and it matches the Override Password established in the File Away Options page, then any user roles or capabilities specified here (in addition to the user roles / capabilities set in the permanent settings) will have Manager Mode privileges for this shortcode only. Enter a comma-separated list of user roles / capabilities, like so: <code>author,subscriber,townidiot,edit_pages</code>.<br /><br />Alternatively, in place of specifying actual roles or caps, you can elect to enter the dynamic code: <code>fa-userrole</code> into the Role Access Override field. Be aware that doing this will effectively grant Manager Mode access to all logged in users. Thus, the dynamic role code should only be used on File Away tables where the directory paths are also dynamic. This will grant users access to rename, copy, move, and delete files within the confines of their of own subdirectories."
			);			
			$optioninfo['dirman_access'] = array(
				'heading' => 'Manager Mode: Directory Management Access',
				'info' => "If left blank, all users otherwise able to access manager mode will have the ability to create/delete/rename sub-directories of the established parent directory. If you wish to limit access to sub-directory management, include a comma-separated list of user roles / capabilities here. Only those roles / capabilities listed here will have access to directory management."
			);			
			$optioninfo['user_override'] = array(
				'heading' => 'Manager Mode: User Access Override',
				'info' => "If the Override Password is provided in the password field, and it matches the Override Password established in the File Away Options page, then any user IDs specified here (in addition to the users set in the permanent settings) will have Manager Mode privileges, for this shortcode only. Enter a comma-separated list of user IDs, like so: <code>20,217,219</code>.<br /><br />Alternatively, in place of specifying actual user IDs, you can elect to enter the dynamic code: <code>fa-userid</code> into the User Access Override field. Be aware that doing this will effectively grant Manager Mode access to all logged in users. Thus, the dynamic user ID code should only be used on File Away tables where the directory paths are also dynamic. This will grant users access to rename, copy, move, and delete files within the confines of their of own subdirectories."
			);			
			$optioninfo['drawerid'] = array(
				'heading' => 'Drawer ID Number',
				'info' => "You can have multiple Directory Tree Nav or Manager Mode tables on the same page, without the use of the iframe, if you assign each table a unique drawer id number. Just assign them a number the order the shortcodes appear in your page editor. <code>drawerid=1</code> for your first shortcode, <code>drawerid=2</code> for your second shortcode, etc. In the url, it will apear like this: <code>?drawer1=</code>"
			);	
			$optioninfo['drawericon'] = array(
				'heading' => 'Directory Icon',
				'info' => "The icon used for directories in Directory Tree mode. Default: Drawer."
			);			
			$optioninfo['drawerlabel'] = array(
				'heading' => 'Directory Column Label',
				'info' => "The column heading for the Directory Names and File Names. Default: Drawer/File."
			);			
			$optioninfo['parentlabel'] = array(
				'heading' => 'Parent Directory Pseudonym',
				'info' => "If you don't want people to easily see the real name of the topmost directory in a Directory Tree Nav table, you can apply a pseudonym for that directory here. Leave blank/omit from shortcode to show the real directory name on the nav menu."
			);			
			$optioninfo['excludedirs'] = array(
				'heading' => 'Exclude Directories',
				'info' => "In addition to any permanent directory exclusions specified on the File Away Options config tab, here you can include a comma-separated list of directory names you wish to exclude from this specific Directory Tree table or Recursive table/list. Do not include the forward slashes ('/'). The names listed here must match your directory names exactly, and are case-sensitive. Example:<br /><br /><code>My Private Files, Weird_Server_Directory_Name, etc.</code>"
			);			
			$optioninfo['onlydirs'] = array(
				'heading' => 'Only These Directories',
				'info' => "For your Directory Tree tables or Recursive tables/lists, here you can specify a comma-separated list of the only directory names you want to include in this table. All other sibling directories will be excluded. These directories must be found in the parent directory to which your shortcode is pointing (ie, your Base Directory and Sub Directory shortcode settings).<br /><br />Note: If you specify a directory 'My Files,' any subdirectories of 'My Files' will also be included. Example:<br /><br /><code>My Public Files, Public Records, etc.</code>"
			);			
			$optioninfo['playback'] = array(
				'heading' => 'Audio Playback',
				'info' => "Please read these notes carefully:<br /><br />You have two activation options: compact, and extended. Compact will put a small play/stop button in your filetype column. Extended will put a full-featured audio controller, with play/pause, draggable progress bar, track time, and volume, in your filename column.<br /><br />The audio player is compatible with mp3, ogg, and wav. If any of those file types are found, the player will be added to the column. Note that if you have multiple types with the same filename, then only one will show in the table, and the other file types will be added to the player as fallbacks for greater cross-browser compatibility. For instance: <br /><br />'My Song.mp3', 'My Song.ogg', and 'My Song.wav' will only show once on the table, but each file will be nested in the audio player as fallbacks for each other. If you only have one or two of those types in the directory, then only those found will be added to the player. One is sufficient. <br /><br />Note that any other audio file types that have the same filename will appear as download links under the File Name in the File Name column. (See <a class='inner-link' href='https://wordpress.org/plugins/file-away/screenshots/' target='_blank'>screenshots</a> for clarity.) For instance:<br /><br />If you have 'My Song.mp3', 'My Song.ogg', 'My Song.aiff', 'My Song.rx2' in the directory, then the mp3 and ogg files will be nested in the player, and each of the four matching audio files will be given their own download link in the second column, specifying their file type. The system searches for the following file types with matching file names, and will add them automatically: <code>".implode(', ', $get->filegroups['audio'][2])."</code><br /><br />If no mp3, ogg, or wav file exists for that file name, then the files will appear in the table as any other file type, with no audio player. <br /><br />Note that you can also place your sample/playback files (mp3, ogg, wav) in a separate directory from the downloadable files (any audio file type), and specify the playback file directory using the 'playbackpath' shortcode attribute. See the info link next to 'Playback Path' for more info on that.<br /><br />Finally, note that Audio Playback mode is compatible with regular tables, Directory Tree tables, and Recursive tables, but is not compatible with Manager Mode."
			);			
			$optioninfo['onlyaudio'] = array(
				'heading' => 'Audio Files Only',
				'info' => "Activate this option and only audio files will be shown in the table. Disabled, all otherwise-not-excluded files will be shown, but only audio files will get the playback button."
			);			
			$optioninfo['loopaudio'] = array(
				'heading' => 'Loop Audio',
				'info' => "Activate this option to play audio files in a continuous loop."
			);			
			$optioninfo['playbackpath'] = array(
				'heading' => 'Playback Path',
				'info' => "Optional. By default, the Playback system will search for mp3, ogg, and wav files in the directory specified by your Base Directory and Sub Directory shortcode attributes. If, however, you wish to store your playback files in a separate location from your download files, you can specify that location here. Rules:<br /><br />Do NOT include opening and closing forward slashes. Correct: <code>Files/Audio/Samples</code>. Incorrect: <code>/Files/Audio/Samples/</code><br /><br />Note: You must include the entire path beginning from your WordPress installation directory or site root. The Playback Path is ignorant of your specified base directory. So, let's say Base Directory 1 equals 'Files':<br /><br /><code>[fileaway base=\"1\" sub=\"Audio/Downloads\" playbackpath=\"Files/Audio/Samples\" playback=\"yes\"]</code><br /><br />If you have Directory Tree mode or Recursive mode enabled, you will probably want to be sure that your Playback folder is not a subdirectory of your Downloads folder.<br /><br />Finally, you can only specify one playback path for any given File Away table. It will not recurse into subdirectories looking for playback files."
			);			
			$optioninfo['playbacklabel'] = array(
				'heading' => 'Playback Column Label',
				'info' => "When Audio Playback is not enabled, this column heading is fixed to 'Type'. When compact Playback is enabled, you can specify a different column label if desired. E.g., 'Sample'"
			);	
			$optioninfo['encryption'] = array(
				'heading' => 'Encrypted Downloads',
				'info' => "Disabled by default. If enabled, download links will be encrypted and the file locations will be masked. Not compatible with Manager Mode. Not smart to use with Directory Tree Navigation (since the directories are plain to see anyway), or with Bulk Downloads (the file paths can be found in a data-attribute in the HTML of each table row), but fine with Recursive Mode and with Audio Playback."
			);					
			$optioninfo['orderby'] = array(
				'heading' => 'Order By',
				'info' => "Choose whether to order your page attachments by title, menu order, post id, date, date modified, or random."
			);			
			$optioninfo['desc'] = array(
				'heading' => 'Descending',
				'info' => "Omit for ascending order; 'Yes' for descending order."
			);			
			$optioninfo['s2skipconfirm'] = array(
				'heading' => 'S2Members Skip Confirmation',
				'info' => "Deactivates the javascript confirm dialogue on S2Member download links."
			);			
			$optioninfo['fixedlocation'] = array(
				'heading' => 'Upload Loations Options',
				'info' => "If set to fixed, the only upload directory will be the path you specify with the base+sub attributes. By default, a user will be able to select subdirectories of that specified path from a dropdown."
			);	
			$optioninfo['matchdrawer'] = array(
				'heading' => 'Match Upload Directory to Current Directory Tree Drawer',
				'info' => "You can lock the uploader path of the File Up form to match the current drawer of a corresponding File Away Directory Tree or Manager Mode table. If <code>matchdrawer</code> is enabled, <code>fixedlocation</code> will also be hardcoded to \"true\". Here's what you need to know:<br><br>1. The <code>base</code> and <code>sub</code> specifications must be the same for the two matching File Away and File Up shortcodes.<br>2. If you have only one File Away Directory Tree/Manager Mode table on the page, and you have not specified a <code>drawerid</code> in your <code>[fileaway]</code> shortcode, then all you have to do is add <code>matchdrawer=\"true\"</code> to your <code>[fileup]</code> shortcode.<br>3. If, however, you have more than one File Away Directory Tree/Manager Mode table on the page, then each of your File Away shortcodes should have a unique <code>drawerid</code> specified. In that case, the <code>matchdrawer</code> specification in our <code>[fileup]</code> shortcode should match the Drawer ID of the specific table to which you wish to lock the upload form. You can have multiple upload forms, each matched to one of multiple tables. It would look like this:<br><br>".
				"<code>[fileup base=1 sub=\"files\" matchdrawer=\"true\"]</code> <-- No specific drawer specified, just \"true\"<br>".
				"<code>[fileaway base=1 sub=\"files\" manager=\"true\"]</code> <-- no drawerid specified<br><br>".
				"<code>[fileup base=2 sub=\"resources\" matchdrawer=\"1\"]</code> <-- matches drawer id 1<br>".
				"<code>[fileaway base=2 sub=\"resources\" directories=\"true\" drawerid=\"1\"]</code> <-- drawer id set to 1<br><br>".
				"<code>[fileup base=2 sub=\"docs\" matchdrawer=\"2\"]</code> <-- matches drawer id 2<br>".
				"<code>[fileaway base=2 sub=\"docs\" manager=\"true\" drawerid=\"2\"]</code> <-- drawer id set to 2"
			);	
			$optioninfo['uploader'] = array(
				'heading' => 'Append Uploader Name or User ID to File Name',
				'info' => "If enabled and if the user is logged in, the user's display_name or User ID will be appended to the uploaded filename in File Away customdata format. In turn, you can display the uploader information in your File Away table using <code>[fileaway type=\"table\" customdata=\"Uploaded By\"]</code>"
			);						
			$optioninfo['uploadlabel'] = array(
				'heading' => 'Upload Label',
				'info' => "Change the text on the upload button."
			);
			$optioninfo['overwrite'] = array(
				'heading' => 'Overwrite Filenames on Upload',
				'info' => "By default, if an uploaded file has the same name as an existing file, the uploaded file will be automatically renamed. If overwrite is set to true (<code>overwrite=\"true\"</code>), all files uploaded will overwrite any file with the same filename. This is an either/or setting. Overwriting files should only be enabled in very specific circumstances. Note: overwriting will always be disabled if the user is not logged in."
			);			
			$optioninfo['thumbnails'] = array(
				'heading' => 'Media Thumbnails',
				'info' => "You can have thumbnail images for jpg, png, and gif, images, for PDF files, and for any video file type that is playable in the Flightbox, including YouTube and Vimeo videos. Video thumbnails will work in either transient or permanent thumbnails mode. Thumbnails for video files must be supplied by you (with the exception of YouTube and Vimeo thumbs); they must exist in the same directory as the video file, and they must follow this naming format: <br><code>myVideofile.mp4<br>_thumb_vid_myVideofile.jpg</code><br><br>Video thumbnail images can be either <code>jpg</code> or <code>png</code>. The latter will take priority if both image types are found. The thumbnail images for videos should probably be at least 180px wide by 120px in height. Other than that, they can be any size. They will be scaled and cropped purely by CSS3.<br><br>YouTube and Vimeo thumbs will be grabbed from YouTube and Vimeo respectively, but if you want to override the default thumb from their servers, you can add your own thumbnail image to the directory as you would any other video thumbnail.<br><br>For jpg/jpeg, gif, and png thumbnails, you have two options: transient and permanent. Transient requires resources every time the page loads, as it generates a thumbnail for each image, but only temporarily. It does it all over again the next time the page loads. Permanent will create a permanent thumbnail image the first time the page loads. The next time the page loads, if that thumbnail already exists, it doesn't have to create it again. Permanent thumbnails are prefixed by <code>_thumb_wd_</code> or <code>_thumb_sq_</code>, followed by the filename (medium and large thumbs also include med_ or lrg_ in the prefix).<br /><br />Since transient thumbnails require more resources, there are other options to determine how to skip over images that are too large for your server to handle. See the info links for the Max Source Bytes, Max Source Width, and Max Source Height options that will appear below when the Transient option is selected.<br><br>PDF thumbnails will be generated only in permanent mode."
			);			
			$optioninfo['thumbsize'] = array(
				'heading' => 'Thumbnail Size',
				'info' => "Default: Small. If omitted, the thumbnail size will be small.<br><br>Small: 60x40 (wide) and 40x40 (sqaure)<br>Medium: 120x80 (wide) and 80x80 (square)<br>Large: 180x120 (wide) and 120x120 (square)."
			);	
			$optioninfo['thumbstyle'] = array(
				'heading' => 'Thumbnail Style',
				'info' => "The cropped dimensions and aesthetics of your generated thumbnails. The dimensions (wide/oval, square/circle) are fed into the server-side script that generates the thumbnails. The sharp/rounded specification is handled by the CSS on the client-side."
			);			
			$optioninfo['graythumbs'] = array(
				'heading' => 'Thumbnail Grayscale Filter',
				'info' => "If set to Grayscale, the css will apply a grayscale filter to your thumbnails for all browsers that can handle it."
			);			
			$optioninfo['maxsrcbytes'] = array(
				'heading' => 'Max Image Source Size in Bytes',
				'info' => "This setting applies to 'transient' thumbnails only. Default: <code>1887436.8</code> (i.e., 1.8M)<br /><br />If the pixel dimensions and/or filesize of your image are too large for your server to handle, the script will fail and return a broken image graphic in place of your thumbnail. To prevent this, we set the maximum size in bytes, maximum width in pixels, and maximum height in pixels, of the source image. If the source image is greater than any one of these, the filetype icon will be output instead of attempting to generate a thumbnail.<br /><br />Tweak these three settings to suit your server and find the right balance. Find the lowest threshold for an image where the server can easily handle generating the thumbnail, and set it there. <br /><br />You can also adjust your <code>memory_limit</code> setting in your php.ini file, but be very careful about making this limit too large, which might create other problems for you."
			);			
			$optioninfo['maxsrcwidth'] = array(
				'heading' => 'Max Image Source Width in Pixels',
				'info' => "This setting applies to 'transient' thumbnails only. Default: <code>3000</code><br /><br />If the pixel dimensions and/or filesize of your image are too large for your server to handle, the script will fail and return a broken image graphic in place of your thumbnail. To prevent this, we set the maximum size in bytes, maximum width in pixels, and maximum height in pixels, of the source image. If the source image is greater than any one of these, the filetype icon will be output instead of attempting to generate a thumbnail. <br /><br />Tweak these three settings to suit your server and find the right balance. Find the lowest threshold for an image where the server can easily handle generating the thumbnail, and set it there. <br /><br />You can also adjust your <code>memory_limit</code> setting in your php.ini file, but be very careful about making this limit too large, which might create other problems for you."
			);			
			$optioninfo['maxsrcheight'] = array(
				'heading' => 'Max Image Source Height in Pixels',
				'info' => "This setting applies to 'transient' thumbnails only. Default: <code>2500</code><br /><br />If the pixel dimensions and/or filesize of your image are too large for your server to handle, the script will fail and return a broken image graphic in place of your thumbnail. To prevent this, we set the maximum size in bytes, maximum width in pixels, and maximum height in pixels, of the source image. If the source image is greater than any one of these, the filetype icon will be output instead of attempting to generate a thumbnail. <br /><br />Tweak these three settings to suit your server and find the right balance. Find the lowest threshold for an image where the server can easily handle generating the thumbnail, and set it there. <br /><br />You can also adjust your <code>memory_limit</code> setting in your php.ini file, but be very careful about making this limit too large, which might create other problems for you."
			);
			$optioninfo['bannerize'] = array(
				'heading' => 'Banner Interval',
				'info' => "To activate banners in your table, just enter the integer here, i.e., the number of files that should be displayed between each instance of a banner. For further instructions on how to Bannerize your File Away table, see the Tutorials tab on the File Away settings page."
			);
			$optioninfo['fadein'] = array(
				'heading' => 'Fade In',
				'info' => "Default: Disabled. Tables or lists with large numbers of files, and especially paginated tables, can sometimes take a second or two to initialize, and the CSS may look funky for a bit while everything is getting situated. To avoid this, you can choose to fade in your list or table after it's done getting ready. If you choose Opacity Fade, the height of the list or table will be preserved while it's not yet visible. If you choose Display Fade, any content below the list or table will be pushed down when it fades in."
			);
			$optioninfo['fadetime'] = array(
				'heading' => 'Fade In Time',
				'info' => "Default: 1000. Choose the amount of time the fade in will take, in milliseconds. E.g., 1500 equals 1.5 seconds. There will be a fixed one second delay upon pageload before the fade in begins."
			);
			$optioninfo['nolinksbox'] = array(
				'heading' => 'Flightbox Download Links',
				'info' => "Default: Enabled. You can disable the Download Arrow on the Flightbox if you so desire. Won't stop clever people from right-clicking and saving images, and there's nothing you can do to stop PDFs from being saved/printed. Nevertheless, the option exists for your discretion."
			);
			$optioninfo['limit'] = array(
				'heading' => 'Limit Results',
				'info' => "Default: No Limit. If you wish to limit the number of files that are output to your list or table from the scan, enter the number of results you'd like here. Integers only, e.g., <code>25</code>"
			);
			$optioninfo['limitby'] = array(
				'heading' => 'Limit Results By Order Type',
				'info' => "Default: Random (if limit is set). If you haven't set a limit, this feature will do nothing. Otherwise, choose from the available options. For instance, if you want to get the five most recent files in the directory, enter <code>5</code> for the <code>limit</code> attribute, and <code>mostrecent</code> here."
			);
			$optioninfo['filenamelabel'] = array(
				'heading' => 'File Name Column Label',
				'info' => "Optional. The label for the File Name column. You may wish to give it a different name. E.g., <code>Links</code> or <code>Titles</code>."
			);
			$optioninfo['datelabel'] = array(
				'heading' => 'Date Modified Column Label',
				'info' => "Optional. The label for the Date Modified column. Maybe shorten it to just <code>Updated</code> or something."
			);
			$optioninfo['placeholder'] = array(
				'heading' => 'CSV Selection Placeholder Text',
				'info' => "If you're not pointing to a specific CSV filename, and if there is more than one CSV file in the directory or directories to which you're pointing, a dropdown will appear allowing the user to select the CSV file they wish to load into the table. By default, the placeholder text will read <code>Select CSV</code>. You can change it here if you'd like."
			);
			$optioninfo['sorting'] = array(
				'heading' => 'Column Sorting',
				'info' => "Default if not in Editor Mode: Disabled. You can enable or disable column sorting on your CSV table. If in Editor Mode, sorting will be enabled."
			);
			$optioninfo['filename'] = array(
				'heading' => 'CSV File Name',
				'info' => "If you omit the Filename, the shortcode will scan the directory (or directory and sub-directories if recursive iteration is enabled) for all CSV files, and display them in a dropdown for selection by the user. The selected CSV will then load into a table on the page. If, however, you specify a filename, then only that CSV file will load, and there will be no dropdown selection available to the user.<br><br>Note that for CSV filenames, File Away Dynamic Path Codes can be used. See the tutorial on Dynamic Paths for detailed information, but in brief: <code>fa-username</code>, <code>fa-userrole</code>, <code>fa-userid</code>, <code>fa-firstlast</code>, and <code>fa-usermeta(metakey)</code> (replacing \"metakey\" with the user meta key name of your choice) are the five Dynamic Codes. So if the CSV file is named for the user's username, in directory also named for their username, like this for instance: <code>/files/users/billybob/billybob_datasheet.csv</code>, then your shortcode would look something like this: <code>[fileaway_values base=\"1\" sub=\"files/users/fa-username\" filename=\"fa-username_datasheet.csv\"]</code>"
			);
			$optioninfo['editor'] = array(
				'heading' => 'CSV Editor Mode',
				'info' => "Default: Disabled. If enabled, the user will be able to change the values of any cell, add new rows to the spreadsheet, delete existing rows, create new columns, delete existing columns, rename columns, and save timestamped backup files to the server. If Editor Mode is enabled and a specific filename is not specified, the user will also be able to create new CSV files from scratch. To change the value of a cell, double-click on the cell, enter the new value, then click outside the cell. It will save automatically. To add or delete rows, right-mouse click on a row to get the context-menu. To add, delete, or rename columns, right-mouse click on a column heading. When a row is deleted, or a column is modified, the page will automatically refresh in order to update the row and column numbers in the table, to ensure subsequent changes to the document are correct. To see that the changes have been made to your actual CSV file on the server, you can download the CSV at any time (click on the filename header if there is no dropdown, or the Down Arrow icon if there is a dropdown)."
			);
			$optioninfo['makecsv'] = array(
				'heading' => 'Make New CSV',
				'info' => "This takes a comma-separated list of Column Headers for a new CSV file which will be created for you on pageload if the file does not already exist in the directory specified. <code>makecsv</code> will be disabled if no filename is specified in the <code>filename</code> attribute. If <code>makecsv</code> is used, <code>makedir</code> will also be activated whether specified or not. Example: <code>Column 1 Header,Column 2 Header,Column 3 Header</code>. It no filename is specified, you will be able to create new CSV documents using the UI on the front-end, with Editor Mode enabled."
			);
			$optioninfo['read'] = array(
				'heading' => 'Read Encoding',
				'info' => "Default if omitted: ISO-8859-1. The character encoding set with which your CSV file will be read."
			);
			$optioninfo['write'] = array(
				'heading' => 'Write Encoding',
				'info' => "Default if omitted: ISO-8859-1. The character encoding set with which your CSV file will be written."
			);
			$optioninfo['numcols'] = array(
				'heading' => 'Total Number of Columns',
				'info' => "Required. Designate the total number of columns for your table."
			);
			$optioninfo['sort'] = array(
				'heading' => 'Sorting Options',
				'info' => "Choose whether the initial sort will be in ascending or descending order, or disable column sorting completely."
			);
			$optioninfo['initialsort'] = array(
				'heading' => 'Initial Sort',
				'info' => "Designate the column by which to sort the table on initial pageload. Identify the column by its number. Default will be 1 if left blank."
			);
			$optioninfo['col'] = array(
				'heading' => 'Column Heading',
				'info' => "Required. The heading for this particular column."
			);
			$optioninfo['colclass'] = array(
				'heading' => 'Column Class',
				'info' => "Apply one or more CSS classes to your column heading cell. Separate multiple classes with a space."
			);
			$optioninfo['coltype'] = array(
				'heading' => 'Column Sort Type',
				'info' => "Choose whether the column should be sorted as Alphabetical or as Numeric. Default: alpha"
			);
			$optioninfo['colsort'] = array(
				'heading' => 'Column Sort Option',
				'info' => "Choose whether to enable or disable sorting for this particular column. If sorting is disabled for this column, it will still be sorted according to other columns, but will not be sortable itself. By default, all columns are sortable, if sorting is enabled globally."
			);
			$optioninfo['classes'] = array(
				'heading' => 'CSS Classes',
				'info' => "Optionally apply one or more CSS classes to the outermost containing div of your table. Separate multiple classes with a space."
			);
			$optioninfo['sortvalue'] = array(
				'heading' => 'Sort Value',
				'info' => "A hidden value by which to sort that column, if you do not wish to sort by the visible cell content itself. Useful for dates, or images, or whatever you like. For example, if your date field id is 125, your cell shortcode could look like this:<br><br><code>[formaway_cell classes=\"mydateclass\" sortvalue=\"[125 format=Ymd]\"][125 format='D, F jS, Y'][/fileaway_cell]</code><br><br>This would sort the dates column like this: <code>20141231</code>, so that the dates are sorted in proper chronological order. The sort value should be a relatively short string of text, not an image and not a string that includes HTML tags."
			);
			$optioninfo['colspan'] = array(
				'heading' => 'Colspan',
				'info' => "An integer determining how many columns this cell should cover. If omitted, the default is 1. Note that using the colspan attribute with an integer greater than 1 will effectively destroy the table's ability to sort columns."
			);
			$optioninfo['clearfix'] = array(
				'heading' => 'Clearfix',
				'info' => "Optionally add a clearfix div to the end of your table. Omit this attribute if you do not wish to add a clearfix div."
			);
			$optioninfo['show'] = array(
				'heading' => 'Show',
				'info' => 'Show Top Downloads or Most Recent Downloads. Default if omitted: Top Downloads.'
			);
			$optioninfo['scope'] = array(
				'heading' => 'Scope',
				'info' => "The date range within which to draw the data. For <code>[stataway]</code> lists, <code>all</code> is not recommended if your site is high traffic. For <code>[stataway_user]</code> shortcodes, <code>all</code> is fine, since the results are limited to a single user."
			);
			$optioninfo['number'] = array(
				'heading' => 'Number to Show',
				'info' => "If omitted, all results within the scope will be displayed. Otherwise, enter an integer, e.g., <code>10</code> to show the top 10 downloads, or 10 most recent downloads."
			);
			$optioninfo['class'] = array(
				'heading' => 'CSS Class',
				'info' => "Optionally add your own CSS class (or multiple space-separated classes) to the containing element."
			);
			$optioninfo['username'] = array(
				'heading' => 'Username Column',
				'info' => "Whether to show or hide the username column in statistics tables."
			);
			$optioninfo['email'] = array(
				'heading' => 'User Email Column',
				'info' => "Whether to show or hide the user email column in statistics tables."
			);
			$optioninfo['ip'] = array(
				'heading' => 'IP Address Column',
				'info' => "Whether to show or hide the IP address column in statistics tables."
			);
			$optioninfo['agent'] = array(
				'heading' => 'User Agent Column',
				'info' => "Whether to show or hide the user agent column in statistics tables."
			);
			$optioninfo['output'] = array(
				'heading' => 'Output Type',
				'info' => "Choose whether to display the total number of download for the given user as a single integer (wrapped in a span element with the class of your choice), or to display an ordered or unordered list of the given user's file downloads, wrapped in a div with the class of your choice. Default: total"
			);
			$optioninfo['user'] = array(
				'heading' => 'User ID',
				'info' => "If left blank, the default will be the current logged in user. Otherwise, you may specify a single user id."
			);
			$optioninfo['timestamp'] = array(
				'heading' => 'Timestamp',
				'info' => "Whether to show or hide the timestamp on ordered/unordered list output. Default: hide."
			);
			$optioninfo['filecolumn'] = array(
				'heading' => 'File Column',
				'info' => "Whether to show the full path to the file plus the file name, or just the file name, in your statistics table's file column. Full path is the default, and is recommended to avoid confusion if more than one file in different directories has the same file name."
			);
			$this->optioninfo = $optioninfo;
		}
		public function helplinks()
		{
			$helplinks = array();
			$helplinks['rootdirectory'] = array(
				'heading' => 'Set Root Directory',
				'info' => "If your WordPress URL and Site URL are one and the same, you can disregard this setting. If your WordPress installation is in a subdirectory of your domain root directory, this option is for you. Choose whether your absolute path is relative to the WordPress Installation directory (default), or the domain root directory.<br><br>Note: if you choose the latter, be sure to refresh the Config page after changes finish saving, so the abspath in your Base Directory options will be updated to reflect your selection."
			);
			$helplinks['symlinks'] = array(
				'heading' => 'Allow Symlinks',
				'info' => "Disabled by default. If enabled, File Away will allow symlinks in your Base or Sub paths to validate. If you enable this setting, be sure you have measures in place to prevent against symlink attacks. File Away is not responsible for any unwanted eventualities."
			);			
			$helplinks['baseurl'] = array(
				'heading' => 'Base URL',
				'info' => "Determine the Base File-Download URL for your site."
			);	
			$helplinks['redirect'] = array(
				'heading' => 'Guest Redirect URL',
				'info' => "If you have enabled guest redirection in your shortcode, logged-out users will be redirected to the URL you specify here when they click on a download link. Your URL must begin with <code>http://</code> or <code>https://</code>"
			);				
			$helplinks['strictlogin'] = array(
				'heading' => 'Dynamic Username Matching',
				'info' => "See the tutorial on Dynamic Paths under the Tutorials tab for more info. Choose whether the <code>fa-username</code> dynamic path code will render logged-in users' names in a strict, case-sensitive manner, or forced lowercase. Default: Force Lowercase."
			);
			$helplinks['exclusions'] = array(
				'heading' => 'Permanent Exclusions',
				'info' => "A comma-separated list of filenames and/or file extensions you wish to permanently exclude from all lists and tables. Be sure to include the dot ( . ) if it's a file extension. (Not case sensitive.) Example: <br /><br /><code>My File Name, .bat, .php, My Other File Name</code>"
			);
			$helplinks['direxclusions'] = array(
				'heading' => 'Exclude Directories',
				'info' => "A comma-separated list of directory names you wish to permanently exclude from all Directory Tree tables and Recursive tables/lists, and from Manager Mode Bulk Action Destination generators. Do not include the forward slashes (\"/\") Example: <br /><br /><code>My Private Files, Weird_Server_Directory_Name, etc.</code>"
			);
			$helplinks['newwindow'] = array(
				'heading' => 'New Window',
				'info' => "By default, all file links in lists and tables are download links. If you want certain file types to open in a new window instead (e.g., .pdf or image files), add a comma-separated list of file extensions here for the file types you want to open in a new window. Be sure to include the dot ( . ). ( Not case sensitive. ) Example: <br /><br /><code>.pdf, .jpg, .png, .gif, .mp3, .mp4</code><br /><br />Also be aware that most file types will not open in a browser window."
			);
			$helplinks['banner_directory'] = array(
				'heading' => 'Banner Directory',
				'info' => "If you want to add banners to your File Away tables, start here by specifying the path to the directory where you will store your banner images and <code>fileaway-banner-parser.csv</code> file. For more information, see the Tutorials tab."
			);			
			$helplinks['encryption_key'] = array(
				'heading' => 'Encryption Key',
				'info' => "For File Away shortcode encrypted downloads. Use the randomly generated key provided, or set your own secure key, at least 16 characters in length, using upper and lowercase letters and numbers.<br><br>Note: If you want to generate a new random key, delete the existing key, Save Changes, and refresh the page. A new key will be generated for you."
			);
			$helplinks['download_prefix'] = array(
				'heading' => 'Bulk Download File Prefix',
				'info' => "For your bulk download zip file names. If this is not set, the current date will prefix the file name. Some sites have special characters or apostrophes or non-English characters in their site names that are rendered incorrectly when the zip file is created, causing the files not to download. If you experience this problem, change the prefix here to something that works for you, and does not prevent the downloads from being pushed to the browser."
			);						
			$helplinks['modalaccess'] = array(
				'heading' => 'Modal Access',
				'info' => "By user capability or role, choose who has access to the shortcode generator modal. <br><br>Default: edit_posts"
			);
			$helplinks['tmcerows'] = array(
				'heading' => 'Button Position',
				'info' => "Choose the position of the shortcode button on the TinyMCE panel. Default: Second Row"
			);
			$helplinks['adminstyle'] = array(
				'heading' => 'Admin Style',
				'info' => "Choose between classic (animated) or minimal admin style. Default: Classic."
			);
			$helplinks['loadusers'] = array(
				'heading' => 'Load Users',
				'info' => "Some websites have so many registered users (upwards of 20k, for instance) that loading all those users into dropdowns, or the Dynamic Paths table in the tutorial here, puts so much strain on their server that the File Away settings page cannot finish loading. Thus, by default, displaying lists of registered site users is disabled. If this isn't a problem for your setup, you can set Load Users to true."
			);
			$helplinks['stylesheet'] = array(
				'heading' => 'Stylesheet Placement',
				'info' => "Choose whether the stylesheet is enqueued in the header on all pages and posts, or in the footer only on pages and posts where any of the File Away shortcodes are used. For better performance, enqueuing to the footer is highly recommended, but if you are experiencing problems with the appearance of your displays on the page, try enqueuing to the header. <br><br>Default: Footer"
			);
			$helplinks['javascript'] = array(
				'heading' => 'Javascript Placement',
				'info' => "Choose whether the javascript is enqueued in the header on all pages and posts, or in the footer only on pages and posts where any of the File Away shortcodes are used. <br><br>Default: Header"
			);
			$helplinks['pathinfo'] = array(
				'heading' => 'Alternative Pathinfo Function',
				'info' => "Some users with certain UTF-8 encoded files may want to try enabling this alternative pathinfo function if some files do not show up in the file display."
			);
			$helplinks['daymonth'] = array(
				'heading' => 'Date Display Format',
				'info' => "Choose whether the Date Modified column in sortable tables displays the month or the date first. Default: MM/DD/YYYY"
			);
			$helplinks['postidcolumn'] = array(
				'heading' => 'Post ID Column',
				'info' => "Enables/disables the custom Post ID column added to 'All Posts' and 'All Pages.' When enabled, provides easy reference when displaying attachments from a post or page other than your current one. Default: Enabled"
			);
			$helplinks['custom_list_classes'] = array(
				'heading' => 'Custom List Classes',
				'info' => "Add a comma-separated list of your custom list classes. It needs to include the class name (minus the <code>ssfa-</code> prefix) and the display name for each comma-delimited class, and should look exactly like this:<br><br><code>classname1|Display Name 1, classname2|Display Name 2, classname3|Display Name 3</code>"
			);
			$helplinks['custom_table_classes'] = array(
				'heading' => 'Custom Table Classes',
				'info' => "Add a comma-separated list of your custom table classes. It needs to include the class name (minus the <code>ssfa-</code> prefix) and the display name for each comma-delimited class, and should look exactly like this:<br><br><code>classname1|Display Name 1, classname2|Display Name 2, classname3|Display Name 3</code><br><br>In the stylesheet, all of your table class names must be prefixed by <code>ssfa-</code>, but here you leave out the prefix. So, for instance, in the stylesheet it will look like this: <code>.ssfa-myclassname</code> but here it will look like this: <code>myclassname|My Display Name</code>. The shortcode will automatically add the prefix for you when you select your class in the shortcode generator."
			);
			$helplinks['custom_flightbox_classes'] = array(
				'heading' => 'Custom Flightbox Classes',
				'info' => "Add a comma-separated list of your custom Flightbox classes. It needs to include the class name (minus the <code>ssfa-</code> prefix) and the display name for each comma-delimited class, and should look exactly like this:<br><br><code>classname1|Display Name 1, classname2|Display Name 2, classname3|Display Name 3</code><br><br>In the stylesheet, all of your flightbox class names must be prefixed by <code>ssfa-</code>, but here you leave out the prefix. So, for instance, in the stylesheet it will look like this: <code>.ssfa-myclassname</code> but here it will look like this: <code>myclassname|My Display Name</code>. The shortcode will automatically add the prefix for you when you select your class in the shortcode generator."				
			);
			$helplinks['custom_color_classes'] = array(
				'heading' => 'Custom Color Classes',
				'info' => "Add a comma-separated list of your custom primary color classes. The primary color class affects the color of the file name (not hovered), the icon color, and the header. Your list needs to include the class name (minus the <code>ssfa-</code> prefix) and the display name for each comma-delimited class, and should look exactly like this (with your own color names of course):<br><br><code>turquoise|Turquoise, thistle|Thistle, salamander-orange|Salamander Orange</code><br><br>In the stylesheet, all of your primary color class names must be prefixed by <code>ssfa-</code>, but here you leave out the prefix. So, for instance, in the stylesheet it will look like this: <code>.ssfa-myclassname</code> but here it will look like this: <code>myclassname|My Display Name</code>. The shortcode will automatically add the prefix for you when you select your class in the shortcode generator."
			);
			$helplinks['custom_accent_classes'] = array(
				'heading' => 'Custom Accent Classes',
				'info' => "Add a comma-separated list of your custom accent color classes. The accent color class affects the color of the file name (on hover), the icon background color, and a few other little things. Your list needs to include the class name (minus the <code>accent-</code> prefix) and the display name for each comma-delimited class, and should look exactly like this (with your own color names of course):<br><br><code>turquoise|Turquoise, thistle|Thistle, salamander-orange|Salamander Orange</code><br><br>In the stylesheet, all of your accent color class names must be prefixed by <code>accent-</code>, but here you leave out the prefix. So, for instance, in the stylesheet it will look like this: <code>.accent-myclassname</code> but here it will look like this: <code>myclassname|My Display Name</code>. The shortcode will automatically add the prefix for you when you select your class in the shortcode generator."
			);
			$helplinks['custom_stylesheet'] = array(
				'heading' => 'Custom Stylesheet',
				'info' => "As an alternative to using the CSS editor here, you can create your own CSS file and drop it into the File Away Custom CSS directory here: <br><br><code>".WP_CONTENT_URL."/uploads/fileaway-custom-css</code><br><br>Then just enter the filename of the stylesheet into the custom stylesheet field.	<br><br>Keeping your custom stylesheet in the wp-content/uploads/fileaway-custom-css directory will ensure that your styles are never overwritten on plugin updates."
			);
			$helplinks['preserve_options'] = array(
				'heading' => 'Preserve on Uninstall',
				'info' => "Normally, your settings and custom CSS will be lost upon uninstallation of the plugin. Check this box to preserve your settings (i.e., if you plan to reinstall). Default: Preserve"
			);
			$helplinks['reset_options'] = array(
				'heading' => 'Reset to Defaults',
				'info' => "Warning: If you choose to reset on save, any custom CSS in the CSS editor will be erased. Might want to back it up before hitting save."
			);
			$helplinks['manager_role_access'] = array(
				'heading' => 'Manager Mode: Permanent User Role / Capability Access',
				'info' => "Specify which user roles / capabilities will have access to Manager Mode on File Away tables. Manager mode allows users to rename and delete individual files, and to copy, move, and delete files in bulk. Only those with the roles or capabilities specified here will have access to the Manager Mode settings on the shortcode generator modal (if they already have access to the modal) and actual access to Manager Mode on the front-end page. Site administrators will have access to manager mode regardless of the specifications set here. The settings here are permanent. Additional roles and caps can be granted access on a per-shortcode basis (see the help link next to \"Override Password\" below).  <br><br>Default: Administrator"
			);
			$helplinks['manager_user_access'] = array(
				'heading' => 'Manager Mode: Permanent User Access',
				'info' => "A comma-separated list of user IDs. Specify which specific users will have access to Manager Mode on File Away tables. This setting should be used in case a specific user merits access to Manager Mode who does not have one of the user roles / capabilities specified in the above setting. Manager mode allows users to rename and delete individual files, and to copy, move, and delete files in bulk. Individual users specified here will have access to the Manager Mode settings on the shortcode generator modal (if they already have access to the modal) and actual access to Manager Mode on the front-end page. The settings here are permanent. Additional users can be granted access on a per-shortcode basis (see the help link next to \"Override Password\" below). <br><br>Default: None"
			);
			$helplinks['managerpassword'] = array(
				'heading' => 'Manager Mode: Override Password',
				'info' => "Set an override password here, then use the password in your [fileaway] shortcode if you wish to grant front-end Manager Mode access to additional roles or individual users (by identifying their user_id) on a per-shortcode basis. Your File Away shortcode would need to look something like: <br><br><code>[fileaway manager=\"on\" password=\"yourpassword\" role_override=\"author,subscriber\"]</code> or <br><br><code>[fileaway manager=\"on\" password=\"yourpassword\" user_override=\"125,214\"]</code><br><br>In place of using actual roles or user ids in the override shortcode attributes, you can elect to use <code>fa-userrole</code> or <code>fa-userid</code> like this: <br><br><code>[fileaway manager=\"on\" password=\"yourpassword\" role_override=\"fa-userrole\"]</code> or <br><br><code>[fileaway manager=\"on\" password=\"yourpassword\" user_override=\"fa-userid\"]</code><br><br> Be aware that doing this will effectively grant Manager Mode access to all logged in users. Thus, the dynamic role and user id codes should only be used on File Away tables where the directory paths are dynamic. This will grant users access to rename, copy, move, and delete files within the confines of their of own subdirectories. "
			);
			$helplinks['stats'] = array(
				'heading' => 'Download Statistics',
				'info' => "If enabled, <code>[fileaway]</code> lists/tables and <code>[stataway]</code> lists will by default collect download statistics on each user download. Download statistics can also be enabled on <code>[stataway]</code> data tables if desired. For each download, an entry will be added to a custom SQL database table, which will include the following information: <code>TIMESTAMP, FILE, USER ID, USER EMAIL, USER IP ADDRESS, USER AGENT</code>.<br><br>NOTE: If files are moved or renamed using a File Away Manager Mode table, the file's new name and/or location will be updated in all previous records in the statistics database, preserving the statistics for that file. This only obtains if the file is renamed or moved using a File Away Manager Mode table.<br><br>NOTE: Download statistics will not be gathered when the user downloads a file from a File Away RSS Feed. Thus it is recommended that your read the tutorial on RSS Dir-to-URL Mapping."
			);
			$helplinks['ignore_roles'] = array(
				'heading' => 'Ignore Roles/Capabilities',
				'info' => "A comma-separated list of user roles and/or user capabilities for whom download statistics will never be gathered."
			);
			$helplinks['ignore_users'] = array(
				'heading' => 'Ignore Users',
				'info' => "A comma-separated list of user IDs for whom download statistics will never be gathered."
			);
			$helplinks['recordlimit'] = array(
				'heading' => 'Download Record Limit',
				'info' => "Leave blank for unlimited. Otherwise, enter an integer. If the number of records in the database grows larger than the limit specified, the oldest records over the limit will be deleted from the database every six hours. This, or Record Lifespan, is recommended for sites with a high volume of downloads. Note that a CSV file of all records from a specified period can be downloaded using the <code>[stataway type=\"table\"]</code> shortcode."
			);
			$helplinks['recordlifespan'] = array(
				'heading' => 'Download Record Lifespan',
				'info' => "You may specify how long records should be stored in the database. To disable record lifespans, set to <code>Eternal Life</code>. If enabled, the table will be checked once daily for records older than the allowed lifespan.  This, or Record Limit, is recommended for sites with a high volume of downloads. Note that a CSV file of all records from a specified period can be downloaded using the <code>[stataway type=\"table\"]</code> shortcode."
			);
			$helplinks['instant_stats'] = array(
				'heading' => 'Instant Download Notifications',
				'info' => "If enabled, and if Sender Email and Recipient Email(s) are specified, the recipients will receive an email notification every time a file is downloaded."
			);
			$helplinks['instant_sender_name'] = array(
				'heading' => 'Sender Name for Instant Email Notifications',
				'info' => "Your site name, or the administrator name, or whatever you desire."
			);
			$helplinks['instant_sender'] = array(
				'heading' => 'Sender Email for Instant Email Notifications',
				'info' => "Required for Instant Notifications. The single email address from which instant notifications will be sent."
			);
			$helplinks['instant_recipients'] = array(
				'heading' => 'Recipient Email(s) for Instant Email Notifications',
				'info' => "Required for Instant Notifications. A comma-separated list of email addresses of recipients of instant download notification emails."
			);
			$helplinks['instant_subject'] = array(
				'heading' => 'Instant Email Notification Subject',
				'info' => "You may use the following variables for instant email subjects: <code>%blog%</code> (replaced with site name), <code>%file%</code> (replaced with file name), <code>%datetime%</code> (replaced with download time stamp). Use of these variables is optional."
			);
			$helplinks['compiled_stats'] = array(
				'heading' => 'Compiled File Download Report Emails',
				'info' => "If enabled, and if Sender Email and Recipient Email(s) are specified, the recipients will receive a compiled periodic report of file downloads from the period specified, at the end of the period specified. Options are daily, weekly, and fortnightly (every two weeks). Note that WordPress cron jobs are only triggered if the site is hit after the scheduled event. Thus, if you do not have a high traffic site, you may wish to set up custom cron jobs with your hosting provider, to hit your site at the interval you desire."
			);
			$helplinks['compiled_sender_name'] = array(
				'heading' => 'Sender Name for Compiled Email Reports',
				'info' => "Your site name, or the administrator name, or whatever you desire."
			);
			$helplinks['compiled_sender'] = array(
				'heading' => 'Sender Email for Compiled Email Reports',
				'info' => "Required for Compiled Notifications. The single email address from which periodic notifications will be sent."
			);
			$helplinks['compiled_recipients'] = array(
				'heading' => 'Recipient Email(s) for Compiled Email Reports',
				'info' => "Required for Compiled Notifications. A comma-separated list of email addresses of recipients of compiled download reports."
			);
			$helplinks['compiled_subject'] = array(
				'heading' => 'Compiled Email Report Subject',
				'info' => "You may use the following variables for compiled report email subjects: <code>%blog%</code> (replaced with site name), <code>%dates%</code> (replaced with the beginning and end dates of the periodic report). Use of these variables is optional."
			);
			$helplinks['feeds'] = array(
				'heading' => 'Feed Storage Directory',
				'info' => "The directory where all of your feeds will be stored. If this field is blank, RSS is effectively disabled in File Away, though emptying this field will not delete existing feeds if they have already been created. It will just prevent further feed updates.<br><br>It is best to put this directory in your domain root, for instance, directly in your <code>public_html</code> directory. You might call the storage directory <code>rss</code> or <code>feeds</code>. It's up to you. If the directory you specify does not exist, it will be created for you the first time feeds are generated. The feeds from all your monitored directories will be stored in this folder. An example of a subscription link would be: <code>www.yourdomain.com/rss/_feed_154271782d4578.xml</code>. The feed file names are automatically generated the first time a feed is generated for them, from a microtime stamp, which allows you to rename or move directories without their feed locations being affected.<br><br>You can store a global channel logo image for all your feeds in this storage directory. It can be either a png, a jpg, or a gif. It must be named: <code>fa-feed-logo.jpg</code> or .png, .gif, depending. If an image with this name is stored in your Feed Storage Directory, RSS readers that look for channel images will display it at the top of the feed. (Firefox's built-in reader, for instance, will display this image.) This will be the default channel image for all feeds, but you can override this image with another, for specific feeds, by placing a different image of the same filename, inside any monitored directory."
			);	
			$helplinks['basefeeds'] = array(
				'heading' => 'Monitored Directories',
				'info' => "These are relative to your domain root, not your WordPress install directory (if the two are different). You can add as many monitored directories as you like, but be aware that feeds will by default be generated for each sub-directory, recursively, of each monitored directory specified. So for example, adding two monitored directories like this would not be necessary:<br><br><code>public_html/files/</code><br><code>public_html/files/images</code><br><br>The latter of those two will already be monitored by default because it is a subdirectory of the first directory specified. So rather the following would be appropriate: <br><br><code>public_html/files</code><br><code>public_html/wp-content/uploads/</code><br><br>Again, you can add as many monitored base directories as you like, but by default all sub-directories will have individual feeds generated for them as well. You can, however, disable monitoring for a specific sub-directory by adding it to an instance of Excluded Sub-Directories (below)."
			);
			$helplinks['excluded_feeds'] = array(
				'heading' => 'Excluded Sub-Directories',
				'info' => "Say you have a Monitored directory like <code>files</code>, and that directory has five subdirectories, <code>images, pdfs, videos, audio, docs</code>. You want to monitor the first four sub-directories, but not the <code>docs</code> directory. In that case, you would add <code>files/docs</code> to a new Excluded Sub-Directory. You can have as many excluded sub-directories as you like. Just click on the <code>Add Another</code> button for each one.<br><br>If you add a directory to the excluded directories, it will delete that directory's feed if a feed already exists for that directory.<br><br>If you want to delete all feeds for a specified Monitored Directory and all its sub-directories, add the same directory to the Excluded Sub-Directories, but leave it in the Monitored Directories. Then manually update your feeds. All feeds for that directory will be deleted. You may then remove that directory from both your Monitored Directories, and your Excluded Sub-Directories, and save again."
			);
			$helplinks['feed_excluded_exts'] = array(
				'heading' => 'Excluded File Extensions',
				'info' => "A comma-separated list of file extensions that will never show up in any feeds. The file extensions <code>.php</code> and <code>.ini</code> are always excluded from feeds. File extension matching is not case sensitive."
			);
			$helplinks['feed_excluded_files'] = array(
				'heading' => 'Excluded Strings',
				'info' => "A comma-separated list of strings that, if found anywhere in any file name, will result in the exclusion of that file from the feed. String-matching in this case is case-sensitive."
			);		
			$helplinks['feedlimit'] = array(
				'heading' => 'Feed Limit',
				'info' => "If left blank, the feed for each directory will always show all files that are not otherwise excluded, with the most recent at the top. I.e., blank = unlimited. If you specify an integer for a limit, for instance, 50, only the 50 most recent files will appear in your feeds at any given time."
			);
			$helplinks['feeddates'] = array(
				'heading' => 'Show Dates Modified',
				'info' => "By default, enabled. If you have files that are over 2GB on a 32-bit server, you will want to disable this setting."
			);
			$helplinks['feedsize'] = array(
				'heading' => 'Show File Size',
				'info' => "By default, enabled. If you have files that are over 2GB on a 32-bit server, you will want to disable this setting."
			);
			$helplinks['feedlinks'] = array(
				'heading' => 'Direct File Links',
				'info' => "By default, enabled. If direct file links are disabled, the file links will be replaced either with your website url, or with the mapped page location for the file's directory, specified in your <code>fa-directory-map.csv</code> placed in your Feed Storage Directory. (See tutorial for details.)"
			);			
			$helplinks['recursivefeeds'] = array(
				'heading' => 'Feeds within Feeds',
				'info' => "If enabled, if a directory contains subdirectories that also have feeds, links to those feeds will be included in a parent feed."
			);			
			$helplinks['feedinterval'] = array(
				'heading' => 'Auto Feed Update Frequency',
				'info' => "Specify how often your feeds will be updated. This is based on a WordPress cron job, which will only run if someone has hit any page on your website after the next job is scheduled. For high-traffic sites, this is fine. But you may want to set up a custom cron job with your Web Host (e.g., in Cpanel) that is set to hit your WordPress site once every so often. An example of that would look like this:<br><br><code>*/10	*&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;&nbsp;&nbsp;&nbsp;wget -q -O – http://yourdomain.com/wp-cron.php?doing_wp_cron</code><br><br>A command like this will hit your WordPress install's cron mechanism every 10 minutes, reminding it to check for scheduled jobs."
			);
			$helplinks['updatefeeds'] = array(
				'heading' => 'Manual Feed Updates',
				'info' => "Enabling manual feed updates will not disable the automated feed updates. It will just make it so that all your feeds will be updated whenever you click on \"Save Changes\" on any tab of the File Away settings page."
			);
			$this->helplinks = $helplinks;
		}
		public function tutorial($shortcode)
		{
			if(!$shortcode) return false;
			$atts = new fileaway_attributes;
			$array = $atts->shortcodes[$shortcode];
			$hastypes = isset($atts->shortcodes[$shortcode]['type']) ? true : false;
			$typeheader = $hastypes ? '<th>for&nbsp;type</th>' : null;
			$output = 
				'<table id="fileaway-table" class="fileaway-minimalist fileaway-left fileaway-attributes" style="display: table;">'.
					'<thead><tr>'.
						'<th class="fileaway-minimalist-first-column">attribute</th>'.
						$typeheader.
						'<th style="width:275px!important;">acceptable&nbsp;values</th>'.
						'<th class="fileaway-minimalist-last-column">notes</th>'.
					'</tr></thead>'.
					'<tbody>';
			foreach($array as $key=>$a)
			{	
				$typecol = null;
				$values = array();
				if($hastypes)
				{
					$types = array();								
					if($key != 'type')
					{
						foreach($array[$key] as $type=>$ar)
						{ 
							if($type == 'default' || $type == 'options') continue;
							$types[] = $type;	
						}
						$typecol = '<td>'.implode(', ', $types).'</td>';									
						foreach($types as $type)
						{
							$opts = isset($array[$key][$type]['options']) && $array[$key][$type]['options']
								? $array[$key][$type]['options'] 
								: array('User Defined' => 1);
							$o = array();
							foreach($opts as $k=>$opt)
							{
								if($k == '')
								{ 
									$k = isset($array[$key][$type]['default']) 
										? $array[$key][$type]['default']
										: false;	
								}
								if($k) $o[] = $k;	
							}
							$values[] = 'For '.$type.'s:<br>'.implode(', ', $o);	
						}
						if(isset($values[1]) && isset($types[1]))
						{ 
							if(str_replace('For '.$types[0].'s:<br>', 'For '.$types[1].'s:<br>', $values[0]) === $values[1])
								$values = array(str_replace('For '.$types[0].'s:<br>', '', $values[0]));
						}
						elseif(isset($types[0]))
							$values = array(str_replace('For '.$types[0].'s:<br>', '', $values[0]));
					}
					else 
					{
						foreach($array['type']['options'] as $k=>$v) $types[] = $k == '' ? $array['type']['default'] : $k;
						$values[] = implode(', ', $types);
						$typecol = '<td></td>';
					}
				}
				else
				{
					$opts = isset($array[$key]['options']) && $array[$key]['options']
						? $array[$key]['options'] 
						: array('User Defined' => 1);
					$o = array();
					foreach($opts as $k=>$opt)
					{
						if($k == '')
						{ 
							$k = isset($array[$key]['default']) 
								? $array[$key]['default']
								: false;	
						}
						if($k) $o[] = $k;	
					}
					$values[] = implode(', ', $o);
				}
				$details = isset($this->optioninfo[$key]['info']) ? $this->optioninfo[$key]['info'] : null;
				$output .= 
					'<tr>'.
						'<td class="fileaway-minimalist-first-column">'.$key.'</td>'.
						$typecol.
						'<td>'.implode('<br><br>', $values).'</td>'.
						'<td class="fileaway-minimalist-last-column">'.$details.'</td>'.
					'</tr>';
			}
			$output .= '</tbody></table><br><br>';	
			return $output;
		}
	}
}