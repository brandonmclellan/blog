<?php

	//Non-commited configuration file with database and recaptcha-settings
	require "config.php";
	
	// Connect to database
	try {
		$db = new PDO(DSN, USER, PASSWORD);
	} catch(PDOException $e) {
		die("Unable to connect to database: " . $e->getMessage());
	}
	
	date_default_timezone_set('America/New_York');
	
	// reCAPTCHA library
	require "includes/recaptchalib.php";
	
	
	// Include website classes
	require "includes/blog.php";
	require "includes/user.php";
	require "includes/comment.php";
	
	// Start session tracking
	session_start();
?>