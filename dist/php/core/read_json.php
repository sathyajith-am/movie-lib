<?php

function getInputJson(){

	$output = exec("python guess.py");
	return $output;
}


if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {

	$file_content = file_get_contents($_FILES['file']['tmp_name']);

	//Sanitise the input against XSS attack.

	$location = 'upload.txt';
	$file = fopen($location,'w');
	fwrite($file, $file_content);
	fclose($file);


	// Read JSON file
	$json = getInputJson();

	// Remove File.
	if (file_exists($location)) {
        unlink($location);
    }
	
	$json_output = fopen("upload.json", "w+") or die("Unable to open file");	
	fwrite($json_output,$json);
	fclose($json_output);

}
else{
	echo -1;
}


?>
