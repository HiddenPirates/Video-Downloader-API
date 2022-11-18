<?php

function isValidURL($url){
	if (filter_var($url, FILTER_VALIDATE_URL) == false) {
		return false;
	}
	else{
		return true;
	}
}

// ----------------------------------------------------------
function isSupportedVideoLink($video_link, $supported_hosts){

	$host = parse_url($video_link)['host'];

	if (in_array($host, $supported_hosts)) {
		return true;
	}
	else{
		return false;
	}
}

// -----------------------------------------------------------
function isJSON($json){
	json_decode($json);
	return json_last_error() === JSON_ERROR_NONE;
}

// -----------------------------------------------------------
function getFileSizeFromUrl($url){
	$curl = curl_init();
	curl_setopt_array($curl, array(    
	   CURLOPT_URL => $url,
	   CURLOPT_HEADER => true,
	   CURLOPT_RETURNTRANSFER => true,
	   CURLOPT_NOBODY => true,
	   CURLOPT_FOLLOWLOCATION 	=> 1,
		CURLOPT_ENCODING       	=> '',
		CURLOPT_COOKIEFILE     	=> '',
		CURLOPT_USERAGENT      	=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.164 Safari/537.36',
		CURLOPT_SSL_VERIFYHOST 	=> 0,
		CURLOPT_SSL_VERIFYPEER 	=> 0,
		CURLOPT_FAILONERROR	   	=> 1,

	));
	 $data = curl_exec($curl);
	 $size = curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

	 curl_close($curl);

	 if ($size > 0) {
	 	return $size;
	 }
	 else{
	 	return "Unknown";
	 }
 }

// -------------------------------------------------------------------------
 function fileSizeFormatter($fileSize) {

 	if ($fileSize < 1024) {
 		return $fileSize;
 	}
 	elseif ($fileSize >= 1024 && $fileSize < 1048576) {
 		$size = $fileSize/1024;
 		return round($size, 2) . "KB";
 	}
 	elseif ($fileSize >= 1048576 && $fileSize < 1073741824) {
 		$size = $fileSize/1048576;
 		return round($size, 2) . "MB";
 	}
 	elseif ($fileSize >= 1073741824 && $fileSize < 1099511627776) {
 		$size = $fileSize/1073741824;
 		return round($size, 2) . "GB";
 	}
 	elseif ($fileSize >= 1099511627776 && $fileSize < 1125899906842624) {
 		$size = $fileSize/1099511627776;
 		return round($size, 2) . "TB";
 	}
 	else{
 		return "Huge Size";
 	}
 }


// ----------------------------------------------------------

 function get_html($url){
	$curl = curl_init();
	curl_setopt_array($curl, array(    
	   CURLOPT_URL => $url,
	   CURLOPT_HEADER => false,
	   CURLOPT_RETURNTRANSFER  => true,
	   CURLOPT_FOLLOWLOCATION 	=> 1,
		CURLOPT_USERAGENT      	=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:90.0) Gecko/20100101 Firefox/90.0',
		CURLOPT_SSL_VERIFYHOST 	=> 2,
		CURLOPT_SSL_VERIFYPEER 	=> 0,
		CURLOPT_FAILONERROR	   	=> 1,
		CURLOPT_POST			=> false,
		CURLOPT_COOKIEJAR 		=> getcwd() . '/cookies_facebook.txt',
		CURLOPT_COOKIEFILE		=> getcwd() . '/cookies_facebook.txt',

	));
	 $data = curl_exec($curl);
	 curl_close($curl);

	 return $data;
 }


 function get_html2($url){
 	$options = array(
		'http' => array(
			'method' => 'GET',
			'header' => 'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:90.0) Gecko/20100101 Firefox/90.0',
		),
		
		'ssl' => [
	        'verify_peer' => false,
	        'verify_peer_name' => false,
	    ],
	);

	$context = stream_context_create($options);

	$html_data = file_get_contents($url, false, $context);
	return $html_data;
 }

 // -----------------------------------------------------------

 function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

// -------------------------------------------------------------

function is_contain($word_to_search, $sentence){

	if (strpos($sentence, $word_to_search) !== false) {
	    return true;
	}
	else{
		return false;
	}
}