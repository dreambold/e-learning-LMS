<?php

class articulate_license_manager{
	
	
	function __construct( $_file, $_item_name, $_version, $_author, $_optname = null, $_api_url = null ) {
		global $edd_options;

		$this->file           = $_file;
		$this->item_name      = $_item_name;
		$this->item_shortname =  $_optname;
		$this->version        = $_version;
		$this->license        = $_optname;
		$this->author         = $_author;
		$this->api_url        =$_api_url;

		add_action('articulate_licenses', array($this,'license'));
		$this->check_license( $this->api_url,$this->item_name ,get_option($this->license),$this->item_shortname);
		add_action('admin_init', array($this,'deactivate_licenses_button'));
				
		
	}
	
	
	public function deactivate_licenses_button(){
		$deactivate_license_id = @$_GET['deactivate_license_id'];
		if($deactivate_license_id != ''){
			
			 $this->deactivate_license($this->item_name,get_option($deactivate_license_id),$deactivate_license_id);
			 wp_redirect('admin.php?page=articulate-license');
			 exit;
			
		}
	}
	
	public function license(){		
		
		$license = $this->check_license( $this->api_url,$this->item_name ,get_option($this->license),$this->item_shortname,true);
		
		echo '<tr><td>
		<p><strong>'.$this->item_name.'</strong></p>
		<p><em>License Status: '.$license->license.'</em>
		<p> <input style="width:300px" type="text" name="articulate_licenses['.$this->item_shortname.']" value="'.get_option($this->license).'" > 
		<a class="waves-effect waves-light btn"  href="admin.php?page=articulate-license&deactivate_license_id='.$this->item_shortname.'">'.__('Disable License', 'quiz').'</a>
		
		</p></td></tr>';
		
	}
	
	public function activate_license($item_name,$license,$short_name) {
		
		$this->deactivate_license($item_name,get_option($short_name),$short_name);
		update_option( $short_name,trim($license));
		
		// Data to send to the API
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( $item_name )
		);

		// Call the API
		$response = wp_remote_get(
			esc_url_raw( add_query_arg( $api_params, $this->api_url ) ),
			array(
				'timeout'   => 15,
				'body'      => $api_params,				
			)
		);

		// Make sure there are no errors
		if ( is_wp_error( $response ) )
			return;

		// Decode license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		set_transient($short_name, $license_data, 12 * HOUR_IN_SECONDS );

	}
	public function deactivate_license($item_name,$license,$short_name) {
		
		
	delete_transient( $short_name );
		// Data to send to the API
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( $item_name )
		);

		// Call the API
		$response = wp_remote_get(
			esc_url_raw( add_query_arg( $api_params, $this->api_url ) ),
			array(
				'timeout'   => 15,
				'body'      => $api_params,				
			)
		);

		// Make sure there are no errors
		if ( is_wp_error( $response ) )
			return;

		// Decode license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		set_transient($short_name, $license_data, 12 * HOUR_IN_SECONDS );
		update_option( $short_name,'');

	}
	function check_license($store_url,$item_name,$license,$short_name,$force = false) {
	
	if(get_transient($short_name)== false or $force == true){
		delete_transient( $short_name );
		
		
		$api_params = array(
		'edd_action' => 'check_license',
		'license' => $license,
		'item_name' => urlencode( $item_name ),
		'url' => home_url()
	);
	

	$response = wp_remote_post( $store_url, array( 'body' => $api_params, 'timeout' => 15 ) );

	if ( is_wp_error( $response ) ) {
		echo 'Error';
		return false;
  	}

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	set_transient($short_name, $license_data, 12 * HOUR_IN_SECONDS );
		
		
	}
	
	return get_transient($short_name);
	

	
	
}
	

	
}