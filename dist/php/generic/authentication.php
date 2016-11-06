<?php
session_start();
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
  validate($auth_response["request_token"]);
}

function validate($request_token){

  $curl = curl_init();
  #var_dump($request_token);
  $data = json_decode(file_get_contents('php://input'), true);
  $username = $data["username"];
  $password = $data["password"];

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.themoviedb.org/3/authentication/token/validate_with_login?request_token=" . $request_token . "&password=" . $password ."&username=". $username ."&api_key=18295317f7bea08c23f44598e143b3e3",
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
  #var_dump($response_decode);
  curl_close($curl);

  if ($err) {
    echo "cURL Error #:" . $err;
  } else {
    if( isset($response_decode["success"]) && $response_decode["success"] == true ){
      create_session($request_token);
    }
    else {
      echo -1;
    }
  }
}

function create_session($request_token){
  $curl = curl_init();

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
  }
  else {
    $response_decode = json_decode($response, true);
    if( $response_decode["success"] == true ){
      $session_id = $response_decode["session_id"];

      if(!isset($_COOKIE['session_id'])) {
        setcookie('session_id', $session_id, time() + (86400 * 30), "/", "/", true, false);
        $_SESSION["session_id"] = $session_id;
      }

      echo $_SESSION['session_id'];
    }
    else {
      $session_id = -1;
      echo $sessionId;
    }
  }
}
?>
