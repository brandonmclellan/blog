<?php
	require 'common.php';
	
	//Check if there is a login request and we aren't already logged in.
	if (isset($_POST['email'], $_POST['password']) && !isset($_SESSION['user_id'])) {
		// Pass email and password to check for authentication, escaping is done inside function.
		if (!User::Authenticate($_POST['email'], $_POST['password'])) {
			$login_fail = true;
		}
	}
	
	// Check for logout.
	if (isset($_GET['logout'])) {
		User::Logout();
	}
	
	
	$user = (isset($_SESSION['user_id']) ? User::Retrieve($_SESSION['user_id']) : false);
	$blogs = Blog::Retrieve();
	
	
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Blog</title>
		
		<link rel="stylesheet" href="css/main.css">
		<script src="scripts/jquery-1.9.0.min.js"></script>
		
	</head>
	<body>
		<div id="main-header">
			<h1>Blog Title</h1>
			
			<nav>
				<?php if ($user): ?>
					<a href="index.php?logout=true">
						<li id="logout">Logout</li>
					</a>
					<a href="profile.php">
						<li id="profile">Profile</li>
					</a>
					<a href="post.php">
						<li id="post">Create new Entry</li>
					</a>
				<?php else: ?>
				<li id="login">
					<h3>Login</h3>
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
								<input type="submit" id="login-button" value="Login" />
						</form>
					</div>
				</li>
				<?php endif; ?>
			</nav>
		</div>
		<article>
			<?php foreach($blogs as $blog): ?>
			<section>
				<h2><?=$blog->getTitle();?></h2>
				<div class="details">By <span class="highlight">Author</span> posted <span class="highlight"><?=$blog->getPublishDate();?></span></div>
				<hr />
				<div class="content"><?=$blog->getContents();?></div>
				
				
			</section>
			<?php endforeach; ?>
		</article>
		<script src="scripts/login-box.js"></script>
	</body>
</html>