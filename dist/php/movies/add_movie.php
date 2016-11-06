<?php
#To use: add_movie.php?list_id=<list ID>&media_id=<movie/tv show ID>
$curl = curl_init();

$list_id = $_GET["list_id"];
$media_id = $_GET["media_id"];
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.themoviedb.org/3/list/". $list_id .  "/add_item?session_id=b6665b3a97dbb81db4e70829083bea9bb4eda906&api_key=18295317f7bea08c23f44598e143b3e3",
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
  echo "cURL Error #:" . $err;
} else {
  if( $response_decode["staus_code"] == 12 ){
    echo 'Item added to the list'
    #TODO Display appropriate message
  }
  else {
    #TODO Display appropriate error message
  }
}
