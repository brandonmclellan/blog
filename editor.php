<?php
	require "common.php";
	
	$user = (isset($_SESSION['user_id']) ? User::Retrieve($_SESSION['user_id']) : false);
	
	// Redirect to homepage if not logged in.
	if (!$user) {
		header('Location: index.php');
		die();
	}
	
	
 ?>
 
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Blog Editor</title>
		
		<link rel="stylesheet" href="css/main.css">
		<script src="js/jquery-1.9.0.min.js"></script>
		<script src="js/tinymce/tinymce.min.js"></script>
		
	</head>
	<body>
		<div id="main-header">
			<h1>Blog Title</h1>
			<nav>
				<a href="index.php?logout=true">
					<li id="logout">Logout</li>
				</a>
				<a href="profile.php">
					<li id="profile">Profile</li>
				</a>
				<a href="editor.php">
					<li id="post">Create new Entry</li>
				</a>
			</nav>
		</div>
		<div id="editor">
			<h1>Create New Entry</h1>
			<hr>
			<label for="entryTitle">Entry Title: </label>
			<input type="input" name="entryTitle" id="entryTitle" size="150"/>
			<label for="entryPost">Entry Contents: </label>
			<textarea name="content"></textarea>
		</div>
	</body>
	<script type="text/javascript">
	tinymce.init({
		selector: "textarea",
		plugins: [
			"advlist autolink lists link image charmap print preview anchor",
			"searchreplace visualblocks code fullscreen",
			"insertdatetime media table contextmenu paste"
		],
		toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
	});
	</script>
</html>
 
 