<?php

if( !class_exists('Quiz_Unzip') ) :

	class Quiz_Unzip{

		private $shell_unzip_possibility_checked = false;
		private $shell_unzip_possible = false;
		private $has_shell_exec = false;
		private $shell_unzip_path = null; 
		private $try_shell_unzip = false;
		
		public function __construct( $try_shell_unzip = false )
		{
			$this->try_shell_unzip = $try_shell_unzip;
		}
		/*
		 * return if unzip command possible or not
		 */
		private function is_shell_unzip_possible()
		{
			if( $this->shell_unzip_possibility_checked )
			{
				$this->shell_unzip_possible; 
			}
			$this->has_shell_exec();
			$this->get_shell_unzip_path();
			$this->shell_unzip_possibility_checked = true;
			if( $this->has_shell_exec && !empty( $this->shell_unzip_path ) )
			{
				$this->shell_unzip_possible = true;
			}
			else
			{
				$this->shell_unzip_possible = false;
			}
			return $this->shell_unzip_possible;

		}

		/**
	     * Checks to see if the server supports issuing commands to shell_exex
	     *
	     * @return bool		Returns true shell_exec can be ran on this server
	     */
		public function has_shell_exec()
		{
			if( $this->shell_unzip_possibility_checked )
			{
				$this->has_shell_exec; 
			}
			$cmds = array('shell_exec', 'escapeshellarg', 'escapeshellcmd', 'extension_loaded');

			//Function disabled at server level
			if( array_intersect( $cmds, array_map( 'trim', explode( ',', @ini_get( 'disable_functions' ) ) ) ) ) 
			{
				$this->has_shell_exec = false;
				return false;
			}

			//Suhosin: http://www.hardened-php.net/suhosin/
			//Will cause PHP to silently fail
			if( extension_loaded('suhosin') )
			{
				$suhosin_ini = @ini_get( "suhosin.executor.func.blacklist" );
				if( array_intersect( $cmds, array_map( 'trim', explode( ',', $suhosin_ini ) ) ) )
				{
					$this->has_shell_exec = false;
					return false;
				}
			}
			// Can we issue a simple echo command?
			if( !@shell_exec( 'echo duplicator' ) ) 
			{
				$this->has_shell_exec = false;
				return false;
			}
			$this->has_shell_exec = true;
			return true;
		}

		/**
	     * Gets the possible system commands for unzip on Linux
	     *
	     * @return string		Returns unzip file path that can execute the unzip command
	     */
		public function get_shell_unzip_path(){
			$filepath = null;

			if($this->has_shell_exec()) 
			{
				if (shell_exec('hash unzip 2>&1') == NULL) 
				{
					$filepath = 'unzip';
				} 
				else 
				{
					$possible_paths = array(
						'/usr/bin/unzip',
						'/opt/local/bin/unzip',
						'/bin/unzip',
						'/usr/local/bin/unzip',
						'/usr/sfw/bin/unzip',
						'/usr/xdg4/bin/unzip',
						'/opt/bin/unzip',					
						// RSR TODO put back in when we support shellexec on windows,
					);

					foreach ($possible_paths as $path)
					{
						if (file_exists($path)) 
						{
							$filepath = $path;
							break;
						}
					}
				}
			}
			$this->shell_unzip_path = $filepath;
			return $filepath;
		}

		/**
	     * Extract archive file using shell command
	     * @param string $archive_filepath	The path to the archive file.
	     * @param string $save_dir_path directory path where the extracted files will be saved
	     * @return bool	Returns true if the data was properly extracted
	     */
		private function extract_zip_using_shell_exec( $archive_filepath, $save_dir_path ){
			if( ! $this->is_shell_unzip_possible() )
			{
				return false;
			}
			$success = false;
			$unzip_path	 = $this->shell_unzip_path;

			if( $unzip_path != null ) 
			{
				$unzip_command	 = $unzip_path.' '.$archive_filepath.' -d '.$save_dir_path;
				$result	 = shell_exec( $unzip_command );
				if($result == null ) 
				{
					//unzip failed
					$success = false;
				} 
				else 
				{
					//unzip success.
					$success = true;
				}
			}

			return $success;
		}

		/*
		 * Unzip file 
		 * try using wp unzip_file function
		 * then try using shell exec
		 */
		public function unzip_file( $zip_file , $target_folder )
		{
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();		
			if(!is_dir($target_folder) )
			{
				mkdir($target_folder, 0777);
			}
			$success = false;
			$success = unzip_file( $zip_file, $target_folder );
			if( ! $success && $this->try_shell_unzip )
			{
				$success = $this->extract_zip_using_shell_exec( $zip_file, $target_folder );
			}
			
			return $success;

		}

	}//end class

endif;