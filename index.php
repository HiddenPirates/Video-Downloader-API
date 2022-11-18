<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_REQUEST['video-url'])) {
	
	include('other_php_files/functions.php');
	include('other_php_files/arrays.php');
	include('classes/VideoGetterClass.php');



	$video_link = $_REQUEST['video-url'];

	if (!isValidURL($video_link) || empty($video_link)) {
		
		$result = array(
			'status' => 'error', 
			'message' => 'Not a valid URL'
		);

		header('Content-Type: application/json');
		echo json_encode($result);
		die();
	}


	$url_host = parse_url($video_link)['host'];

	if (in_array($url_host, $facebookHosts)) {
		VideoGetterClass::getFacebookVideoInfoByParsing($video_link);
	}
	elseif (in_array($url_host, $instagramHosts)) {
		VideoGetterClass::getInstagramVideoInfoByParsing($video_link);
	}
	elseif (in_array($url_host, $dailymotionHosts)) {
		VideoGetterClass::getDailymotionVideoInfoByParsing($video_link);
	}
	else{

		$youtube_dl_provided_json = shell_exec("youtube-dl --skip-download --dump-single-json --no-warnings " .$video_link. " 2>&1");

		if (isJSON($youtube_dl_provided_json)) {

			$url_host = parse_url($video_link)['host'];

			if (in_array($url_host, $facebookHosts)) {
				VideoGetterClass::getFacebookVideoInfoByParsing($video_link);
			}
			else{
				$youtube_dl_provided_json_decoded_array = json_decode($youtube_dl_provided_json, true);
				VideoGetterClass::getVideoInfo($youtube_dl_provided_json_decoded_array);
			}
		}
		else{
			http_response_code(410);
			die('Invalid json provided from youtube-dl.');
		}
	}
}
else{
	http_response_code(406);
	die('Required valid video url.');
}

?>