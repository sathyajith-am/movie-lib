<?php
#To use: create_list.php?name=<name of the list>&description=<description of the list>
session_start();
$curl = curl_init();
$session_id = $_SESSION["session_id"];

$name = $_GET["name"];
$description = $_GET["description"];

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.themoviedb.org/3/list?session_id=". $session_id . "&api_key=18295317f7bea08c23f44598e143b3e3",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  #Need to take input from user, name of the list, description of the list, through GET request
  CURLOPT_POSTFIELDS => "{\n  \"name\": \" " . $name . "\",\n  \"description\": \" ". $description ."\",\n  \"language\": \"en\"\n}",
  CURLOPT_HTTPHEADER => array(
    "content-type: application/json;charset=utf-8"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
$response_decode = json_decode($response, true);

curl_close($curl);

if ($err) {
  echo -1;
} else {
  if( isset($response_decode["success"]) && $response_decode["success"] == true ){
    echo $response_decode["list_id"];
    #TODO
    #Need to store list_id somewhere to be able to access it as required to be able to add movies to it accordingly
  }
  else {
    echo -1;
  }
}
