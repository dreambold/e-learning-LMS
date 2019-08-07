<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(!class_exists('feedaway'))
{
	class feedaway
	{
		private $options;
		private $definitions;
		public function __construct()
		{
			$this->options = get_option('fileaway_options');
			$this->definitions = new fileaway_definitions;
			add_filter('cron_schedules', array($this, 'intervals'));
			if(isset($this->options['feeds']) && $this->options['feeds'] && trim($this->options['feeds']) != '')
			{
				if(!wp_next_scheduled('fileaway_scheduled_rss_feeds')) 
					wp_schedule_event(time(), $this->options['feedinterval'], 'fileaway_scheduled_rss_feeds');
				add_action('fileaway_scheduled_rss_feeds', array($this, 'feeds'));	
			}
			elseif(wp_next_scheduled('fileaway_scheduled_rss_feeds')) 
				wp_clear_scheduled_hook('fileaway_scheduled_rss_feeds');
		}
		public function intervals($schedules)
		{
			$schedules['fifteenminutes'] = array('interval' => 900, 'display' => __('Every 15 Minutes', 'file-away'));
			$schedules['thirtyminutes'] = array('interval' => 1800, 'display' => __('Every 30 Minutes', 'file-away'));
			$schedules['fortyfiveminutes'] = array('interval' => 2700, 'display' => __('Every 45 Minutes', 'file-away'));
			$schedules['sixhours'] = array('interval' => 21600, 'display' => __('Every 6 Hours', 'file-away'));
			$schedules['weekly'] = array('interval' => 604800, 'display' => __('Once Weekly', 'file-away'));
			$schedules['fortnightly'] = array('interval' => 1209600, 'display' => __('Every Two Weeks', 'file-away'));
		    return $schedules;
		}
		public function csvsearch($rows, $headers, $value, $searchkey = '0', $outputkey = '1')
		{
			$output = false;
			foreach($rows as $row)
			{
				if(trim($row[$headers[$searchkey]], '/') == $value)
				{
					$output = $row[$headers[$outputkey]]; 
					break;	
				}
			}
			return $output;
		}
		public function feeds()
		{
			extract($this->definitions->pathoptions);
			$basedirs = fileaway_utility::feeds();
			$excluded_dirs = fileaway_utility::feeds(true);
			$wp_excludes = array('wp-admin', 'wp-includes', basename(WP_CONTENT_DIR).'/themes', basename(WP_CONTENT_DIR).'/upgrade');
			$startswith = array('fa-feed-logo', '_thumb_', 'fileaway-url-parser', 'fileaway-banner-parser', 'index.htm', 'index.php', '.ht');
			$endswith = array('ini', 'php');
			if(isset($this->options['feed_excluded_exts']) && !empty($this->options['feed_excluded_exts']))
			{
				$endswith = array_unique(array_merge($endswith, preg_split('/(, |,)/', trim($this->options['feed_excluded_exts']), -1, PREG_SPLIT_NO_EMPTY))); 
			}
			$excludestrings = array();
			if(isset($this->options['feed_excluded_files']) && !empty($this->options['feed_excluded_files']))
			{
				$excludestrings = preg_split('/(, |,)/', trim($this->options['feed_excluded_files']), -1, PREG_SPLIT_NO_EMPTY);
			}
			if(!isset($this->options['feeds']) || !$this->options['feeds'] || trim($this->options['feeds']) == '' || trim($this->options['feeds']) == '/') return;
			$original_timezone = date_default_timezone_get();
			fileaway_utility::timezone();
			$storage = $rootpath.trim(trim($this->options['feeds']), '/');
			if(!is_dir($storage)) if(mkdir($storage, 0775, true)) fileaway_utility::indexmulti($storage, $chosenpath);
			$globallogo = false;
			if(is_file($storage.'/fa-feed-logo.png')) $globallogo = $storage.'/fa-feed-logo.png';
			elseif(is_file($storage.'/fa-feed-logo.jpg')) $globallogo = $storage.'/fa-feed-logo.jpg';
			elseif(is_file($storage.'/fa-feed-logo.gif')) $globallogo = $storage.'/fa-feed-logo.gif';
			if($globallogo)	$globallogo = fileaway_utility::urlesc(fileaway_utility::replacefirst($globallogo, rtrim($rootpath, '/'), rtrim($this->options['baseurl'], '/').'/')); 
			$feedlimit = isset($this->options['feedlimit']) && is_numeric($this->options['feedlimit']) ? round($this->options['feedlimit'], 0) : false;
			$hardlinks = array();
			$map = is_file($storage.'/fa-directory-map.csv') ? new fileaway_csv($storage.'/fa-directory-map.csv') : false;
			$now = time();
			foreach($basedirs as $basedir)
			{
				$basedir = $rootpath.trim(trim($basedir), '/');
				$dirs = fileaway_utility::recursivedirs($basedir);
				array_unshift($dirs, $basedir);
				$datestring = $this->options['daymonth'] == 'md' ? 'm/d/Y H:i' : 'd/m/Y H:i'; 
				foreach($dirs as $k => $dir)
				{
					foreach($wp_excludes as $wp)
					{
						if(stripos($dir, $wp) !== false) continue 2;	
					}
					$feedfile = $dir.'/_fa.feed.id.ini';
					if(!is_file($feedfile))
					{
						$feedid = uniqid(true);
						$feedfile_contents = "; Do not move or delete this file, nor alter its contents \n"."id = ".$feedid;
						file_put_contents($feedfile, $feedfile_contents);	
					}	
					else
					{
						$ini = parse_ini_file($feedfile);
						$feedid = $ini['id'];
					}
					$feed = $storage.'/_feed_'.$feedid.'.xml';
					if(fileaway_utility::startswith(fileaway_utility::replacefirst($dir, $rootpath, ''), $excluded_dirs))
					{
						if(is_file($feed)) unlink($feed);
						continue;
					}
					$feedlogo = false;
					if(is_file($dir.'/fa-feed-logo.png')) $feedlogo = $dir.'/fa-feed-logo.png';
					elseif(is_file($dir.'/fa-feed-logo.jpg')) $feedlogo = $dir.'/fa-feed-logo.jpg';
					elseif(is_file($dir.'/fa-feed-logo.gif')) $feedlogo = $dir.'/fa-feed-logo.gif';
					$feedlogo = $feedlogo 
						? fileaway_utility::urlesc(fileaway_utility::replacefirst($feedlogo, rtrim($rootpath, '/'), rtrim($this->options['baseurl'], '/').'/'))
						: $globallogo;
					$files = array();
					$subxml = array();
					$initfiles = array_filter(glob("$dir/*"), 'is_file');
					if($this->options['recursivefeeds'] == 'true')
					{
						$initdirs = glob("{$dir}/*", GLOB_ONLYDIR);
						if(is_array($initdirs) && count($initdirs) > 0)
						{
							foreach($initdirs as $subdir)
							{
								if(in_array(fileaway_utility::replacefirst($subdir, $rootpath, ''), $excluded_dirs)) continue;
								$subfeedfile = $subdir.'/_fa.feed.id.ini';
								if(!is_file($subfeedfile)) continue;
								$subini = parse_ini_file($subfeedfile);
								$subfeedid = $subini['id'];
								$subfeed = $storage.'/_feed_'.$subfeedid.'.xml';
								if(!is_file($subfeed)) continue;
								$sublink = $map ? $this->csvsearch($map->data, $map->titles, trim(fileaway_utility::replacefirst($subdir, $rootpath, ''), '/')) : false;
								$sublink = $sublink ? $sublink : fileaway_utility::replacefirst($subfeed, $rootpath, rtrim($this->options['baseurl'], '/').'/');
								$subPubDate = $this->options['feeddates'] == 'false' ? null : "<pubDate>".date($datestring, filemtime($subdir))."</pubDate>\n".
								$subxml[] = 
										"<item>\n".
											"<title>".fileaway_utility::basename($subdir)."</title>\n".
											"<link>".$sublink."</link>\n".
											"<description>RSS Feed</description>\n".
											$subPubDate.
										"</item>\n";
							}
						}
					}
					if(is_array($initfiles))
					{
						foreach($initfiles as $i => $file)
						{	
							if(fileaway_utility::startswith(fileaway_utility::basename($file), $startswith))
							{
								unset($initfiles[$i]);
								continue;
							}
							if(fileaway_utility::endswith(strtolower(fileaway_utility::basename($file)), $endswith))
							{
								unset($initfiles[$i]);
								continue;
							}
							foreach($excludestrings as $str)
							{
								if(strpos(fileaway_utility::basename($file), $str) !== false)
								{
									unset($initfiles[$i]);
									continue 2;
								}
							}
							$mime = false;
							$parts = fileaway_utility::pathinfo($file);
							$ext = strtolower($parts['extension']);
							if($this->options['feedlinks'] != 'false')
							{
								if(in_array($ext, array('mp3', 'wav', 'ogg')))
								{
									$mime = $ext == 'mp3' ? 'audio/mpeg' : ($ext == 'wav' ? 'audio/vnd.wave' : 'audio/ogg');
								}
								elseif(in_array($ext, array('png', 'gif', 'jpg', 'jpeg')))
								{
									$mime = $ext == 'png' ? 'image/png' : ($ext == 'gif' ? 'image/gif' : 'image/jpeg');
								}
								elseif(in_array($ext, array('avi', 'mpeg', 'mp4', 'ogv', 'mov', 'webm', 'flv', 'wmv', 'mkv')))
								{
									$mime = 
									$ext == 'avi' ? 'video/avi' : 
									($ext == 'mp4' ? 'video/mp4' : 
									($ext == 'flv' ? 'video/x-flv' : 
									($ext == 'mpeg' ? 'video/mpeg' : 
									($ext == 'ogv' ? 'video/ogg' : 
									($ext == 'mov' ? 'video/quicktime' : 
									($ext == 'webm' ? 'video/webm' : 
									($ext == 'wmv' ? 'video/x-ms-wmv' : 
									'video/x-matroska')))))));
								}
								if(stripos($file, 's2member-files/'))
								{
									$getsub = explode('s2member-files/', fileaway_utility::urlesc($file));
									$fileurl = rtrim($this->options['baseurl'], '/').'/?s2member_file_download='.$getsub[1];
								}
								else $fileurl = fileaway_utility::urlesc(fileaway_utility::replacefirst($file, $rootpath, rtrim($this->options['baseurl'], '/').'/'));
							}
							else $fileurl = false;
							$files['dirname'][] = $parts['dirname'];
							$files['basename'][] = $parts['basename'];
							$files['filename'][] = $parts['filename'];
							$files['extension'][] = $parts['extension'];
							$files['mime'][] = $mime;
							$files['datemodified'][] = $this->options['feeddates'] != 'false' ? filemtime($file) : $now;
							$files['filesize'][] = $this->options['feedsize'] != 'false' ? filesize($file) : false;
							$files['url'][] = $fileurl;
						}
						if(isset($files['datemodified']) && count($files['datemodified']) > 0)
						{
							array_multisort(
								$files['datemodified'], 
								SORT_DESC, SORT_NUMERIC, 
								$files['dirname'], 
								$files['basename'], 
								$files['filename'], 
								$files['extension'], 
								$files['mime'],
								$files['filesize'], 
								$files['url']
							);
						}
						$directory = explode('/', $dir);
						$directory = array_pop($directory);
						$stripped_url = str_ireplace(array('http:', 'https:', 'www.', 'ww2.', '//'), '', rtrim($this->options['baseurl'], '/')); 
						$ttl = $this->options['feedinterval'] == 'fifteenminutes' ? 15 
							: ($this->options['feedinterval'] == 'thirtyminutes' ? 30 
							: ($this->options['feedinterval'] == 'fortyfiveminutes' ? 45 : 60));
						$channellink = $map ? $this->csvsearch($map->data, $map->titles, trim(fileaway_utility::replacefirst($dir, $rootpath, ''), '/')) : $this->options['baseurl'];
						$description = $channellink ? $channellink : fileaway_utility::replacefirst($dir, $rootpath, rtrim($this->options['baseurl'], '/').'/');
						$channellink = $channellink ? $channellink : $this->options['baseurl'];
						$xml = 
							"<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n".
							"<rss version=\"2.0\">\n".
							"<channel>\n".
								"<title>".$stripped_url." > ".$directory."</title>\n".
								"<link>".$channellink."</link>\n".
								"<description>".$description."</description>\n".
								"<lastBuildDate>".date($datestring)."</lastBuildDate>\n".
								"<pubDate>".date($datestring)."</pubDate>\n".
								"<ttl>".$ttl."</ttl>\n".
								"<generator>File Away</generator>\n";
						if($feedlogo)
						{ 
							$xml .= 
								"<image>\n".
									"<url>".$feedlogo."</url>\n".
									"<title>".str_replace(array('http://', 'https://', 'www.'), '', $this->options['baseurl'])." > ".$directory."</title>\n".
									"<link>".$this->options['baseurl']."</link>\n".
								"</image>\n";
						}
						if(count($subxml > 0)) $xml .= implode($subxml);
						if(isset($files['datemodified']) && count($files['datemodified']) > 0)
						{
							foreach($files['datemodified'] as $k => $file)
							{
								if($feedlimit && $k >= $feedlimit) break;
								$rawname = str_replace('&', 'and', $files['filename'][$k]);
								if(preg_match('/\[([^\]]+)\]/', $rawname))
								{
									list($thename, $customvalue) = preg_split("/[\[\]]/", $rawname);
									$customvalue = str_replace(array('~', '--', '_', '.', '*'), ' ', $customvalue);
									$customvalue = preg_replace('/(?<=\D)-(?=\D)/', ' ', "$customvalue");
									$customvalue = preg_replace('/(?<=\d)-(?=\D)/', ' ', "$customvalue");
									$customvalue = preg_replace('/(?<=\D)-(?=\d)/', ' ', "$customvalue");	
									$thename = str_replace(array('~', '--', '_', '.', '*'), ' ', $thename); 
								}	
								else
								{ 
									$customvalue = false; 
									$thename = str_replace(array('~', '--', '_', '.', '*'), ' ', $rawname); 
								}
								$thename = preg_replace('/(?<=\D)-(?=\D)/', ' ', "$thename"); 
								$thename = preg_replace('/(?<=\d)-(?=\D)/', ' ', "$thename"); 
								$thename = preg_replace('/(?<=\D)-(?=\d)/', ' ', "$thename"); 
								$showsize = $this->options['feedsize'] == 'false' ? null :  "Size: ".fileaway_utility::formatBytes($files['filesize'][$k]).".";
								$filedescription = $customvalue ? $customvalue.' ('.$files['extension'][$k]." file. ".$showsize : $files['extension'][$k]." file. ".$showsize;
								$pubDate = $this->options['feeddates'] == 'false' ? null : "<pubDate>".date($datestring, $file)."</pubDate>\n";
								$filelink = $files['url'][$k] ? $files['url'][$k] : $channellink;
								$xml .= 
									"<item>\n".
										"<title>".$thename."</title>\n".
										"<link>".$filelink."</link>\n".
										"<description>".$filedescription."</description>\n".
										$pubDate;
								if($files['mime'][$k]) $xml .= "<enclosure url=\"".$files['url'][$k]."\" length=\"".$files['filesize'][$k]."\" type=\"".$files['mime'][$k]."\" />\n";
								$xml .=	"</item>\n";
							}
						}
						$xml .= "</channel>\n".
								"</rss>";
						$oldfeed = file_get_contents($feed);
						if($xml != $oldfeed) file_put_contents($feed, $xml);
					}	
				}
			}
			date_default_timezone_set($original_timezone);
			exit;
		}
	}
}