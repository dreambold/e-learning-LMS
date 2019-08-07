<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(!class_exists('fileaway_attributes'))
{
	class fileaway_attributes
	{
		public $op;
		public $classes;
		public $shortcodes;
		public $fileaway;
		public $attachaway;
		public $fileup;
		public $fileaway_values;
		public $fileaframe;
		public $formaway_open;
		public $formaway_row;
		public $formaway_cell;
		public $formaway_close;
		public $stataway;
		public $statawat_user;
		public $fileaway_tutorials;
		public function __construct()
		{
			$this->op = get_option('fileaway_options');
			$this->classes = array(
				'lists'=>array(),
				'tables'=>array(),
				'flightboxes'=>array(),
				'colors'=>array(),
				'accents'=>array()
			);
			$this->classes();
			$this->shortcodes = array(
				'fileaway'=>array(),
				'attachaway'=>array(),
				'fileup'=>array(),
				'fileaway_values'=>array(),
				'fileaframe'=>array(),
				'formaway_open'=>array(),
				'formaway_row'=>array(),
				'formaway_cell'=>array(),
				'formaway_close'=>array(),
				'stataway'=>array(),
				'stataway_user'=>array(),
				'fileaway_tutorials'=>array(),
			);
			$this->handle();
			$this->fileaway = $this->atts('fileaway');
			$this->attachaway = $this->atts('attachaway');
			$this->fileup = $this->atts('fileup');
			$this->fileaway_values = $this->atts('fileaway_values');
			$this->fileaframe = $this->atts('fileaframe');
			$this->formaway_open = $this->atts('formaway_open');
			$this->formaway_row = $this->atts('formaway_row');
			$this->formaway_cell = $this->atts('formaway_cell');
			$this->formaway_close = $this->atts('formaway_close');
			$this->stataway = $this->atts('stataway');
			$this->stataway_user = $this->atts('stataway_user');
			$this->fileaway_tutorials = $this->atts('fileaway_tutorials');
			add_filter('the_content', array($this, 'autofix'));
			add_filter('the_excerpt', array($this, 'autofix'));
		}
		public function base($no_s2mem = false)
		{
			$options = array(); $op = $this->op;
			if($op['base1'] && $op['bs1name']) $options[''] = $op['bs1name'];
			if($op['base2'] && $op['bs2name']) $options['2'] = $op['bs2name'];
			if($op['base3'] && $op['bs3name']) $options['3'] = $op['bs3name'];
			if($op['base4'] && $op['bs4name']) $options['4'] = $op['bs4name'];
			if($op['base5'] && $op['bs5name']) $options['5'] = $op['bs5name'];
			if(!$no_s2mem && fileaway_definitions::$s2member) $options['s2member-files'] = 's2member-files';
			return $options;
		}
		public function classes()
		{
			$lists = $this->op['custom_list_classes']; 
			$tables = $this->op['custom_table_classes'];
			$flightboxes = $this->op['custom_flightbox_classes'];
			$accents = $this->op['custom_accent_classes'];
			$colors = $this->op['custom_color_classes']; 
			$accents = $this->op['custom_accent_classes'];
			$lists = !$lists || $lists == '' ? false : preg_split("/(,\s|,)/", preg_replace('/\s+/', ' ', $lists), -1, PREG_SPLIT_NO_EMPTY);
			$tables = !$tables || $tables == '' ? false : preg_split("/(,\s|,)/", preg_replace('/\s+/', ' ', $tables), -1, PREG_SPLIT_NO_EMPTY);
			$flightboxes = !$flightboxes || $flightboxes == '' ? false : preg_split("/(,\s|,)/", preg_replace('/\s+/', ' ', $flightboxes), -1, PREG_SPLIT_NO_EMPTY);
			$colors = !$colors || $colors == '' ? false : preg_split("/(,\s|,)/", preg_replace('/\s+/', ' ', $colors), -1, PREG_SPLIT_NO_EMPTY);
			$accents = !$accents || $accents == '' ? false : preg_split("/(,\s|,)/", preg_replace('/\s+/', ' ', $accents), -1, PREG_SPLIT_NO_EMPTY);
			$this->classes['lists'] = $this->classcleaner($lists);
			$this->classes['tables'] = $this->classcleaner($tables);
			$this->classes['flightboxes'] = $this->classcleaner($flightboxes);
			$this->classes['colors'] = $this->classcleaner($colors);
			$this->classes['accents'] = $this->classcleaner($accents);
		}
		public function classcleaner($classes)
		{
			if(!$classes || !is_array($classes)) return false;
			$newclasses = array();
			foreach($classes as $c)
			{
				list($class, $label) = preg_split ("/(\|)/", $c);
				$class = trim($class, ' '); $label = trim($label, ' ');
				if($class != '') $newclasses[$class] = $label;
			}
			return $newclasses;
		}
		public function colors($type)
		{
			$primary = array(
				'black' => 'Black',
				'silver' => 'Silver',
				'red' => 'Red',
				'blue' => 'Blue',
				'green' => 'Green',
				'brown' => 'Brown',
				'orange' => 'Orange',
				'purple' => 'Purple',
				'pink' => 'Pink',
			);
			if($type === 'matched')
			{
				$accents = $this->classes['accents'] ? array_merge($primary, $this->classes['accents']) : $primary;	
				$output = array_merge(array('' => 'Matched'), $accents);
				return $output;
			}
			else
			{
				$colors = $this->classes['colors'] ? array_merge($primary, $this->classes['colors']) : $primary;
			}
			if($type === 'classic')
			{
				$output = array_merge(array('' => 'Classic', 'random' => 'Random'), $colors);
			}
			else $output = array_merge(array('' => 'Random'), $colors);
			return $output;
		}
		public function styles($type)
		{
			if($type === 'lists')
			{
				$primary = array('' => 'Minimal-List', 'silk' => 'Silk');
				$styles = $this->classes['lists'] ? array_merge($primary, $this->classes['lists']) : $primary;
			}
			elseif($type === 'flightboxes')
			{
				$primary = array('' => 'Minimallist', 'silver-bullet' => 'Silver Bullet', 'yin' => 'Yin', 'yang' => 'Yang');
				$styles = $this->classes['flightboxes'] ? array_merge($primary, $this->classes['flightboxes']) : $primary;
			}
			else
			{
				$primary = array('' => 'Minimalist', 'silver-bullet' => 'Silver Bulllet', 'greymatter' => 'Grey Matter', 'whitestripes' => 'White Stripes');
				$styles = $this->classes['tables'] ? array_merge($primary, $this->classes['tables']) : $primary;
			}
			return $styles;
		}
		public function filegroups()
		{
			$filegroups = array();
			$defs = new fileaway_definitions;
			foreach($defs->filegroups as $value => $array) 
				$filegroups[$value] = $array[0];
			unset($filegroups['unknown']);
			return $filegroups;
		}
		public function handle($handler = false)
		{			
			$base = $this->base(); 
			$upbase = $this->base();
			$csvbase = $this->base();
			$liststyles = $this->styles('lists');
			$tablestyles = $this->styles('tables');
			$flightboxstyles = $this->styles('flightboxes');
			$random = $this->colors('random');
			$matched = $this->colors('matched');
			$classic = $this->colors('classic');
			$filegroups = $this->filegroups();
			$roles = fileaway_utility::caps();
			$all = $handler && in_array($handler, $this->shortcodes) ? false : true;
			if($all || $handler == 'fileaway')
			{
				$this->shortcodes['fileaway'] = array(
					// Config
					'type' => array(
						'default' => 'list',
						'options' => array(
							'' => 'Sorted List',
							'table' => 'Sortable Data Table'
						),
					),
					'base' => array(
						'list' => array(
							'default' => '1',
							'options' => $base,
						),
						'table'	=> array(
							'default' => '1',
							'options' => $base,
						),
					),
					'sub' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => false,
							'options' => false,
						),					
					),
					'makedir' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'true' => 'Enabled'
							),
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'true' => 'Enabled'
							),
						),
					),
					'name' => array(
						'table' => array(
							'default' => false,
							'options' => false,
						),							
					),
					'paginate' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'true' => 'Enabled',
							),
							'binary' => 'true',
						),
					),
					'pagesize' => array(
						'table' => array(
							'default' => '15',
							'options' => false,
						),
					),
					'search' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Enabled',
								'no' => 'Disabled',
							),
						),
					),
					'searchlabel' => array(
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),
					'filenamelabel' => array(
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),
					'datelabel' => array(
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),
					'customdata' => array(
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),
					'metadata' => array(
						'table' => array(
							'default' => '',
							'options' => array(
								'' => 'In File Name',
								'database' => 'In Database', 
							),
							'binary' => 'database',
						),
					),
					'sortfirst' => array(
						'table' => array(
							'default' => 'filename',
							'options' => array(
								'' => 'Filename ASC',
								'filename-desc' => 'Filename DSC', 
								'type' => 'Filetype ASC', 
								'type-desc' => 'Filetype DSC', 
								'custom' => 'Custom Column ASC', 
								'custom-desc' => 'Custom Column DSC', 
								'mod' => 'Date Modified ASC', 
								'mod-desc' => 'Date Modified DSC', 
								'size' => 'Filesize ASC', 
								'size-desc' => 'Filesize DSC',
								'disabled' => 'Disable Sorting',
							),
						),
					),
					'mod' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Hide',
								'yes' => 'Show'
							),
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Show',
								'no' => 'Hide'
							),
						),
					),
					'size' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Show',
								'no' => 'Hide'
							),
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Show',
								'no' => 'Hide'
							),
						),
					),
					'nolinks' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'False',
								'yes' => 'True'
							),
							'binary' => 'yes',
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'False',
								'yes' => 'True'
							),
							'binary' => 'yes',
						),
					),
					'redirect' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'true' => 'Enabled'
							),
							'binary' => 'true',
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'true' => 'Enabled'
							),
							'binary' => 'true',
						),
					),					
					'showrss' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'False',
								'true' => 'True'
							),
							'binary' => 'true',
						),
					),					
					'fadein' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'opacity' => 'Opacity Fade',
								'display' => 'Display Fade'
							),
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'opacity' => 'Opacity Fade',
								'display' => 'Display Fade'
							),
						),
					),
					'fadetime' => array(
						'list' => array(
							'default' => '1000',
							'options' => array(
								'500' => '500',
								'' => '1000',
								'1500' => '1500',
								'2000' => '2000'
							),
						),
						'table' => array(
							'default' => '1000',
							'options' => array(
								'500' => '500',
								'' => '1000',
								'1500' => '1500',
								'2000' => '2000'
							),
						),
					),	
					'class' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),				
					'debug' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'on' => 'Enabled'
							),
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'on' => 'Enabled'
							),
						),
					),
					's2skipconfirm' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Confirmations On',
								'true' => 'Confirmations Off'
							),
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Confirmations On',
								'true' => 'Confirmations Off'
							),
						),
					),
					// Modes
					'stats' => array(
						'list' => array(
							'default' => 'true',
							'options' => array(
								'' => 'Enabled',
								'false' => 'Disabled',
							)
						),
						'table' => array(
							'default' => 'true',
							'options' => array(
								'' => 'Enabled',
								'false' => 'Disabled',
							),
						),
					),
					'bulkdownload' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'on' => 'Enabled'
							),
							'binary' => 'on',
						),
					),
					'playback' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'compact' => 'Compact',
								'extended' => 'Extended',
							),
							'binary' => 'compact',
						),
					),
					'playbackpath' => array(
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),
					'playbacklabel' => array(
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),
					'onlyaudio' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'true' => 'Enabled'
							),
							'binary' => 'true',
						),
					),
					'loopaudio' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'true' => 'Enabled'
							),
							'binary' => 'true',
						),
					),					
					'flightbox' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'images' => 'Images',
								'videos' => 'Videos',
								'pdfs' => 'PDFs',
								'multi' => 'Multi-Media'
							),
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'images' => 'Images',
								'videos' => 'Videos',
								'pdfs' => 'PDFs',
								'multi' => 'Multi-Media'
							),
						),
					),
					'boxtheme' => array(
						'list' => array(
							'default' => 'minimalist',
							'options' => $flightboxstyles,
						),
						'table' => array(
							'default' => 'minimalist',
							'options' => $flightboxstyles,
						),
					),
					'nolinksbox' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Enable Downloads',
								'true' => 'Disable Downloads'
							),
							'binary' => 'true',
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Enable Downloads',
								'true' => 'Disable Downloads'
							),
							'binary' => 'true',
						),
					),										
					'maximgwidth' => array(
						'list' => array(
							'default' => '1920',
							'options' => false,
						),
						'table' => array(
							'default' => '1920',
							'options' => false,
						),							
					),
					'maximgheight' => array(
						'list' => array(
							'default' => '1080',
							'options' => false,
						),
						'table' => array(
							'default' => '1080',
							'options' => false,
						),							
					),
					'videowidth' => array(
						'list' => array(
							'default' => '1920',
							'options' => false,
						),
						'table' => array(
							'default' => '1920',
							'options' => false,
						),
					),
					'encryption' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'on' => 'Enabled'
							),
							'binary' => 'on',
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'on' => 'Enabled'
							),
							'binary' => 'on',
						),
					),
					'recursive' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'on' => 'Enabled'
							),
							'binary' => 'on',
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'on' => 'Enabled'
							),
							'binary' => 'on',
						),
					),
					'directories' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'on' => 'Enabled'
							),
							'binary' => 'on',
						),
					),
					'manager' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'on' => 'Enabled'
							),
							'binary' => 'on',
						),
					),
					'drawerid' => array(
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),
					'excludedirs' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),
					'onlydirs' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),
					'drawericon' => array(
						'table' => array(
							'default' => 'drawer',
							'options' => array(
								'' => 'Drawer',
								'drawer-2' => 'Drawer Alt',
								'book' => 'Book',
								'cabinet' => 'Cabinet',
								'console' => 'Console',
							),
						),
					),
					'drawerlabel' => array(
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),
					'parentlabel' => array(
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),					
					'password' => array(
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),
					'user_override' => array(
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),
					'role_override' => array(
						'table' => array(
							'default' => 'skip',
							'options' => $roles,
						),
					),
					'dirman_access' => array(
						'table' => array(
							'default' => 'skip',
							'options' => $roles,
						),
					),
					// Filters
					'exclude' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => false,
							'options' => false,
						),							
					),
					'include' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => false,
							'options' => false,
						),							
					),
					'only' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => false,
							'options' => false,
						),							
					),
					'images' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Include',
								'only' => 'Only',
								'none' => 'Exclude',
							),
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Include',
								'only' => 'Only',
								'none' => 'Exclude',
							),
						),
					),
					'code' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Exclude',
								'yes' => 'Include',
							),
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Exclude',
								'yes' => 'Include',
							),
						),
					),
					'show_wp_thumbs' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Hide',
								'true' => 'Show',
							),
							'binary' => 'true',
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Hide',
								'true' => 'Show',
							),
							'binary' => 'true',
						),
					),					
					'devices' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'All Devices',
								'desktop' => 'Desktops/Notebooks',
								'mobile' => 'Mobiles/Tablets',
							),
						),
						'table' => array(
							'options' => array(
								'' => 'All Devices',
								'desktop' => 'Desktops/Notebooks',
								'mobile' => 'Mobiles/Tablets',
							),						
						),
					),
					'limit' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),	
					'limitby' => array(
						'list' => array(
							'default' => 'random',
							'options' => array(
								'' => 'Random',
								'oldest' => 'Oldest',
								'mostrecent' => 'Most Recent',
								'alpha' => 'Alpha Asc',
								'alpha-desc' => 'Alpha Desc',
							),
						),
						'table' => array(
							'default' => 'random',
							'options' => array(
								'' => 'Random',
								'mostrecent' => 'Most Recent',
								'oldest' => 'Oldest',
								'alpha' => 'Alpha Asc',
								'alpha-desc' => 'Alpha Desc',
							),
						),
					),															
					'showto' => array(
						'list' => array(
							'default' => 'skip',
							'options' => $roles,
						),
						'table' => array(
							'default' => 'skip',
							'options' => $roles,
						),							
					),
					'hidefrom' => array(
						'list' => array(
							'default' => 'skip',
							'options' => $roles,
						),
						'table' => array(
							'default' => 'skip',
							'options' => $roles,
						),							
					),
					// Styles
					'theme' => array(
						'list' => array(
							'default' => 'minimal-list',
							'options' => $liststyles,
						),
						'table' => array(
							'default' => 'minimalist',
							'options' => $tablestyles,
						),
					),
					'heading' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => false,
							'options' => false,
						),							
					),					
					'width' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => '100',
							'options' => false,
						),
					),
					'perpx' => array(
						'list' => array(
							'default' => '%',
							'options' => array(
								'' => 'Percent',
								'px' => 'Pixels',
							),
						),
						'table' => array(
							'default' => '%',
							'options' => array(
								'' => 'Percent',
								'px' => 'Pixels',
							),
						),
					),
					'align' => array(
						'list' => array(
							'default' => 'left',
							'options' => array(
								'' => 'Left',
								'right' => 'Right',
								'none' => 'None',
							),
						),
						'table' => array(
							'default' => 'left',
							'options' => array(
								'' => 'Left',
								'right' => 'Right',
								'none' => 'None',
							),
						),
					),
					'textalign' => array(
						'table' => array(
							'default' => 'center',
							'options' => array(
								'' => 'Center',
								'left' => 'Left',
								'right' => 'Right',
							),
						),
					),
					'hcolor' => array(
						'list' => array(
							'default' => false,
							'options' => $random,
						),
						'table' => array(
							'default' => false,
							'options' => $random,
						),
					),
					'color' => array(
						'list' => array(
							'default' => false,
							'options' => $random,
						),
						'table' => array(
							'default' => false,
							'options' => $classic,
						),					
					),
					'accent' => array(
						'list' => array(
							'default' => false,
							'options' => $matched,
						),
					),
					'iconcolor' => array(
						'list' => array(
							'default' => false,
							'options' => $random,
						),
						'table' => array(
							'default' => false,
							'options' => $classic,
						),
					),
					'icons' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Filetype',
								'paperclip' => 'Paperclip',
								'none' => 'None',
							),
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Filetype',
								'paperclip' => 'Paperclip',
								'none' => 'None',
							),
						),
					),
					'prettify' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Enabled',
								'off' => 'Disabled',
							),
							'binary' => 'off',
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Enabled',
								'off' => 'Disabled',
							),
							'binary' => 'off',
						),
					),
					'corners' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Rounded',
								'sharp' => 'Sharp',
								'roundtop' => 'Rounded Top',
								'roundbottom' => 'Rounded Bottom',
								'roundleft' => 'Rounded Left',
								'roundright' => 'Rounded Right',
								'elliptical' => 'Elliptical'
							),
						),
					),
					'display' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Vertical',
								'inline' => 'Side-by-Side',
								'2col' => 'Two Columns',
							),
						),
					),
					'thumbnails' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'transient' => 'Transient',
								'permanent' => 'Permanent',
							),
							'binary' => 'transient',
						),
					),
					'thumbsize' => array(
						'table' => array(
							'default' => 'small',
							'options' => array(
								'' => 'Small',
								'medium' => 'Medium',
								'large' => 'Large',
							),
						),
					),
					'thumbstyle' => array(
						'table' => array(
							'default' => 'widerounded',
							'options' => array(
								'' => 'Wide-Rounded',
								'widesharp' => 'Wide-Sharp',
								'squarerounded' => 'Square-Rounded',
								'squaresharp' => 'Square-Sharp',
								'oval' => 'Oval',
								'circle' => 'Circle'
							),
						),
					),
					'graythumbs' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'None',
								'true' => 'Grayscale',
							),
						),
					),
					'maxsrcbytes' => array(
						'table' => array(
							'default' => '1887436.8',
							'options' => false,
						),
					),
					'maxsrcheight' => array(
						'table' => array(
							'default' => 2500,
							'options' => false,
						),
					),
					'maxsrcwidth' => array(
						'table' => array(
							'default' => 3000,
							'options' => false,
						),
					),
					// Bannerize
					'bannerize' => array(
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),										
				);
			}
			if($all || $handler == 'attachaway')
			{
				$this->shortcodes['attachaway'] = array(
					// Config
					'type' => array(
						'default' => 'list',
						'options' => array(
							'' => 'Sorted List',
							'table' => 'Sortable Data Table'
						),
					),
					'postid' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => false,
							'options' => false,
						),							
					),					
					'search' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Enabled',
								'no' => 'Disabled',
							),
						),
					),
					'searchlabel' => array(
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),	
					'filenamelabel' => array(
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),
					'capcolumn'	=> array(
						'table' => array(
							'default' => false,
							'options' => false,
						),										
					),
					'descolumn'	=> array(
						'table' => array(
							'default' => false,
							'options' => false,
						),					
					),
					'paginate' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'true' => 'Enabled',
							),
						),
					),
					'pagesize' => array(
						'table' => array(
							'default' => 15,
							'options' => false,
						),
					),
					'size' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Show',
								'no' => 'Hide'
							),
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Show',
								'no' => 'Hide'
							),
						),
					),
					'sortfirst' => array(
						'table' => array(
							'default' => 'filename',
							'options' => array(
								'' => 'Filename ASC',
								'filename-desc' => 'Filename DSC', 
								'type' => 'Filetype ASC', 
								'type-desc' => 'Filetype DSC', 
								'caption' => 'Caption Column ASC', 
								'caption-desc' => 'Caption Column DSC', 
								'description' => 'Description Column ASC',
								'description-desc' => 'Description Column DSC',
								'mod' => 'Date Modified ASC', 
								'mod-desc' => 'Date Modified DSC', 
								'size' => 'Filesize ASC', 
								'size-desc' => 'Filesize DSC',
								'disabled' => 'Disable Sorting',
							),
						),
					),					
					'orderby' => array(
						'list' => array(
							'default' => 'title',
							'options' => array(
								'' => 'Title',
								'menu_order' => 'Menu Order',
								'ID' => 'ID',
								'date' => 'Date',
								'modified' => 'Modified',
								'rand' => 'Random',
							),
						),					
					),
					'desc' => array(
						'list' => array(
							'default' => 'asc',
							'options' => array(
								'' => 'Asc',
								'true' => 'Desc',
							),
						),									
					),
					'fadein' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'opacity' => 'Opacity Fade',
								'display' => 'Display Fade'
							),
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'opacity' => 'Opacity Fade',
								'display' => 'Display Fade'
							),
						),
					),
					'fadetime' => array(
						'list' => array(
							'default' => '1000',
							'options' => array(
								'500' => '500',
								'' => '1000',
								'1500' => '1500',
								'2000' => '2000'
							),
						),
						'table' => array(
							'default' => '1000',
							'options' => array(
								'500' => '500',
								'' => '1000',
								'1500' => '1500',
								'2000' => '2000'
							),
						),
					),	
					'class' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),				
					'debug' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'on' => 'Enabled'
							),
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'on' => 'Enabled'
							),
						),
					),
					// Modes
					'flightbox' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'images' => 'Images',
								'videos' => 'Videos',
								'pdfs' => 'PDFs',
								'multi' => 'Multi-Media'
							),
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'images' => 'Images',
								'videos' => 'Videos',
								'pdfs' => 'PDFs',
								'multi' => 'Multi-Media'
							),
						),
					),
					'boxtheme' => array(
						'list' => array(
							'default' => 'minimalist',
							'options' => $flightboxstyles,
						),
						'table' => array(
							'default' => 'minimalist',
							'options' => $flightboxstyles,
						),
					),	
					'nolinksbox' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Enable Downloads',
								'true' => 'Disable Downloads'
							),
							'binary' => 'true',
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Enable Downloads',
								'true' => 'Disable Downloads'
							),
							'binary' => 'true',
						),
					),									
					'maximgwidth' => array(
						'list' => array(
							'default' => '1920',
							'options' => false,
						),
						'table' => array(
							'default' => '1920',
							'options' => false,
						),							
					),
					'maximgheight' => array(
						'list' => array(
							'default' => '1080',
							'options' => false,
						),
						'table' => array(
							'default' => '1080',
							'options' => false,
						),							
					),
					'videowidth' => array(
						'list' => array(
							'default' => '1920',
							'options' => false,
						),
						'table' => array(
							'default' => '1920',
							'options' => false,
						),
					),
					// Filters
					'exclude' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => false,
							'options' => false,
						),							
					),
					'include' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => false,
							'options' => false,
						),							
					),
					'only' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => false,
							'options' => false,
						),							
					),
					'images' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Include',
								'only' => 'Only',
								'none' => 'Exclude',
							),
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Include',
								'only' => 'Only',
								'none' => 'Exclude',
							),
						),
					),
					'code' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Exclude',
								'yes' => 'Include',
							),
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Exclude',
								'yes' => 'Include',
							),
						),
					),					
					'devices' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'All Devices',
								'desktop' => 'Desktops/Notebooks',
								'mobile' => 'Mobiles/Tablets',
							),
						),
						'table' => array(
							'options' => array(
								'' => 'All Devices',
								'desktop' => 'Desktops/Notebooks',
								'mobile' => 'Mobiles/Tablets',
							),						
						),
					),
					'showto' => array(
						'list' => array(
							'default' => 'skip',
							'options' => $roles,
						),
						'table' => array(
							'default' => 'skip',
							'options' => $roles,
						),							
					),
					'hidefrom' => array(
						'list' => array(
							'default' => 'skip',
							'options' => $roles,
						),
						'table' => array(
							'default' => 'skip',
							'options' => $roles,
						),							
					),		
					// Styles
					'theme' => array(
						'list' => array(
							'default' => 'minimal-list',
							'options' => $liststyles,
						),
						'table' => array(
							'default' => 'minimalist',
							'options' => $tablestyles,
						),
					),
					'heading' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => false,
							'options' => false,
						),							
					),					
					'width' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => '100',
							'options' => false,
						),
					),
					'perpx' => array(
						'list' => array(
							'default' => '%',
							'options' => array(
								'' => 'Percent',
								'px' => 'Pixels',
							),
						),
						'table' => array(
							'default' => '%',
							'options' => array(
								'' => 'Percent',
								'px' => 'Pixels',
							),
						),
					),
					'align' => array(
						'list' => array(
							'default' => 'left',
							'options' => array(
								'' => 'Left',
								'right' => 'Right',
								'none' => 'None',
							),
						),
						'table' => array(
							'default' => 'left',
							'options' => array(
								'' => 'Left',
								'right' => 'Right',
								'none' => 'None',
							),
						),
					),
					'textalign' => array(
						'table' => array(
							'default' => 'center',
							'options' => array(
								'' => 'Center',
								'left' => 'Left',
								'right' => 'Right',
							),
						),
					),
					'hcolor'	=> array(
						'list' => array(
							'default' => false,
							'options' => $random,
						),
						'table' => array(
							'default' => false,
							'options' => $random,
						),					
					),
					'color' => array(
						'list' => array(
							'default' => false,
							'options' => $random,
						),
						'table' => array(
							'default' => false,
							'options' => $classic,
						),					
					),
					'accent' => array(
						'list' => array(
							'default' => false,
							'options' => $matched,
						),
					),
					'iconcolor' => array(
						'list' => array(
							'default' => false,
							'options' => $random,
						),
						'table' => array(
							'default' => false,
							'options' => $classic,
						),
					),
					'icons' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Filetype',
								'paperclip' => 'Paperclip',
								'none' => 'None',
							),
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Filetype',
								'paperclip' => 'Paperclip',
								'none' => 'None',
							),
						),
					),
					'corners' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Rounded',
								'sharp' => 'Sharp',
								'roundtop' => 'Rounded Top',
								'roundbottom' => 'Rounded Bottom',
								'roundleft' => 'Rounded Left',
								'roundright' => 'Rounded Right',
								'elliptical' => 'Elliptical'
							),
						),
					),
					'display' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Vertical',
								'inline' => 'Side-by-Side',
								'2col' => 'Two Columns',
							),
						),
					),
				);
			}
			if($all || $handler == 'fileup')
			{
				$this->shortcodes['fileup'] = array(			
					// Config
					'base' => array(
						'default' => 1,
						'options' => $upbase,
					),
					'sub' => array(
						'default' => false,
						'options' => false,					
					),
					'makedir'	=> array(
						'default' => false,
						'options' => array(
							'' => 'Disabled',
							'true' => 'Enabled',
						),
					),					
					'matchdrawer' => array(
						'default' => false,
						'options' => false,					
					),
					'single' => array(
						'default' => false,
						'options' => array(
							'' => 'Multiple',
							'true' => 'Single',
						),					
					),
					'maxsize' => array(
						'default' => '10',
						'options' => false,					
					),
					'maxsizetype' => array(
						'default' => 'm',
						'options' => array(
							'' => 'MB',
							'k' => 'KB',
							'g' => 'GB',							
						),					
					),
					'uploadlabel' => array(
						'default' => false,
						'options' => false,
					),
					'fadein' => array(
						'default' => false,
						'options' => array(
							'' => 'Disabled',
							'opacity' => 'Opacity Fade',
							'display' => 'Display Fade'
						),
					),
					'fadetime' => array(
						'default' => '1000',
						'options' => array(
							'500' => '500',
							'' => '1000',
							'1500' => '1500',
							'2000' => '2000'
						),
					),
					'fixedlocation'	=> array(
						'default' => false,
						'options' => array(
							'' => 'Allow Sub Selection',
							'true' => 'Fixed Location',
						),
					),
					'uploader' => array(
						'default' => false,
						'options' => array(
							'' => 'Disabled',
							'name' => 'Display Name',
							'id' => 'User ID',
						),
					),
					'overwrite' => array(
						'default' => false,
						'options' => array(
							'' => 'Never Overwrite',
							'true' => 'Always Overwrite',
						),
					),
					'name' => array(
						'default' => false,
						'options' => false,
					),
					'class' => array(
						'default' => false,
						'options' => false,
					),
					// Filters
					'devices' => array(
						'default' => false,
						'options' => array(
							'' => 'All Devices',
							'desktop' => 'Desktops/Notebooks',
							'mobile' => 'Mobiles/Tablets',
						),
					),
					'action' => array(
						'default' => 'permit',
						'options' => array(
							'' => 'Permit',
							'prohibit' => 'Prohibit',
						),
					),
					'filetypes' => array(
						'default' => false,
						'options' => false,					
					),
					'filegroups' => array(
						'default' => 'skip',
						'options' => $filegroups,
					),	
					'showto' => array(
						'default' => 'skip',
						'options' => $roles,
					),
					'hidefrom' => array(
						'default' => 'skip',
						'options' => $roles,
					),				
					// Style
					'theme' => array(
						'default' => 'minimalist',
						'options' => $tablestyles,
					),
					'width' => array(
						'default' => '100',
						'options' => false,
					),
					'perpx' => array(
						'default' => '%',
						'options' => array(
							'' => 'Percent',
							'px' => 'Pixels',
						),
					),
					'align' => array(
						'default' => 'none',
						'options' => array(
							'' => 'None',
							'left' => 'Left',
							'right' => 'Right',
						),
					),
					'iconcolor' => array(
						'default' => false,
						'options' => $classic,
					),
				);
			}
			if($all || $handler == 'fileaway_values')
			{
				$this->shortcodes['fileaway_values'] = array(
					// Config
					'base' => array(
						'default' => '1',
						'options' => $csvbase,
					),
					'sub' => array(
						'default' => false,
						'options' => false,
					),
					'filename' => array(
						'default' => false,
						'options' => false,
					),					
					'makecsv' => array(
						'default' => false,
						'options' => false,						
					),
					'makedir' => array(
						'default' => false,
						'options' => array(
							'' => 'Disabled',
							'true' => 'Enabled'
						),
					),
					'paginate' => array(
						'default' => false,
						'options' => array(
							'' => 'Disabled',
							'true' => 'Enabled',
						),
						'binary' => 'true',
					),
					'pagesize' => array(
						'default' => '15',
						'options' => false,
					),
					'sorting' => array(
						'default' => false,
						'options' => array(
							'' => 'Disabled',
							'true' => 'Enabled',							
						),
					),
					'search' => array(
						'default' => false,
						'options' => array(
							'' => 'Enabled',
							'no' => 'Disabled',
						),
					),
					'searchlabel' => array(
						'default' => false,
						'options' => false,
					),
					'placeholder' => array(
						'default' => __('Select CSV', 'file-away'),
						'options' => false,
					),
					'read' => array(
						'default' => 'ISO-8859-1',
						'options' => array(
							'' => 'ISO-8859-1',
							'UTF-8' => 'UTF-8',
							'UTF-16' => 'UTF-16',
							'ISO-8859-2' => 'ISO-8859-2',
							'ISO-8859-3' => 'ISO-8859-3',
							'ISO-8859-4' => 'ISO-8859-4',
							'ISO-8859-5' => 'ISO-8859-5',
							'ISO-8859-6' => 'ISO-8859-6',
							'ISO-8859-7' => 'ISO-8859-7',
							'ISO-8859-8' => 'ISO-8859-8',
							'ISO-8859-9' => 'ISO-8859-9',
							'ISO-8859-10' => 'ISO-8859-10',
							'ISO-8859-11' => 'ISO-8859-11',
							'ISO-8859-13' => 'ISO-8859-13',
							'ISO-8859-14' => 'ISO-8859-14',
							'ISO-8859-15' => 'ISO-8859-15',
							'ISO-8859-16' => 'ISO-8859-16',
						),					
					),
					'write' => array(
						'default' => 'ISO-8859-1',
						'options' => array(
							'' => 'ISO-8859-1',
							'UTF-8' => 'UTF-8',
							'UTF-16' => 'UTF-16',
							'ISO-8859-2' => 'ISO-8859-2',
							'ISO-8859-3' => 'ISO-8859-3',
							'ISO-8859-4' => 'ISO-8859-4',
							'ISO-8859-5' => 'ISO-8859-5',
							'ISO-8859-6' => 'ISO-8859-6',
							'ISO-8859-7' => 'ISO-8859-7',
							'ISO-8859-8' => 'ISO-8859-8',
							'ISO-8859-9' => 'ISO-8859-9',
							'ISO-8859-10' => 'ISO-8859-10',
							'ISO-8859-11' => 'ISO-8859-11',
							'ISO-8859-13' => 'ISO-8859-13',
							'ISO-8859-14' => 'ISO-8859-14',
							'ISO-8859-15' => 'ISO-8859-15',
							'ISO-8859-16' => 'ISO-8859-16',
						),	
					),							
					// Modes
					'recursive' => array(
						'default' => false,
						'options' => array(
							'' => 'Disabled',
							'on' => 'Enabled'
						),
						'binary' => 'on',
					),
					'editor' => array(
						'default' => false,
						'options' => array(
							'' => 'Disabled',
							'true' => 'Enabled'
						),
						'binary' => 'true',
					),
					// Filters
					'exclude' => array(
						'default' => false,
						'options' => false,
					),
					'include' => array(
						'default' => false,
						'options' => false,
					),
					'only' => array(
						'default' => false,
						'options' => false,
					),
					'excludedirs' => array(
						'default' => false,
						'options' => false,
					),
					'onlydirs' => array(
						'default' => false,
						'options' => false,
					),
					'devices' => array(
						'default' => false,
						'options' => array(
							'' => 'All Devices',
							'desktop' => 'Desktops/Notebooks',
							'mobile' => 'Mobiles/Tablets',
						),
					),										
					'showto' => array(
						'default' => 'skip',
						'options' => $roles,
					),
					'hidefrom' => array(
						'default' => 'skip',
						'options' => $roles,
					),
					// Styles
					'theme' => array(
						'default' => 'minimalist',
						'options' => $tablestyles,
					),
					'width' => array(
						'default' => '100',
						'options' => false,
					),
					'perpx' => array(
						'default' => '%',
						'options' => array(
							'' => 'Percent',
							'px' => 'Pixels',
						),
					),
					'align' => array(
						'default' => 'none',
						'options' => array(
							'' => 'None',
							'left' => 'Left',
							'right' => 'Right',
						),
					),
					'textalign' => array(
						'default' => 'center',
						'options' => array(
							'' => 'Center',
							'left' => 'Left',
							'right' => 'Right',
						),
					),
					'hcolor' => array(
						'default' => false,
						'options' => $random,
					),
				);
			}
			if($all || $handler == 'formaway_open')
			{			
				$this->shortcodes['formaway_open'] = array(
					// Config
					'paginate' => array(
						'default' => false,
						'options' => array(
							'' => 'Disabled',
							'true' => 'Enabled',
						),
						'binary' => 'true',
					),
					'pagesize' => array(
						'default' => '15',
						'options' => false,
					),
					'search' => array(
						'default' => false,
						'options' => array(
							'' => 'Enabled',
							'no' => 'Disabled',
						),
					),
					'searchlabel' => array(
						'default' => false,
						'options' => false,
					),
					'fadein' => array(
						'default' => false,
						'options' => array(
							'' => 'Disabled',
							'opacity' => 'Opacity Fade',
							'display' => 'Display Fade'
						),
					),
					'fadetime' => array(
						'default' => '1000',
						'options' => array(
							'500' => '500',
							'' => '1000',
							'1500' => '1500',
							'2000' => '2000'
						),
					),					
					// Columns
					'numcols' => array(
						'default' => '1',
						'options' => false,
					),
					'sort' => array(
						'default' => false,
						'options' => array(
							'' => 'Ascending',
							'desc' => 'Descending',
							'no' => 'Disabled',
						),					
					),
					'initialsort' => array(
						'default' => '1',
						'options' => array(),					
					),					
					// Styles
					'theme' => array(
						'default' => 'minimalist',
						'options' => $tablestyles,
					),
					'heading' => array(
						'default' => false,
						'options' => false,
					),
					'hcolor' => array(
						'default' => false,
						'options' => $random,
					),					
					'classes' => array(
						'default' => false,
						'options' => false,
					),
					'width' => array(
						'default' => '100',
						'options' => false,
					),
					'perpx' => array(
						'default' => '%',
						'options' => array(
							'' => 'Percent',
							'px' => 'Pixels',
						),
					),
					'align' => array(
						'default' => 'none',
						'options' => array(
							'' => 'None',
							'left' => 'Left',
							'right' => 'Right',
						),
					),
					'textalign' => array(
						'default' => 'center',
						'options' => array(
							'' => 'Center',
							'left' => 'Left',
							'right' => 'Right',
						),
					),
				);
			}			
			if($all || $handler == 'formaway_row')
			{			
				$this->shortcodes['formaway_row'] = array(
					// Config
					'classes' => array(
						'default' => false,
						'options' => false,
					),
				);
			}
			if($all || $handler == 'formaway_cell')
			{			
				$this->shortcodes['formaway_cell'] = array(
					// Config
					'sortvalue' => array(
						'default' => false,
						'options' => false,
					),
					'classes' => array(
						'default' => false,
						'options' => false,
					),
					'colspan' => array(
						'default' => false,
						'options' => false,
					),
				);
			}
			if($all || $handler == 'formaway_close')
			{			
				$this->shortcodes['formaway_close'] = array(
					'clearfix'=> array(
						'default' => false,
						'options' => array(
							'' => 'No',
							'true' => 'Yes',
						),
					),
				);
			}						
			if($all || $handler == 'fileaframe')
			{
				$this->shortcodes['fileaframe'] = array(			
					// Config
					'source' => array(
						'default' => false,
						'options' => false,					
					),
					'name' => array(
						'default' => false,
						'options' => false,
					),
					// Style
					'scroll' => array(
						'default' => 'no',
						'options' => array(
							'' => 'Off',
							'yes' => 'On',
							'auto' => 'Auto',
						),					
					),
					'width' => array(
						'default' => '100%',
						'options' => false,
					),
					'height' => array(
						'default' => '1000px',
						'options' => false,					
					),
					'mwidth' => array(
						'default' => '0px',
						'options' => false,
					),
					'mheight' => array(
						'default' => '0px',
						'options' => false,
					),					
					'showto' => array(
						'default' => 'skip',
						'options' => $roles,
					),
					'hidefrom' => array(
						'default' => 'skip',
						'options' => $roles,
					),														
					'devices' => array(
						'default' => false,
						'options' => array(
							'' => 'All Devices',
							'desktop' => 'Desktops/Notebooks',
							'mobile' => 'Mobiles/Tablets',
						),
					),
				);
			}
			if($all || $handler == 'stataway')
			{
				$this->shortcodes['stataway'] = array(
					// Config
					'type' => array(
						'default' => 'table',
						'options' => array(
							'' => 'Sorted List',
							'table' => 'Sortable Data Table'
						),
					),
					'show' => array(
						'list' => array(
							'default' => 'top',
							'options' => array(
								'' => 'Top Downloads',
								'recent' => 'Most Recent Downloads',
							),
						),
					),
					'scope' => array(
						'list' => array(
							'default' => 'week',
							'options' => array(
								'24hrs' => 'Past 24 Hours',
								'yesterday' => 'Yesterday',
								'' => 'Past Week',
								'twoweeks' => 'Past Two Weeks',
								'month' => 'Past Month',
								'year' => 'Past Year',
								'all' => 'All Time (Not Recommended)',
							),
						),
					),
					'number' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
					),
					'class' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),
					'paginate' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'true' => 'Enabled',
							),
							'binary' => 'true',
						),
					),
					'pagesize' => array(
						'table' => array(
							'default' => '15',
							'options' => false,
						),
					),
					'search' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Enabled',
								'no' => 'Disabled',
							),
						),
					),
					'searchlabel' => array(
						'table' => array(
							'default' => false,
							'options' => false,
						),
					),
					'mod' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Hide',
								'yes' => 'Show'
							),
						),
					),
					'size' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Show',
								'no' => 'Hide'
							),
						),
					),
					'filecolumn' => array(
						'table' => array(
							'default' => 'path',
							'options' => array(
								'' => 'Full Path to File',
								'file' => 'File Name Only'
							),
						),
					),
					'username' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Show',
								'no' => 'Hide'
							),
						),
					),
					'email' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Show',
								'no' => 'Hide'
							),
						),
					),
					'ip' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Show',
								'no' => 'Hide'
							),
						),
					),
					'agent' => array(
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Hide',
								'yes' => 'Show'
							),
						),
					),					
					'redirect' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'true' => 'Enabled'
							),
							'binary' => 'true',
						),
					),					
					'fadein' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'opacity' => 'Opacity Fade',
								'display' => 'Display Fade'
							),
						),
						'table' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'opacity' => 'Opacity Fade',
								'display' => 'Display Fade'
							),
						),
					),
					'fadetime' => array(
						'list' => array(
							'default' => '1000',
							'options' => array(
								'500' => '500',
								'' => '1000',
								'1500' => '1500',
								'2000' => '2000'
							),
						),
						'table' => array(
							'default' => '1000',
							'options' => array(
								'500' => '500',
								'' => '1000',
								'1500' => '1500',
								'2000' => '2000'
							),
						),
					),					
					's2skipconfirm' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Confirmations On',
								'true' => 'Confirmations Off'
							),
						),
					),
					// Modes
					'stats' => array(
						'list' => array(
							'default' => 'true',
							'options' => array(
								'' => 'Enabled',
								'false' => 'Disabled',
							)
						),
						'table' => array(
							'default' => 'false',
							'options' => array(
								'' => 'Disabled',
								'true' => 'Enabled',
							)
						),						
					),
					'flightbox' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'images' => 'Images',
								'videos' => 'Videos',
								'pdfs' => 'PDFs',
								'multi' => 'Multi-Media'
							),
						),
					),
					'boxtheme' => array(
						'list' => array(
							'default' => 'minimalist',
							'options' => $flightboxstyles,
						),
					),
					'maximgwidth' => array(
						'list' => array(
							'default' => '1920',
							'options' => false,
						),
					),
					'maximgheight' => array(
						'list' => array(
							'default' => '1080',
							'options' => false,
						),
					),
					'videowidth' => array(
						'list' => array(
							'default' => '1920',
							'options' => false,
						),
					),
					'encryption' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Disabled',
								'on' => 'Enabled'
							),
							'binary' => 'on',
						),
					),
					// Filters
					'devices' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'All Devices',
								'desktop' => 'Desktops/Notebooks',
								'mobile' => 'Mobiles/Tablets',
							),
						),
						'table' => array(
							'options' => array(
								'' => 'All Devices',
								'desktop' => 'Desktops/Notebooks',
								'mobile' => 'Mobiles/Tablets',
							),						
						),
					),
					'showto' => array(
						'list' => array(
							'default' => 'skip',
							'options' => $roles,
						),
						'table' => array(
							'default' => 'skip',
							'options' => $roles,
						),							
					),
					'hidefrom' => array(
						'list' => array(
							'default' => 'skip',
							'options' => $roles,
						),
						'table' => array(
							'default' => 'skip',
							'options' => $roles,
						),							
					),
					// Styles
					'theme' => array(
						'list' => array(
							'default' => 'minimal-list',
							'options' => $liststyles,
						),
						'table' => array(
							'default' => 'minimalist',
							'options' => $tablestyles,
						),
					),
					'heading' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
					),					
					'width' => array(
						'list' => array(
							'default' => false,
							'options' => false,
						),
						'table' => array(
							'default' => '100',
							'options' => false,
						),
					),
					'perpx' => array(
						'list' => array(
							'default' => '%',
							'options' => array(
								'' => 'Percent',
								'px' => 'Pixels',
							),
						),
						'table' => array(
							'default' => '%',
							'options' => array(
								'' => 'Percent',
								'px' => 'Pixels',
							),
						),
					),
					'align' => array(
						'list' => array(
							'default' => 'left',
							'options' => array(
								'' => 'Left',
								'right' => 'Right',
								'none' => 'None',
							),
						),
						'table' => array(
							'default' => 'left',
							'options' => array(
								'' => 'Left',
								'right' => 'Right',
								'none' => 'None',
							),
						),
					),
					'textalign' => array(
						'table' => array(
							'default' => 'center',
							'options' => array(
								'' => 'Center',
								'left' => 'Left',
								'right' => 'Right',
							),
						),
					),
					'hcolor' => array(
						'list' => array(
							'default' => false,
							'options' => $random,
						),
					),
					'color' => array(
						'list' => array(
							'default' => false,
							'options' => $random,
						),
					),
					'accent' => array(
						'list' => array(
							'default' => false,
							'options' => $matched,
						),
					),
					'iconcolor' => array(
						'list' => array(
							'default' => false,
							'options' => $random,
						),
					),
					'icons' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Filetype',
								'paperclip' => 'Paperclip',
								'none' => 'None',
							),
						),
					),
					'corners' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Rounded',
								'sharp' => 'Sharp',
								'roundtop' => 'Rounded Top',
								'roundbottom' => 'Rounded Bottom',
								'roundleft' => 'Rounded Left',
								'roundright' => 'Rounded Right',
								'elliptical' => 'Elliptical'
							),
						),
					),
					'display' => array(
						'list' => array(
							'default' => false,
							'options' => array(
								'' => 'Vertical',
								'inline' => 'Side-by-Side',
								'2col' => 'Two Columns',
							),
						),
					),
				);
			}
			if($all || $handler == 'stataway_user')
			{
				$this->shortcodes['stataway_user'] = array(
					// Config
					'output' => array(
						'default' => 'total',
						'options' => array(
							'' => 'Total Downloads',
							'ol' => 'Ordered List',
							'ul' => 'Unordered List',
						),
					),
					'scope' => array(
						'default' => 'week',
						'options' => array(
							'24hrs' => 'Past 24 Hours',
							'' => 'Past Week',
							'twoweeks' => 'Past Two Weeks',
							'month' => 'Past Month',
							'year' => 'Past Year',
							'all' => 'All Time',
						),
					),
					'user' => array(
						'default' => false,
						'options' => false,
					),
					'timestamp' => array(
						'default' => false,
						'options' => array(
							'' => 'Hide',
							'yes' => 'Show',
						),
					),										
					'class' => array(
						'default' => false,
						'options' => false,
					),
				);
			}
			if($all || $handler == 'fileaway_tutorials')
			{
				$this->shortcodes['fileaway_tutorials'] = array(			
					'showto' => array(
						'default' => false,
						'options' => $roles,
					),
					'hidefrom' => array(
						'default' => false,
						'options' => $roles,
					),				
				);
			}			
		}
		protected function atts($handle)
		{
			$atts = array(); if(!$handle) $handle = 'fileaway';
			foreach($this->shortcodes[$handle] as $att => $discard) $atts[$att] = '';
			return $atts;
		}
		protected function correct($atts, $ctrl)
		{
			foreach($atts as $a => $v)
			{
				$ops = isset($ctrl[$a]['options']) ? $ctrl[$a]['options'] : false;
				$dflt = isset($ctrl[$a]['default']) ? $ctrl[$a]['default'] : false;
				$binary = isset($ctrl[$a]['binary']) ? $ctrl[$a]['binary'] : false;
				if(!$ops && !$dflt) continue;
				if(!$v && !$dflt) continue;
				if($dflt == 'skip') continue;
				if($v && $ops && $binary) $atts[$a] = !array_key_exists($v, $ops) ? $binary : $v;
				elseif($v && $ops && !array_key_exists($v, $ops)) $atts[$a] = $dflt;
				elseif(!$v && $dflt) $atts[$a] = $dflt; 
			}
			return $atts;
		}		
		protected function correctatts($atts, $control, $shortcode)
		{
			extract($atts);	
			if($shortcode == 'fileaway') 
				$type = $type == 'table' || $directories || $manager || $playback || $bulkdownload || $thumbnails ? 'table' : 'list';
			else $type = $type == 'table' ? 'table' : 'list';
			foreach($atts as $a => $v)
			{
				if($a == 'type')
				{
					$atts[$a] = $type;
					continue;	
				}
				$ctrl = isset($control[$a][$type]) ? $control[$a][$type] : false;
				if(!$ctrl)
				{
					$atts[$a] = false; 
					continue;
				}
				$ops = isset($ctrl['options']) ? $ctrl['options'] : false;
				$dflt = isset($ctrl['default']) ? $ctrl['default'] : false;
				$binary = isset($ctrl['binary']) ? $ctrl['binary'] : false;
				if(!$ops && !$dflt) continue;
				if(!$v && !$dflt) continue;
				if($dflt == 'skip') continue;
				if($v && $ops && $binary) $atts[$a] = !array_key_exists($v, $ops) ? $binary : $v;
				elseif($v && $ops && !array_key_exists($v, $ops)) $atts[$a] = $dflt;
				elseif(!$v && $dflt) $atts[$a] = $dflt; 
			}
			return $atts;
		}
		public function autofix($content)
		{
			$html = trim($content);
			if($html === '') return '';	
			$blocktags = implode('|', array('audio', 'h3', 'iframe', 'li', 'ol', 'script', 'table', 'tbody', 'td', 'tfoot', 'thead', 'th', 'tr', 'ul', 'video')); 
			$html = preg_replace('~<p>\s*<('.$blocktags.')\b~i', '<$1', $html);
			$html = preg_replace('~</('.$blocktags.')>\s*</p>~i', '</$1>', $html);
			$html = preg_replace('~</('.$blocktags.')>\s*<br />~i', '</$1>', $html);
			return $html;
		}
	}
}