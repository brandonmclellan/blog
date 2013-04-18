<?php

	// Connect to database
	try {
		$db = new PDO("mysql:dbname=blog;host=127.0.0.1", "root", "");
	} catch(PDOException $e) {
		die("Unable to connect to database: " . $e->getMessage());
	}
	
	// Include website classes
	include "includes/blog.php";
?>