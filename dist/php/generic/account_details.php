<?php
session_start();

$curl = curl_init();

if(isset($_SESSION["session_id"])){
    $session_id = $_SESSION["session_id"];

    //urls for api calls:
    $api = array(
        "details" => "https://api.themoviedb.org/3/account?session_id=",
        "lists" => "https://api.themoviedb.org/3/account/%7Baccount_id%7D/lists?language=en-US&session_id=",
        "favMovies" => "https://api.themoviedb.org/3/account/%7Baccount_id%7D/favorite/movies?sort_by=created_at.asc&language=en-US&session_id=",
        "favTv" => "https://api.themoviedb.org/3/account/%7Baccount_id%7D/favorite/tv?sort_by=created_at.asc&session_id=",
        "ratedMovies" => "https://api.themoviedb.org/3/account/%7Baccount_id%7D/rated/movies?sort_by=created_at.asc&session_id=",
        "ratedTv" => "https://api.themoviedb.org/3/account/%7Baccount_id%7D/rated/tv?sort_by=created_at.asc&session_id=",
        "movieWatch" => "https://api.themoviedb.org/3/account/%7Baccount_id%7D/watchlist/movies?sort_by=created_at.asc&&session_id=",
        "tvWatch" => "https://api.themoviedb.org/3/account/%7Baccount_id%7D/watchlist/tv?sort_by=created_at.asc&&session_id=",
    );

    $user = array();

    foreach ($api as $key => $value) {
     $details = getDetailsGeneric($value);
     if($details != -1){
        $user[$key] = json_decode($details);
     }
    }

    

    print_r(json_encode($user));

    // print_r(json_decode($details));

    //$lists = json_decode(getLists($session_id));

    //$details->lists = $lists->results;
}
else{
    echo -1;
}

curl_close($curl);

function getDetailsGeneric($apiString){

    global $curl, $session_id;

    curl_setopt_array($curl, array(
        CURLOPT_URL => $apiString. $session_id ."&api_key=18295317f7bea08c23f44598e143b3e3",
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

    if ($err) {
        return -1;
    } else {
        return $response;
    }

}
