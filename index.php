<?php
	require 'common.php';
	
	//Check if there is a login request and we aren't already logged in.
	if (isset($_POST['email'], $_POST['password']) && !isset($_SESSION['user_id'])) {
		// Pass email and password to check for authentication, escaping is done inside function.
		if (!User::Authenticate($_POST['email'], $_POST['password'])) {
			$login_fail = true;
		}
	}
	
	// Check for comment submission
	if (isset($_POST['comment'], $_POST['blog_id'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field'])) {
		if (!Comment::Post($_POST['blog_id'], $_POST['comment'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field'])) {
			$captcha_fail = true;
		}
	}
	
	// Check for logout.
	if (isset($_GET['logout'])) {
		User::Logout();
	}
	
	
	$user = (isset($_SESSION['user_id']) ? User::Retrieve($_SESSION['user_id']) : false);
	$blogs = Blog::Retrieve(($user ? 'WHERE author_id = ' . $_SESSION['user_id'] : '') . ' ORDER BY publish_date DESC LIMIT 1');
	
	$recent_entries = Blog::Retrieve('ORDER BY publish_date DESC LIMIT 20');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Blog</title>
		
		<link rel="stylesheet" href="css/main.css">
		<script src="js/jquery-1.9.0.min.js"></script>
		
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
					<a href="editor.php">
						<li id="post">Create new Entry</li>
					</a>
				<?php else: ?>
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
								<input type="submit" id="login-button" value="Login" />
						</form>
					</div>
				</li>
				<?php endif; ?>
			</nav>
		</div>
		<article>
			<?php if (count($blogs) == 1): ?>
			<section>
				<div id="entry">
					<h2><?=$blogs[0]->getTitle();?></h2>
					<div class="details">By <span class="highlight"><?=$blogs[0]->getAuthorName();?></span> posted <span class="highlight"><?=$blogs[0]->getPublishDate();?></span></div>
					<hr />
					<div class="content"><?=$blogs[0]->getContents();?></div>
					<hr>
				</div>
				<div id="comments">
					<h3>Comments</h3>
					<?php if (!$blogs[0]->isCommentsClosed()): ?>
						<h5>Post Comment</h5>
						<?php if (isset($captcha_fail)): ?>
							<p class="errorMessage">You have incorrectly entered captcha.</p>
						<?php endif; ?>
						<form action="index.php" method="POST">
							<input type="hidden" name="blog_id" value="<?=$blogs[0]->getId();?>" />
							<textarea name="comment" id="comment" rows="4" cols="50"></textarea>
							<?=recaptcha_get_html(PUBLICKEY);?>
							<input type="submit" name="submit_comment" id="submit_comment" />
						</form>
					<?php else: ?>
						<h5>Posting comments has been closed</h5>
					<?php endif; ?>
					
					<h5>Latest Comments</h5>
					<?php foreach($blogs[0]->getComments() as $comment): ?>
						<div class="comment">
							<?=$comment->getComment();?>
							<div class="details">By <span class="highlight"><?=$comment->getAuthorName();?></span> at <span class="highlight"><?=$comment->getPublishDate();?></span></div>
						</div>
					<?php endforeach; ?>
				</div>
			</section>
			<?php else: ?>
			<h2>
			<?php endif; ?>
		</article>
		<aside>
			<h2>Recent Entries</h2>
			<hr>
			
			<p class="date">Friday, April 19th</p>
			<div class="blogEntry">
				<a href="index.php?id=4">Test Blog Entry</a><br />
				by <a href="profile.php?id=1">Brandon McLellan</a> at 8:21pm
			</div>
			<div class="blogEntry">
				<a href="index.php?id=4">Test Blog Entry</a><br />
				by <a href="profile.php?id=1">Brandon McLellan</a> at 8:21pm
			</div>
			<div class="blogEntry">
				<a href="index.php?id=4">Test Blog Entry</a><br />
				by <a href="profile.php?id=1">Brandon McLellan</a> at 8:21pm
			</div>
		</aside>
		<script src="js/login-box.js"></script>
	</body>
</html>