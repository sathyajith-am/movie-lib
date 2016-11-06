<?php

if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {

// uploads image in the folder images
    $temp = explode(".", $_FILES["file"]["name"]);

// give callback to your angular code with the image src name
    $file_contents =  file_get_contents($_FILES['file']['tmp_name']); 

    echo 0;
}
else{
	echo -1;
}

?>
