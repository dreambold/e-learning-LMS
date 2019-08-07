<?php
	/*
		MailChimp GetLists
	*/
@session_start();

function dpProEventCalendar_mailchimp_curl_connect( $url, $request_type, $api_key, $data = array() ) {
	if( $request_type == 'GET' )
		$url .= '?' . http_build_query($data);
 
	$mch = curl_init();
	$headers = array(
		'Content-Type: application/json',
		'Authorization: Basic '.base64_encode( 'user:'. $api_key )
	);
	curl_setopt($mch, CURLOPT_URL, $url );
	curl_setopt($mch, CURLOPT_HTTPHEADER, $headers);
	//curl_setopt($mch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
	curl_setopt($mch, CURLOPT_RETURNTRANSFER, true); // do not echo the result, write it into variable
	curl_setopt($mch, CURLOPT_CUSTOMREQUEST, $request_type); // according to MailChimp API: POST/GET/PATCH/PUT/DELETE
	curl_setopt($mch, CURLOPT_TIMEOUT, 10);
	curl_setopt($mch, CURLOPT_SSL_VERIFYPEER, false); // certificate verification for TLS/SSL connection
 
	if( $request_type != 'GET' ) {
		curl_setopt($mch, CURLOPT_POST, true);
		curl_setopt($mch, CURLOPT_POSTFIELDS, json_encode($data) ); // send data in json
	}
 
	return curl_exec($mch);
}

//require_once (dirname (__FILE__) . '/../mailchimp/miniMCAPI.class.php');

if($_POST['mailchimp_api']!=''){
	$api_key = $_POST['mailchimp_api'];
	$return = '';
	//$mailchimp_class = new mailchimpSF_MCAPI($_POST['mailchimp_api']);
											
	//$retval = $mailchimp_class->lists();
	
	// Query String Perameters are here
	// for more reference please vizit http://developer.mailchimp.com/documentation/mailchimp/reference/lists/
	$data = array(
		'fields' => 'lists' // total_items, _links
	);
	 
	$url = 'https://' . substr($api_key,strpos($api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/';
	$result = json_decode( dpProEventCalendar_mailchimp_curl_connect( $url, 'GET', $api_key, $data) );
	//print_r( $result);
	 

	if( !empty($result->lists) ) {
		$return .= '<select name="mailchimp_list">';
		foreach( $result->lists as $list ){
	
			$return .= '<option value="'.$list->id.'">'.$list->name.'</option>';
	
		}	
		$return .= '</select>';
	} else {
		$return = "Error: No lists found";
	}
	
	die($return);
}
?>