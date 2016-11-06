<?php

	function logged_in()
	{
		return (isset($_SESSION['session_id'])) ? true : false;
	}

	if(logged_in() === true)
	{
		header('Location: localhost/movie-lib/#/main/dashboard');
		exit();
	}
	else
	{
		header('Location: localhost/movie-lib/#/login');
		exit();
	}

?>