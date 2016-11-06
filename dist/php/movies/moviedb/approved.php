<?php

$curl = curl_init();
$request_token = $_GET["request_token"];

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.themoviedb.org/3/authentication/session/new?request_token=" .$request_token. "&api_key=18295317f7bea08c23f44598e143b3e3",
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

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  $response_decode = json_decode($response, true);
  if( $response_decode["success"] == true ){
    $session_id = $response_decode["session_id"];
    $_SESSION["user_id"] = $session_id;
    echo 'Session created';
    #$session_id has the session id for the particular user
  }
  else {
    $session_id = -1;
    echo 'Session Denied';
  }
}
