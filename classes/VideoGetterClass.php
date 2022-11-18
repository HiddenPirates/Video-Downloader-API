<?php

include('parser.php');

class VideoGetterClass 
{
	public static function getVideoInfo($decoded_array)
	{
		$extractor = null;
		$video_title = "F5 Video Downloader";
		$video_thumbnail = "https://i.ibb.co/LrBckSg/no-thumbnail.jpg";
		$video_links = array();
		$qualities = array();
		$audio_link = null;
		$error_status = array(
			'status' => 'error',
			'message' => 'No download link found.',
		);

		if (isset($decoded_array['entries'])) {

			// print_r($decoded_array['entries']);
			if (isset($decoded_array['entries'][0]['title']))
				$video_title = $decoded_array['entries'][0]['title'];

			if (isset($decoded_array['entries'][0]['extractor'])) {
				$extractor = $decoded_array['entries'][0]['extractor'];
			}

			if (isset($decoded_array['entries'][0]['thumbnail']))
				$video_thumbnail = $decoded_array['entries'][0]['thumbnail'];

			if (isset($decoded_array['entries'][0]['formats'])) {
				
				foreach ($decoded_array['entries'][0]['formats'] as $video_info) {
					
					if (strtolower($video_info['ext']) == "mp4") {

						if (strtolower($video_info['protocol']) == "https" || strtolower($video_info['protocol']) == "http") {

							if (!in_array($video_info['url'], $video_links)) {

								$video_links[] = $video_info['url'];
								$qualities[] = $video_info['height'];
							}
						}
					}
					elseif (strtolower($video_info['ext']) == "m4a") {
						if (strtolower($video_info['protocol']) == "https" || strtolower($video_info['protocol']) == "http") {
							$audio_link = $video_info['url'];
						}
					}
				}
			}

			if (!empty($video_links)) {
				
				$video_final_array = array();
				$audio_final_array = array(
					'audio_link' => $audio_link,
					'audio_size' => fileSizeFormatter(getFileSizeFromUrl($audio_link)),
					'audio_extension' => 'm4a',
				);

				$v_size_temp = array();

				foreach ($video_links as $link) {
						
					$size = getFileSizeFromUrl($link);

					if (is_numeric($size)) {
						
						if (!in_array($size, $v_size_temp)) {
							
							$video_final_array[] = array(
								'video_link' => $link, 
								'video_size' => fileSizeFormatter($size),
								'video_extension' => 'mp4',
							);
						}

						$v_size_temp[] = $size;
					}
					else{

						$video_final_array[] = array(
							'video_link' => $link, 
							'video_size' => fileSizeFormatter($size),
							'video_extension' => 'mp4',
						);
					}
				}

				$final_array = array(
					'status' => 'ok',
					'extractor' => $extractor,
					'file_title' => str_replace(".", "", $video_title),
					'thumbnail' => $video_thumbnail, 
					'video_info' => $video_final_array,
					'audio_info' => $audio_final_array,
				);

				if (!empty($final_array)) {

					header('Content-Type: application/json');
					echo json_encode($final_array, JSON_PRETTY_PRINT);
				} 
				else{
					die('Try again.');
				}
			}
			else{
				header('Content-Type: application/json');
				echo json_encode($error_status, JSON_PRETTY_PRINT);
				die();
			}
		}
		else{

			if (isset($decoded_array['title']))
				$video_title = $decoded_array['title'];

			if (isset($decoded_array['extractor'])) {
				$extractor = $decoded_array['extractor'];
			}

			if (isset($decoded_array['thumbnail']))
				$video_thumbnail = $decoded_array['thumbnail'];

			if (isset($decoded_array['formats'])) {
				
				foreach ($decoded_array['formats'] as $video_info) {
					
					if (strtolower($video_info['ext']) == "mp4") {

						if (strtolower($video_info['protocol']) == "https" || strtolower($video_info['protocol']) == "http") {

							if (!in_array($video_info['url'], $video_links)) {
								$video_links[] = $video_info['url'];
								$qualities[] = $video_info['height'];
							}
						}
					}
					elseif (strtolower($video_info['ext']) == "m4a") {
						if (strtolower($video_info['protocol']) == "https" || strtolower($video_info['protocol']) == "http") {
							$audio_link = $video_info['url'];
						}
					}
				}
			}

			if (!empty($video_links)) {
				
				$video_final_array = array();
				$audio_final_array = array(
					'audio_link' => $audio_link,
					'audio_size' => fileSizeFormatter(getFileSizeFromUrl($audio_link)),
					'audio_extension' => 'm4a',
				);

				$v_size_temp = array();

				$i = 0;

				foreach ($video_links as $link) {

					$size = getFileSizeFromUrl($link);

					if (is_numeric($size)) {
						
						if (!in_array($size, $v_size_temp)) {
							
							$video_final_array[] = array(
								'video_link' => $link, 
								'video_size' => fileSizeFormatter($size),
								'video_extension' => 'mp4',
								'quality' => $qualities[$i],
							);
						}

						$v_size_temp[] = $size;
					}
					else{

						$video_final_array[] = array(
							'video_link' => $link, 
							'video_size' => fileSizeFormatter($size),
							'video_extension' => 'mp4',
							'quality' => $qualities[$i],
						);
					}

					$i++;
				}

				$final_array = array(
					'status' => 'ok',
					'extractor' => $extractor,
					'file_title' => str_replace(".", "", $video_title),
					'thumbnail' => $video_thumbnail, 
					'video_info' => $video_final_array,
					'audio_info' => $audio_final_array,
				);

				if (!empty($final_array)) {

					header('Content-Type: application/json');
					echo json_encode($final_array, JSON_PRETTY_PRINT);
				} 
				else{
					die('Try again.');
				}
			}
			else{
				header('Content-Type: application/json');
				echo json_encode($error_status, JSON_PRETTY_PRINT);
				die();
			}
		}
	}

	// ==================================================================

	public static function getFacebookVideoInfo($video_link)
	{
		$html = get_html($video_link);

		$extractor = "facebook";
		$hd_src = get_string_between($html, 'hd_src:"', '",');
		$sd_src = get_string_between($html, 'sd_src:"', '",');
		$audio_src = get_string_between($html, 'audio:[{url:"', '",');
		$video_title = get_string_between($html, '"headline":"', '",');
		$video_thumbnail = urldecode(get_string_between($html, '"thumbnailUrl":"', '",'));

		$video_final_array = array();
		$audio_final_array = array(
			'audio_link' => $audio_src,
			'audio_size' => fileSizeFormatter(getFileSizeFromUrl($audio_src)),
			'audio_extension' => 'm4a',
		);

		if (empty($video_title)) {
			$video_title = "F5 Video Downloader";
		}


		if ($sd_src != "" || $sd_src != null) {

			if ($hd_src == null) {
				
				$video_final_array[] = array(
					'video_link' => $sd_src, 
					'video_size' => fileSizeFormatter(getFileSizeFromUrl($sd_src)),
					'video_extension' => 'mp4',
					'quality' => 'SD',
				);
			}
			else{

				$video_final_array[] = array(
					'video_link' => $hd_src, 
					'video_size' => fileSizeFormatter(getFileSizeFromUrl($hd_src)),
					'video_extension' => 'mp4',
					'quality' => 'HD',
				);

				$video_final_array[] = array(
					'video_link' => $sd_src, 
					'video_size' => fileSizeFormatter(getFileSizeFromUrl($sd_src)),
					'video_extension' => 'mp4',
					'quality' => 'SD',
				);
			}

			$final_array = array(
				'status' => 'ok',
				'extractor' => $extractor,
				'file_title' => str_replace(".", "", $video_title),
				'thumbnail' => $video_thumbnail, 
				'video_info' => $video_final_array,
				'audio_info' => $audio_final_array,
			);

			header('Content-Type: application/json');
			echo json_encode($final_array, JSON_PRETTY_PRINT);
		}
		else{
			die('Something gone wrong.');
		}
	}

	// ========================================================================================================

	public static function getFacebookVideoInfoByParsing($video_link)
	{
		$url2 = "https://downvideo.net/download.php";

		$parameters = array('URL' => $video_link);

		$options = array(
			'http' => array(
			    'method'  => 'POST',
			    'content' => http_build_query($parameters),
			    'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
		                    "Content-Length: ".strlen(http_build_query($parameters))."\r\n".
		                    "User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:90.0) Gecko/20100101 Firefox/90.0\r\n",
			)
		);

		$context  = stream_context_create($options);
		$result = file_get_contents($url2, false, $context);

		$dom2 = new DomDocumentParser($result);

		$hd_word_to_search = "high quality";
		$sd_word_to_search = "normal quality";

		$hd_link = null;
		$sd_link = null;

		$video_thumbnail = "https://i.ibb.co/LrBckSg/no-thumbnail.jpg";
		$video_title = "F5 Video Downloader";
		$audio_link = null;
		$extractor = "facebook";

		foreach ($dom2->getLinks() as $link) {
			
			if (is_contain($hd_word_to_search, strtolower($link->nodeValue))) {

				if (isValidURL($link->getAttribute('href'))) {
					$hd_link = $link->getAttribute('href');
				}
			}
			elseif (is_contain($sd_word_to_search, strtolower($link->nodeValue))) {
				
				 if (isValidURL($link->getAttribute('href'))) {
				 	$sd_link = $link->getAttribute('href');
				 }
			}
		}

		foreach ($dom2->getImgs() as $img) {
			
			if (strtolower($img->getAttribute('class')) == "img-thumbnail") {

				if (isValidURL($img->getAttribute('src'))) {
					$video_thumbnail = $img->getAttribute('src');
				}
			}
		}

		foreach ($dom2->getElementsByTagName('p') as $tag) {

			if ($tag->parentNode->tagName == "div") {
				
				if ($tag->parentNode->getAttribute('class') !== null) {
					
					if ($tag->parentNode->getAttribute('class') == "col-md-12") {
						
						if (isset($tag->firstChild)) {
							
							if ($tag->firstChild->tagName == 'strong') {

								$video_title = $tag->nodeValue;
							}
						}
					}
				}
			}
		}

		if ($sd_link == null && $hd_link == null) {

			$result = array(
				'status' => 'error', 
				'message' => 'Sorry! This video is private! Access denied from Facebook! Try another video!',
			);

			header('Content-Type: application/json');
			echo json_encode($result);
			die();
		}

		$video_final_array = array();

		$audio_final_array = array(
			'audio_link' => $audio_link,
			'audio_size' => null,
			'audio_extension' => null,
		);
		
		if ($hd_link !== null) {
			
			$video_final_array[] = array(
				'video_link' => $hd_link, 
				'video_size' => fileSizeFormatter(getFileSizeFromUrl($hd_link)),
				'video_extension' => 'mp4',
				'quality' => 'SD',
			);
		}
		
		if ($sd_link !== null) {
			
			$video_final_array[] = array(
				'video_link' => $sd_link, 
				'video_size' => fileSizeFormatter(getFileSizeFromUrl($sd_link)),
				'video_extension' => 'mp4',
				'quality' => 'HD',
			);
		}

		
		if (!empty($video_final_array)) {
			
			$final_array = array(
				'status' => 'ok',
				'extractor' => $extractor,
				'file_title' => str_replace(".", "", $video_title), 
				'thumbnail' => $video_thumbnail, 
				'video_info' => $video_final_array,
				'audio_info' => $audio_final_array,
			);
		} else{

			$final_array = array(
				'status' => 'error',
				'message' => 'Sorry! This video is private! Access denied from Facebook! Try another video!',
			);
		}
		

		header('Content-Type: application/json');
		echo json_encode($final_array, JSON_PRETTY_PRINT);
	}

	// ========================================================================================================
	//  ar ekta option achhe https://api.y2mate.guru/api/convert
	// ========================================================================================================


	public static function getInstagramVideoInfoByParsing($video_link){

		$url2 = "https://api.y2mate.guru/api/convert";

		$parameters = array('url' => $video_link);

		$options = array(
			'http' => array(
			    'method'  => 'POST',
			    'content' => http_build_query($parameters),
			    'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
		                    "Content-Length: ".strlen(http_build_query($parameters))."\r\n".
		                    "User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:90.0) Gecko/20100101 Firefox/90.0\r\n",
			)
		);

		$context  = stream_context_create($options);
		$result = file_get_contents($url2, false, $context);


		$video_thumbnail = "https://i.ibb.co/LrBckSg/no-thumbnail.jpg";
		$video_title = "F5 Video Downloader";
		$audio_link = null;
		$extractor = "instagram";

		$video_final_array = array();

		$audio_final_array = array(
			'audio_link' => $audio_link,
			'audio_size' => null,
			'audio_extension' => null,
		);

		if (isJSON($result)) {

			$result = json_decode($result, true);

			if (isset($result['meta']['title'])) {
				$video_title = $result['meta']['title'];
			}

			if (isset($result['thumb'])) {
				$video_thumbnail = $result['thumb'];
			}
			
			if (isset($result['sd']['url'])) {
				
				$video_final_array[] = array(
					'video_link' => $result['sd']['url'], 
					'video_size' => fileSizeFormatter(getFileSizeFromUrl($result['sd']['url'])),
					'video_extension' => 'mp4',
					'quality' => 'SD',
				);
			}

			if (isset($result['hd']['url'])) {
				
				$video_final_array[] = array(
					'video_link' => $result['hd']['url'], 
					'video_size' => fileSizeFormatter(getFileSizeFromUrl($result['hd']['url'])),
					'video_extension' => 'mp4',
					'quality' => 'HD',
				);
			}

			$final_array = array(
				'status' => 'ok',
				'extractor' => $extractor,
				'file_title' => str_replace(".", "", $video_title),
				'thumbnail' => $video_thumbnail, 
				'video_info' => $video_final_array,
				'audio_info' => $audio_final_array,
			);

			header('Content-Type: application/json');
			echo json_encode($final_array, JSON_PRETTY_PRINT);
		}
		else{
			$result = array(
				'status' => 'error', 
				'message' => 'Script blocked by the server! Please report to admin!'
			);

			header('Content-Type: application/json');
			echo json_encode($result);
			die();
		}
	}

	// ========================================================================================================

	public static function getDailymotionVideoInfoByParsing($video_link){

		$url2 = "https://dmvideo.download/?url=";

		$result = get_html($url2.$video_link);

		$dom2 = new DomDocumentParser($result);


		$video_title = "F5 Video Downloader";
		$video_thumbnail = "https://www.dailymotion.com/thumbnail/video/" . strtok(basename($video_link), '?');
		$audio_link = null;

		$video_links = array();

		$video_final_array = array();

		$audio_final_array = array(
			'audio_link' => $audio_link,
			'audio_size' => null,
			'audio_extension' => null,
		);

		foreach ($dom2->getElementsByTagName("tbody") as $nodes) {
			
			foreach ($nodes->childNodes as $tr) {

				$temp_quality = explode("x", $tr->firstChild->nodeValue);
				$temp_quality = end($temp_quality);

				$temp_array = array(
					'link' => $tr->lastChild->firstChild->getAttribute("href"),
					'quality' => $temp_quality,
					'size' => $tr->childNodes->item(2)->nodeValue,
				);

				$video_links[] = $temp_array;

				if ($tr->lastChild->firstChild->getAttribute("download") !== null) {
					
					if ($tr->lastChild->firstChild->getAttribute("download") !== "") {
						$video_title = $tr->lastChild->firstChild->getAttribute("download");
					}
				}
			}
		}

		foreach ($video_links as $video) {
			
			$video_final_array[] = array(
				'video_link' => $video['link'], 
				'video_size' => $video['size'],
				'video_extension' => 'mp4',
				'quality' => $video['quality'],
			);

		}

		$final_array = array(
			'status' => 'ok',
			'extractor' => 'dailymotion',
			'file_title' => str_replace(".", "", $video_title),
			'thumbnail' => $video_thumbnail, 
			'video_info' => $video_final_array,
			'audio_info' => $audio_final_array,
		);

		header('Content-Type: application/json');
		echo json_encode($final_array, JSON_PRETTY_PRINT);
	}
}

