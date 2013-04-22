<?php
	require "common.php";
	
	$user = (isset($_SESSION['user_id']) ? User::Retrieve($_SESSION['user_id']) : false);
	
	// Redirect to homepage if not logged in.
	if (!$user) {
		header('Location: index.php');
		die();
	}
	
	if (isset($_POST['entryTitle'], $_POST['content'], $_POST['closeComments'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field'])) {
		if (!Blog::post($_POST['entryTitle'], $_POST['content'], $_POST['closeComments'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field'])) {
			$captcha_failed = true;
		} else {
			header('Location: index.php');
			die();
		}
	}
	
 ?>
 
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Blog Editor</title>
		
		<link rel="stylesheet" href="css/main.css">
		<script src="js/jquery-1.9.0.min.js"></script>
		<script src="js/jquery.validate.js"></script>
		<script src="js/tinymce/tinymce.min.js"></script>
		<script src="js/editor.js"></script>
		
	</head>
	<body>
		<div id="main-header">
			<a class="title" href="index.php">Blogging Site</a>
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
			<form action="editor.php" method="POST">
				<label for="entryTitle">Entry Title: </label>
				<input type="input" name="entryTitle" id="entryTitle" size="150" value="<?php echo (isset($_POST['entryTitle']) ? htmlspecialchars($_POST['entryTitle']) : ''); ?>" required/><br />
				<label for="entryPost">Entry Contents: </label>
				<textarea name="content"><?php echo (isset($_POST['content']) ? htmlspecialchars ($_POST['content']) : ''); ?></textarea>
				<input type="checkbox" name="closeComments" id="closeComments" value="true" /><label for="closeComments">Disable Commenting</label>
				<h3>Captcha</h3>
				<?php if (isset($captcha_failed)) { print('<div class="error">Invalid captcha entered</div>'); } ?>
				<?=recaptcha_get_html(PUBLICKEY);?>
				<input type="submit" name="post" id="post" class="button" value="Post Blog" />
				<input type="reset" name="reset" id="reset" class="button" value="Reset" />
			</form>
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
 
 