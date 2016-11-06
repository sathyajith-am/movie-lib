<?php

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

function search($query){
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://api.themoviedb.org/3/search/". $query ."&language=en-US&api_key=18295317f7bea08c23f44598e143b3e3",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_POSTFIELDS => "{}",
	  CURLOPT_HTTPHEADER => array(
	    "content-type: application/json"
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);
	$response_decode = json_decode($response, true);
	curl_close($curl);

	if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
		if($response_decode["total_results"] != 0){
	  	return $response_decode["results"][0]["id"];
		}
		else {
			return -1;
		}
	}
}


function get_movie_details( $id, $root, $filename ){
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://api.themoviedb.org/3/movie/" . $id . "?anguage=en-US&api_key=18295317f7bea08c23f44598e143b3e3",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_POSTFIELDS => "{}",
	  CURLOPT_HTTPHEADER => array(
	    "content-type: application/json"
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);
	$response_decode = json_decode($response, true);

	curl_close($curl);

	if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
		$movie_detail = array();
		$movie_detail["id"] = $response_decode["id"];
		$movie_detail["title"] = $response_decode["original_title"];
		$movie_detail["genres"] = $response_decode["genres"];
		$movie_detail["poster_path"] = $response_decode["poster_path"];
		$movie_detail["type"] = "Movies";
		$movie_detail["root"] = $root;
		$movie_detail["filename"] = $filename;
		return $movie_detail;
	}
}

function get_tv_details( $id , $root, $filename){
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://api.themoviedb.org/3/tv/". $id ."?language=en-US&api_key=18295317f7bea08c23f44598e143b3e3",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_POSTFIELDS => "{}",
	  CURLOPT_HTTPHEADER => array(
	    "content-type: application/json"
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);
	$response_decode = json_decode($response, true);


	curl_close($curl);

	if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
		$tv_detail = array();

		$tv_detail["id"] = $response_decode["id"];
		$tv_detail["title"] = $response_decode["name"];
		$tv_detail["genres"] = $response_decode["genres"];
		$tv_detail["poster_path"] = $response_decode["poster_path"];
		$tv_detail["type"] = "TV Shows";
		$tv_detail["root"] = $root;
		$tv_detail["filename"] = $filename;
		return $tv_detail;
	}
}

function send_update($event,$message){
	echo "event: $event\n";
	echo "data: $message";
	echo "\n\n";
	ob_flush();
	flush();
}

function check_id_exist($id){

	global $output_dict;
	
	return array_key_exists($id, $output_dict);
}	


$out_exists = false;
$json_out_data = array();
$output_dict = array();

//Decode JSON
$string = file_get_contents('upload.json') or die('no upload file found!'); 
$json_data = json_decode($string,true); 


if(file_exists('output.json')) {
	$out_exists = true;
	$string = file_get_contents('output.json') or die('no output file found!');
	// echo $string;
    $json_out_data = json_decode($string,true); 
    // echo $json_out_data;
    foreach ($json_out_data as $index => $value) {
    	$output_dict[$value['id']] = 1;
    }
    // var_dump($output_dict);
}

//$details = array();

foreach ($json_data as $index => $moviearray) {
		if($json_data[$index]["type"] == "movie"){
			$movie_name = array_key_exists("title",$json_data[$index]) ? $json_data[$index]["title"] : null;
			 
			$release_year = array_key_exists("year",$json_data[$index]) ? $json_data[$index]["year"] : null;
			$root = $json_data[$index]["root"];
			$filename = $json_data[$index]["root"];
			if($movie_name == null)
				$id = -1;
			else{

				$query = "movie?year=" . $release_year . "&query=" . urlencode($movie_name);
				$id = search($query);
			}
			if( $id != -1 ){

				if( ($out_exists && !check_id_exist($id)) || !$out_exists){
					$result = get_movie_details($id, $root, $filename);
					send_update('update-list',json_encode($result));
					$json_out_data[] = $result;
				}
			}
			else {
				send_update('update-list','Invalid');
			}

		}
		else if($json_data[$index]["type"] == "episode"){
			$tv_show_name = $json_data[$index]["title"];
			$release_year = $json_data[$index]["year"];
			$season = $json_data[$index]["season"];
			$episode = $json_data[$index]["episode"];
			$root = $json_data[$index]["root"];
			$filename = $json_data[$index]["root"];
			$query = "tv?query=" . urlencode($tv_show_name);
			$id = search($query);
			if( $id != -1 ){
				
				if( ($out_exists && !check_id_exist($id)) || !$out_exists){
					$result = get_tv_details($id, $root, $filename);
					send_update('update-list',json_encode($result));
					$json_out_data[] = $result;
				}
			}
			else {
				send_update('update-list','Invalid');
			}
		}
		else {
			#TODO Handling multi request
		}
}

//echo json_encode($details);
$json_output = fopen("output.json", "w") or die("Unable to open file");
fwrite($json_output, json_encode($json_out_data));
fclose($json_output);

send_update('close-update-list','Close');
?>
