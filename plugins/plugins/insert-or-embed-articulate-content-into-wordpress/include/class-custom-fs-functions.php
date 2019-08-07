<?php
/**
 * A PHP class of some filesystem related alternative custom functions
 * Author: oneTarek
 * Author URL: http://onetarek.com
 * Version: 1.0.0
 * Source URL : https://github.com/onetarek/php-custom-filesystem-functions
 */ 

class Custom_FS_Functions{
	
	/**
	 * Delete a file or a Directory and Containing Files Recursively
	 * @param string $dir /path/for/the/directory/
	 * @return void
	 **/
	public static function delete( $dir ){
		
	  $dir = rtrim($dir, "/");
	  if ( is_dir( $dir ) )
	  {
	     
	    $dir_handle = opendir( $dir );
	    if( $dir_handle )
	    {       
	      while( $file = readdir( $dir_handle ) ) 
	      {
	           if($file != "." && $file != "..") 
	           {
	                if( ! is_dir( $dir."/".$file ) )
	                {
	                  unlink( $dir."/".$file );
	                }
	                else
	                {
	                  self::delete($dir.'/'.$file);
	                }
	                      
	           }
	      }
	      closedir( $dir_handle );
	    }
	    rmdir( $dir );
	    return true;
	  }
	  else
	  {
	  	unlink( $dir );
	  	return true;
	  }
	  
	}

	/**
	 * Copy a file or a Directory and Containing Files Recursively
	 * @param string $src /path/for/the/directory/
	 * @param string $dst /path/for/the/directory/
	 * @return bool
	 **/
	public static function copy( $src, $dst ){
		$src = rtrim($src, "/");
		$dst = rtrim($dst, "/");
		if( empty($src) || empty($dst) || $src == $dst || !file_exists( $src ) )
		{
			return false;
		}

		if( is_file( $src ) )
		{
			copy( $src, $dst ); 
			return true;
		}
		elseif( is_dir( $src ) )
		{
			@mkdir( $dst );
		    $dir = opendir( $src ); 
		     
		    while( false !== ( $file = readdir( $dir ) ) )
		    { 
		        if( ( $file != '.' ) && ( $file != '..' ) ) 
		        { 
		            self::copy( $src . '/' . $file, $dst . '/' . $file );
		        } 
		    } 

		    closedir( $dir ); 
		    return true;
		}

		return false;
	}

	/**
	 * Rename a file or a Directory.
	 * Some server is unable to handle PHP rename() function. 
	 * For example : Pantheon server https://pantheon.io/docs/platform-considerations/#renamemove-files-or-directories
	 * We should use this alternative rename function only for this type of server.
	 *
	 * @param string $old_path /path/for/the/directory/
	 * @param string $new_path /path/for/the/directory/
	 * @return void
	 **/
	public static function rename( $old_path, $new_path ){
		self::copy($old_path, $new_path );
		self::delete($old_path);
	}

	/**
	 * Move a file or a Directory to new location. This is same as rename.
	 * @param string $old_path /path/for/the/directory/
	 * @param string $new_path /path/for/the/directory/
	 * @return void
	 **/
	public static function move( $old_path, $new_path ){
		self::rename( $old_path, $new_path );
	}

}//end class
