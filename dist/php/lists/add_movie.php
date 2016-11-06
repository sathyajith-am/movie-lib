<?php
session_start();
#To use: add_movie.php?list_id=<list ID>&media_id=<movie/tv show ID>
$curl = curl_init();

$session_id = $_SESSION['session_id'];

$list_id = $_GET["list_id"];
$media_id = $_GET["media_id"];
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.themoviedb.org/3/list/". $list_id .  "/add_item?session_id=" . $session_id . "&api_key=18295317f7bea08c23f44598e143b3e3",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\n  \"media_id\": ". $media_id ."\n}",
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
  if( isset($response_decode["status_code"]) && $response_decode["status_code"] == 12 ){
    echo 0;
    #TODO Display appropriate message
  }
  else {
    echo -1;
  }
  echo json_encode($response_decode);
}
