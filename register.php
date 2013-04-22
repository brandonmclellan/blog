<?php
	/** 
	 *	Filename: register.php
	 *	Author Name:	Brandon McLellan
	 *	Website Name:	Blogging Site
	 *	Description:
	 *		- Handles passing registration data to user.php
	 *		- HTML page for registering new users.
	 */
	require "common.php";
	
	// Check if user is logged in already, if so redirect.
	if (isset($_SESSION['user_id'])) {
		header('Location: index.php');
		die();
	}
	
	// Check if user has submitted new registration.
	if (isset($_POST['emailAddress'], $_POST['register_password'], $_POST['confirm_password'], $_POST['username'])) {
		$errors = User::Register($_POST['emailAddress'], $_POST['register_password'], $_POST['username'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);
		if (count($errors) == 0) {
			header('Location: index.php');
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Blog</title>
		
		<link rel="stylesheet" href="css/main.css">
		<script src="js/jquery-1.9.0.min.js"></script>
		<script src="js/jquery.validate.js"></script>
		<script src="js/register.js"></script>
	</head>
	<body>
		<div id="main-header">
			<a class="title" href="index.php">Blogging Site</a>
			
			<nav>
				<li id="login">
					<div class="header">Login</div>
					<div id="login-form">
						<form action="index.php" method="POST">
							<?php if (isset($login_fail)): ?>
								<script>
									$(document).ready(function() {
										$("#login-form").show();
									});
								</script>
								<p id="errorMessage">Your email/password combination is incorrect.</p>
							<?php endif; ?>
								<label for="email">Email Address: </label><input type="text" name="email" id="email" /><div class="clear"></div>
								<label for="password">Password: </label><input type="password" name="password" id="password" /><div class="clear"></div>
								<input type="submit" id="login-button" class="button" value="Login" />
						</form>
					</div>
				</li>
			</nav>
		</div>
		<div id="register">
			<h1>Register Account</h1>
			<?php if (isset($errors) && count($errors) > 0): ?>
				<div class="errorList">
			<?php foreach($errors as $error):?>
					<p><?=$error?></p>
			<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<form action="register.php" method="POST" id="registerForm">
				<h2>Account Information</h2>
				<div class="field">
					<label for="emailAddress">Email Address: </label>
					<input type="input" name="emailAddress" id="emailAddress" size="50" required />
				</div>
				<div class="field">
					<label for="password">Password: </label>
					<input type="password" name="register_password" id="register_password" size="50"  required />
				</div>
				<div class="field">
					<label for="confirm_password">Confirm Password: </label>
					<input type="password" name="confirm_password" id="confirm_password" size="50"  required />
				</div>
				<div class="field">
					<label for="username">Username: </label>
					<input type="input" name="username" id="username" size="50" required />
				</div>
				<h2>Captcha</h2>
				<?=recaptcha_get_html(PUBLICKEY);?>
				<input type="submit" name="submit" id="submit" value="Register Account" class="button" />
			</form>
		</div>
	</body>
</html>