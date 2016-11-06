<?php

function getInputJson(){

	$output = exec("python guess.py");
	return $output;
}

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
	  CURLOPT_URL => "https://api.themoviedb.org/3/movie/" . $id . "?append_to_response=videos%2Ccredits%2Crecommendations&language=en-US&api_key=18295317f7bea08c23f44598e143b3e3",
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
		$movie_detail["type"] = "movie";
		$movie_detail["root"] = $root;
		$movie_detail["filename"] = $filename;
		return $movie_detail;
	}
}

function get_tv_details( $id , $root, $filename){
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://api.themoviedb.org/3/tv/". $id ."?append_to_response=videos%2Ccredits%2Crecommendations&language=en-US&api_key=18295317f7bea08c23f44598e143b3e3",
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
		$tv_detail["type"] = "episode";
		$tv_detail["root"] = $root;
		$tv_detail["filename"] = $filename;
		return $tv_detail;
	}
}

if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {

	$file_content = file_get_contents($_FILES['file']['tmp_name']);

	$file = fopen('upload.txt','w');
	fwrite($file, $file_content);
	fclose($file);

	// Read JSON file
	$json = getInputJson();
	//echo $json;
	//Decode JSON
	$json_data = json_decode($json,true);

	$details = array();

foreach ($json_data as $index => $moviearray) {
		if($json_data[$index]["type"] == "movie"){
			$movie_name = $json_data[$index]["title"];
			$release_year = $json_data[$index]["year"];
			$root = $json_data[$index]["root"];
			$filename = $json_data[$index]["root"];
			$query = "movie?year=" . $release_year . "&query=" . urlencode($movie_name);
			$id = search($query);
			if( $id != -1 ){
				$details[] = get_movie_details($id, $root, $filename);
			}
			else {
				echo 'Invalid';
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
				$details[] = get_tv_details($id, $root, $filename);
			}
			else {
				echo 'Invalid';
			}
		}
		else {
			#TODO Handling multi request
		}
}

echo json_encode($details);
$json_output = fopen("output.json", "w+") or die("Unable to open file");
fwrite($json_output,  json_encode($details));
fclose($json_output);



}
else{
	echo -1;
}


?>
