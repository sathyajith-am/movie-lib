<?php
session_start();
$curl = curl_init();

$session_id = $_SESSION['session_id'];

$media_type = $_GET["media_type"];
$media_id = $_GET["media_id"];

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.themoviedb.org/3/account/%7Baccount_id%7D/favorite?session_id=" . $session_id . "&api_key=18295317f7bea08c23f44598e143b3e3",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\n  \"media_type\": \"" . $media_type . "\",\n  \"media_id\": " . $media_id . ",\n  \"favorite\": true\n}",
  CURLOPT_HTTPHEADER => array(
    "content-type: application/json;charset=utf-8"
  ),
));

$response = curl_exec($curl);
$response_decode = json_decode($response, true);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo -1;
} else {
  if( isset($response_decode["status_code"]) && $response_decode["status_code"] == 12 ){
    echo 0;
  }
  else {
    echo -1;
  }
}