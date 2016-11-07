<?php

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

function getDetails($query, $root, $filename){

	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://api.themoviedb.org/3/search/multi?query=" . urlencode($query) . "&language=en-US&api_key=18295317f7bea08c23f44598e143b3e3",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_POSTFIELDS => "{}",
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);
	
	$response_decode = json_decode($response, true);
	curl_close($curl);

	if ($err) {
	  return -1;
	} else {
	  	if($response_decode["total_results"] != 0){
	  		
	  		$response_decode["results"][0]["root"] = $root;
			$response_decode["results"][0]["filename"] = $filename;

	  		return $response_decode["results"][0];
		}
		else {
			return -1;
		}
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

// appends to output.json
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

	// retrieve the name from upload.json
	$title = array_key_exists("title",$json_data[$index]) ? $json_data[$index]["title"] : null;

	$root = $json_data[$index]["root"];
	$filename = $json_data[$index]["root"];

	if($title == null){
		
		send_update('update-list','Invalid: not recognised');

	}	
	else {
		//get the json object for a given title
		$result = getDetails($title, $root, $filename);
		//print_r($result);

		// check to see if json object already exists
		if( $result != -1 ){
			if( ($out_exists && !check_id_exist($result['id'])) || !$out_exists){
				
				send_update('update-list',json_encode($result));
				$json_out_data[] = $result;
			}
		}
		else{

			send_update('update-list','Invalid: ' . $title);
		}
	}
}

//echo json_encode($details);
$json_output = fopen("output.json", "w") or die("Unable to open file");
fwrite($json_output, json_encode($json_out_data));
fclose($json_output);

send_update('close-update-list','Close');
?>
