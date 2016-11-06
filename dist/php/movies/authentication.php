<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.themoviedb.org/3/authentication/token/new?api_key=18295317f7bea08c23f44598e143b3e3",
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
  $auth_response = json_decode($response, true);
  redirect($auth_response["request_token"]);
}

function redirect($request_token){
  #Automatically redirects to approved.php after the authentication is complete at TMDB with the given request ID sent as GET parameter
  header('Location: https://www.themoviedb.org/authenticate/' . $request_token . '?redirect_to=http://localhost/moviedb/approved.php');
  die();
}
?>
