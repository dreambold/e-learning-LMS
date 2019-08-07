<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(!class_exists('fileaway_definitions'))
{
	class fileaway_definitions
	{
		private $options;
		public $pathoptions, $filegroups, $imagetypes, $codexts, $nevershows, $file_exclusions, $dir_exclusions, $is_opera, $is_mobile;
		public static $s2member, $pathinfo;
		private static $symlinks = false;
		public function __construct()
		{
			$this->options = get_option('fileaway_options');
			self::$symlinks = empty($this->options['symlinks']) ? 0 : 1;
			self::$pathinfo = isset($this->options['pathinfo']) && $this->options['pathinfo'] == 'enabled' ? true : false;
			self::$s2member = fileaway_utility::active('s2member/s2member.php');
			$this->pathoptions = array(); 
			$this->paths();
			$this->imagetypes = array('bmp', 'jpg', 'jpeg', 'gif', 'png', 'tif', 'tiff');
			$this->codexts = array('js', 'pl', 'py', 'rb', 'css', 'less', 'scss', 'sass', 'php', 'htm', 
				'html', 'cgi', 'asp', 'cfm', 'cpp', 'xml', 'yml', 'shtm', 'xhtm', 'java', 'class');
			$this->nevershows = array('index.htm', 'index.html', 'index.php', '.htaccess', '.htpasswd', '_fa.feed.id.ini');
			$this->file_exclusions = $this->options['exclusions'] 
				? preg_split('/(, |,)/', trim($this->options['exclusions']), -1, PREG_SPLIT_NO_EMPTY) 
				: array();
			$this->dir_exclusions = $this->options['direxclusions'] 
				? preg_split('/(, |,)/', trim($this->options['direxclusions']), -1, PREG_SPLIT_NO_EMPTY) 
				: array();
			$image = $GLOBALS['is_safari'] ? '&#x62;' : '&#x31;';
			$this->filegroups = array(
				'adobe'=>array(
					'Adobe',
					'&#x21;',
					array('abf', 'aep', 'afm', 'ai', 'as', 'eps', 'fla', 'flv', 'fm', 'indd', 
						'pdd', 'pdf', 'pmd', 'ppj', 'prc', 'ps', 'psb', 'psd', 'swf')
				),
				'application'=>array(
					'Application',
					'&#x54;',
					array('bat', 'dll', 'exe', 'msi')
				),
				'audio'=>array(
					'Audio',
					'&#x43;',
					array('aac', 'aif', 'aifc', 'aiff', 'amr', 'ape', 'au', 'bwf', 'flac', 'iff', 
						'gsm', 'la', 'm4a', 'm4b', 'm4p', 'mid', 'mp2', 'mp3', 'mpc', 'ogg', 'ots', 
						'ram', 'raw', 'rex', 'rx2', 'spx', 'swa', 'tta', 'vox', 'wav', 'wma', 'wv')
				),
				'compression'=>array(
					'Compression',
					'&#x27;',
					array('7z', 'a', 'ace', 'afa', 'ar', 'bz2', 'cab', 'cfs', 'cpio', 'cpt', 'dar', 
						'dd', 'dmg', 'gz', 'lz', 'lzma', 'lzo', 'mar', 'rar', 'rz', 's7z', 'sda', 
						'sfark', 'shar', 'tar', 'tgz', 'xz', 'z', 'zip', 'zipx', 'zz')
				),
				'css'=>array(
					'CSS',
					'&#x28;',
					array('css', 'less', 'sass', 'scss')
				),
				'image'=>array(
					'Image',
					$image,
					array('bmp', 'dds', 'exif', 'gif', 'hdp', 'hdr', 'iff', 'jfif', 'jpeg', 'jpg', 
						'jxr', 'pam', 'pbm', 'pfm', 'pgm', 'png', 'pnm', 'ppm', 'raw', 'rgbe', 'tga', 
						'thm', 'tif', 'tiff', 'webp', 'wdp', 'yuv')
				),
				'msdoc'=>array(
					'MS Doc',
					'&#x23;',
					array('doc', 'docm', 'docx', 'dot', 'dotx')
				),
				'msexcel'=>array(
					'MS Excel',
					'&#x24;',
					array('xls', 'xlsm', 'xlsb', 'xlsx', 'xlt', 'xltm', 'xltx', 'xlw')
				),
				'openoffice'=>array(
					'Open Office',
					'&#x22;',
					array('dbf', 'dbf4', 'odp', 'ods', 'odt', 'stc', 'sti', 'stw', 'sxc', 'sxi', 'sxw')
				),
				'powerpoint'=>array(
					'PowerPoint',
					'&#x26;',
					array('pot', 'potm', 'potx', 'pps', 'ppt', 'pptm', 'pptx', 'pub')
				),
				'script'=>array(
					'Script',
					'&#x25;',
					array('asp', 'cfm', 'cgi', 'clas', 'class', 'cpp', 'htm', 'html', 'java', 'js', 
						'php', 'pl', 'py', 'rb', 'shtm', 'shtml', 'xhtm', 'xhtml', 'xml', 'yml')
				),
				'text'=>array(
					'Text',
					'&#x2e;',
					array('123', 'csv', 'log', 'psw', 'rtf', 'sql', 'txt', 'uof', 'uot', 'wk1', 
						'wks', 'wpd', 'wps')
				),
				'video'=>array(
					'Video',
					'&#x57;',
					array('avi', 'divx', 'mov', 'm4p', 'm4v', 'mkv', 'mp4', 'mpeg', 'mpg', 'ogv', 'qt', 
						'rm', 'rmvb', 'vob', 'webm', 'wmv')
				),
				'unknown'=>array(
					'Unknown',
					'&#x29;',
					false
				)
			);
			$agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
			$this->is_opera = stripos($agent, 'opr') !== false || stripos($agent, 'opera') !== false ? true : false; 
			$mobiles = array(
				'mobile', 'iphone','ipod', 'ipad', 'android', 'tablet', 'wOSBrowser', 'TouchPad', 'Nook', 'Pad', 'blackberry', 'opera mobi', 
				'opera mini', 'Skyfire', 'Samsung', 'webOS', 'LG', 'Kindle', 'Silk', 'WindowsMobile', 'IEMobile', 'blazer', 'BOLT', 'Series60', 
				'Symbian', 'Nokia', 'Droid', 'XT720', 'MOT-', 'MIB', 'WM5', 'teleca q7', 'TeaShark', 'SEMC-Browser', 'NetFront', 'Minimo', 'MIB', 
				'Maemo Browser', 'Iris', 'GoBrowser', 'Fennec', 'Dorothy', 'Doris', 'uzardweb', 'SPH', 'SCH', 
			);
			$this->is_mobile = false;
			foreach($mobiles as $mobile)
			{
				if(stripos($agent, $mobile) !== false)
				{ 
					$this->is_mobile = true;	
					break;
				}
			}
		}
		private function paths()
		{
			$home = str_replace('\\','/',fileaway_utility::replacefirst(get_option('home'), 'https:', 'http:'));
			$wpurl = str_replace('\\','/',fileaway_utility::replacefirst(get_bloginfo('wpurl'), 'https:', 'http:'));
			$install = trim($home, '/') === trim($wpurl, '/') ? false 
				: str_replace('//', '/', ltrim(str_replace(rtrim($home, '/'), '', rtrim($wpurl, '/')), '/').'/');		
			$install = $install === '/' ? false : $install; 
			$installpath = str_replace('\\','/',ABSPATH);
			$rootpath = str_replace('\\','/',($install ? substr_replace(ABSPATH, '', strrpos(ABSPATH, $install), strlen($install)) : ABSPATH));
			$chosenpath = str_replace('\\','/',($this->options['rootdirectory'] === 'siteurl' ? $rootpath : ABSPATH));
			$problemchild = $install && $this->options['rootdirectory'] !== 'siteurl' ? true : false;
			$playback_url = $this->options['rootdirectory'] === 'siteurl' ? rtrim(get_option('home'),'/').'/' : rtrim(get_bloginfo('wpurl'),'/').'/';
			$this->pathoptions = array(
				'install'		=> $install, 
				'installpath'	=> $installpath, 
				'rootpath'		=> $rootpath, 
				'chosenpath'	=> $chosenpath, 
				'problemchild'	=> $problemchild, 
				'playback_url'	=> $playback_url
			);
		}
		public static function s2member()
		{
			return self::$s2member;
		}
		public static function symlinks()
		{
			if(false !== self::$symlinks) return self::$symlinks;
			$ops = get_option('fileaway_options');
			self::$symlinks = empty($ops['symlinks']) ? 0 : 1;
			return self::$symlinks;
		}		
	}
}