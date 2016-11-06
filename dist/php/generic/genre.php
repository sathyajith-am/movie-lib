<?php

$curl = curl_init();

#$genre_ids = json_decode(stripslashes($_POST["genre_ids"]));

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.themoviedb.org/3/genre/movie/list?language=en-US&api_key=18295317f7bea08c23f44598e143b3e3",
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
$genres = array();

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  foreach($response_decode["genres"] as $genre_id){
    echo $genre_id;
    if($genre_id == 28)
    {
      $genres[] = $response_decode["genres"][$genre_id];
    }
  }
}
