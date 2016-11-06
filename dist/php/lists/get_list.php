<?php

$curl = curl_init();
$list_id = $_GET["list_id"];
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.themoviedb.org/3/list/" . $list_id . "?language=en-US&api_key=18295317f7bea08c23f44598e143b3e3",
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
  echo -1;
} else {
    if(isset($response_decode["items"])){
      echo json_encode($response_decode);
    }
}
