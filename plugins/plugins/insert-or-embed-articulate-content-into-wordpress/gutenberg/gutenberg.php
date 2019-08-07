<?php

function articulate_enqueue_gutenberg_scripts() {
	wp_enqueue_script( 'articulate-gutenberg-block', WP_QUIZ_EMBEDER_PLUGIN_URL . 'gutenberg/build/block.js?v=429992', array( 'wp-api', 'wp-i18n', 'wp-blocks', 'wp-components', 'wp-compose', 'wp-data', 'wp-editor', 'wp-element' ), filemtime( WP_QUIZ_EMBEDER_PLUGIN_DIR . '/gutenberg/build/block.js' ), true );

	wp_localize_script( 'articulate-gutenberg-block', 'articulateOptions', array(
		'options' => get_quiz_embeder_options(),
		'uploadData' => get_rest_url(null, 'articulate/v1/upload-data'),
		'dir' => count( getDirs() ),
		'count' => quiz_embeder_count(),
		'plupload' => array(
				'chunk_size' => articulate_get_upload_chunk_size(),
				'max_retries' => 10
			)
	) );

	wp_enqueue_style( 'articulate-gutenberg-block', WP_QUIZ_EMBEDER_PLUGIN_URL . 'gutenberg/build/block.css?v=429992' );
}

add_action( 'enqueue_block_editor_assets', 'articulate_enqueue_gutenberg_scripts' );

function articulate_register_rest_endpoints() {
	
	if( ! is_user_logged_in() || ! current_user_can( 'upload_files' ) ) 
	{
		return;
	}

	register_rest_route(
		'articulate/v1',
		'/upload-data',
		array(
			'methods'  => 'POST',
			'callback' => 'articulate_upload_form_data',
		)
	);

	register_rest_route(
		'articulate/v1',
		'/get-data',
		array(
			'methods'  => 'GET',
			'callback' => 'articulate_get_dir_data',
		)
	);

	register_rest_route(
		'articulate/v1',
		'/delete-data',
		array(
			'methods'  => 'POST',
			'callback' => 'articulate_delete_dir_data',
		)
	);

	register_rest_route(
		'articulate/v1',
		'/rename-data',
		array(
			'methods'  => 'POST',
			'callback' => 'articulate_gutenberg_rename_dir',
		)
	);
}

add_action( 'rest_api_init', 'articulate_register_rest_endpoints' );

function articulate_get_dir_data() {
	$dirs = getDirs();

	if ( count( $dirs ) > 0 ) {
		return $dirs;
	}
}

function articulate_delete_dir_data( $data ) {
	$dirname = sanitize_file_name( $data['dir'] );
	$dir = getUploadsPath() . $dirname;
	articulate_rrmdir( $dir );
	return articulate_get_dir_data();
}

function articulate_register_block() {
	register_block_type(
		'e-learning/block', array(
			'render_callback' => 'articulate_gutenberg_block_callback',
			'attributes'      => array(
				'src'            => array(
					'type' => 'string',
				),
				'href'            => array(
					'type' => 'string',
				),
				'type'           => array(
					'type' => 'string',
					'default' => 'iframe',
				),
				'width'          => array(
					'type' => 'string',
					'default' => '100%',
				),
				'height'         => array(
					'type' => 'string',
					'default' => '600',
				),
				'ratio'          => array(
					'type' => 'string',
					'default' => '4:3',
				),
				'frameborder'    => array(
					'type' => 'string',
					'default' => '0',
				),
				'scrolling'      => array(
					'type' => 'string',
					'default' => 'no',
				),
				'title'          => array(
					'type' => 'string',
				),
				'link_text'      => array(
					'type' => 'string',
				),
				'button'         => array(
					'type' => 'string',
				),
				'scrollbar'      => array(
					'type' => 'string',
				),
				'colorbox_theme' => array(
					'type' => 'string',
				),
				'size_opt'       => array(
					'type' => 'string',
				),
			),
		)
	);
}

add_action( 'init', 'articulate_register_block' );

function articulate_gutenberg_block_callback( $attr ) {
	if ( ! isset( $attr['href'] ) && ! isset( $attr['src'] ) ) {
		return;
	} else {
		if ( $attr['type'] === 'iframe' || $attr['type'] === 'iframe_responsive' ) {
			unset( $attr['href'] );
		} else {
			unset( $attr['src'] );
		}
	
		$params = wp_parse_args( array_filter( $attr ) );

		if ( ! empty( $attr['src'] ) || ! empty( $attr['href'] ) ) {
			return iframe_handler( $params, '' );
		}
	}
}


function articulate_upload_form_data( $data ) {

	$count_dirs = getDirs();
	
	// you can use WP's wp_handle_upload() function:
	$file = $_FILES['file'];
	$upload_dir = wp_upload_dir();
	$dir = '' . $upload_dir['basedir'] . '/articulate_uploads';
	$dir = untrailingslashit( getUploadsPath() );
	if (empty($_FILES) || $_FILES['file']['error'])
	{
		die(json_encode( array( "OK"=> 0, "info"=> __( "Failed to move uploaded file.  Please check if the folder has write permissions.", 'quiz'))));
	}
	$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
	$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
	$fileName = isset($_REQUEST["name"]) ? sanitize_file_name($_REQUEST["name"]) : sanitize_file_name($_FILES['file']["name"]);
	$filePath = "".$dir."/".sanitize_file_name($fileName)."";
	// Open temp file
	$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
	if ($out)
	{
		// Read binary input stream and append it to temp file
		$in = @fopen($_FILES['file']['tmp_name'], "rb");
		if ($in)
		{
			while ($buff = fread($in, 4096))
			fwrite($out, $buff);
		} 
		else
		die(json_encode( array( "OK"=> 0, "info"=> __( "Failed to open input stream. Please check if the folder has write permissions", 'quiz'))));
		
		@fclose($in);
		@fclose($out);
		@unlink($_FILES['file']['tmp_name']);
	} 
	else
	die(json_encode( array( "OK"=> 0, "info"=> __( "Failed to open output stream.  Please check if the folder has write permissions", 'quiz'))));
	// Check if file has been uploaded
	if (!$chunks || $chunk == $chunks - 1)
	{
		// Strip the temp .part suffix off
		articulate_custom_rename("{$filePath}.part", $filePath);
		#start extracting
		#unzip file
		$dir = explode(".",$fileName);
		$dir[0] = str_replace(" ","_",$dir[0]);		
		$target = getUploadsPath() . $dir[0];
		$file = $filePath;
		while(file_exists($target))
		{
			$r = rand(1,10);
			$target .= $r;
			$dir[0] .= $r;
		}
		$ext = pathinfo ($filePath ,PATHINFO_EXTENSION);
		if( $ext == 'mp4')
		{
			$arr = articulate_process_single_mp4_file_upload($file,$target,$dir[0]);
		}
		else
		{
			$arr = extractZip($file,$target,$dir[0]);
		}
		
		unlink($filePath);
		do_action('iea/uploaded_quiz', $arr,$target);
		$ok = isset( $arr[4] ) ? $arr[4] : 0;
		die('{"OK": '.json_encode($ok).', "info": '.json_encode($arr[0]).', "folder" : '.json_encode($arr[2]).', "path" : '.json_encode($arr[1]).', "name" : '.json_encode($arr[3]).', "target": "'.$target.'"}');
	}
	else
	{
		die(json_encode( array( "OK"=> 1, "info"=> __("Uploading chunks!", 'quiz'))));
	}

	
	
	exit;
}

function articulate_gutenberg_rename_dir( $data ) {
	
	$dir_name = ( isset($data['dir_name']) ) ? $data['dir_name'] : "";
	$dir_name = sanitize_file_name( $dir_name );

	$title = ( isset($data['title']) ) ? $data['title'] : "";
	$title = sanitize_file_name( $title );

	$arr=array();
	if($dir_name!="") {
		$target = getUploadsPath() . $dir_name;
		if(file_exists($target))
		{
			
			if($title)
			{   
				$title=str_replace(" ","_" , $title);
				$new_file= getUploadsPath() . $title;
				while(file_exists($new_file))
				{
					$r = rand(1,10);
					$new_file .= $r;
					$title .= $r;
				}
				articulate_custom_rename($target, $new_file);
				$arr[0]="success";
				$arr[1]=$title;
			}
			else
			{
				$arr[0]="error";
				$arr[1]=__("Failed: New Title Was Not Given", 'quiz');
			}
			
		}
		else
		{
			$arr[0]="error";
			$arr[1]=__("Failed: Given File is Not Exits", 'quiz');
		}
	}
	else
	{
		$arr[0]="error";
		$arr[1]=__("Failed: Targeted Directory Name Was Not Given", 'quiz');
	}
	echo json_encode($arr);	
	die();
}
