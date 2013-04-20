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
	
	// Retrieve logged-in users information
	$user = (isset($_SESSION['user_id']) ? User::Retrieve($_SESSION['user_id']) : false);
	
	// Check if request is for specific blog entry.
	if (isset($_GET['id'])) {
		$blog = Blog::Retrieve(array('id' => $_GET['id']), 1);
	// Show latest entry from user if logged in.
	} else if (isset($_SESSION['user_id'])) {
		$blog = Blog::Retrieve(array('author_id' => $_SESSION['user_id']), 1);
	} else {
	// Show latest global entry if not logged in.
		$blog = Blog::Retrieve();
	}
	
	$comments = $blog->getComments();
	
	$recent_entries = Blog::Retrieve(array(), 20);
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
			<section>
				<!-- Blog Post -->
				<div id="entry">
					<h2><?=$blog->getTitle();?></h2>
					<div class="details">By <span class="highlight"><?=$blog->getAuthorName();?></span> posted <span class="highlight"><?=$blog->getPublishDate();?></span></div>
					<hr />
					<div class="content"><?=$blog->getContents();?></div>
					<hr>
				</div>
				<!-- Comment Section -->
				<div id="comments">
					<?php if (!$blog->isCommentsClosed() && $user): ?>
						<!-- Posting Comment Section -->
						<h3>Post Comment</h3>
						<?php if (isset($captcha_fail)): ?>
							<p class="errorMessage">You have incorrectly entered captcha.</p>
						<?php endif; ?>
						<form action="index.php" method="POST">
							<input type="hidden" name="blog_id" value="<?=$blog->getId();?>" />
							<textarea name="comment" id="comment" rows="4" cols="50"></textarea>
							<?=recaptcha_get_html(PUBLICKEY);?>
							<input type="submit" name="submit_comment" id="submit_comment" />
						</form>
					<?php else: ?>
						<h4>Posting comments has been closed or you must login.</h4>
					<?php endif; ?>
					
					<!-- Latest Comments -->
					<h3>Latest Comments</h3>
					<?php foreach($comments as $comment): ?>
						<div class="comment">
							<?=$comment->getComment();?>
							<div class="details">By <span class="highlight"><?=$comment->getAuthorName();?></span> at <span class="highlight"><?=$comment->getPublishDate();?></span></div>
						</div>
					<?php endforeach; ?>
					<?php if (count($comments) == 0): ?>
						<h4>No comments posted</h4>
					<?php endif; ?>
				</div>
			</section>
		</article>
		<aside>
			<h2>Recent Entries</h2>
			<hr>
			<?php if (count($recent_entries) > 0):
					foreach($recent_entries as $entries): ?>
				<div class="blogEntry">
					<a href="index.php?id=<?=$entries->getId();?>"><?=$entries->getTitle(); ?></a><br />
					by <a href="profile.php?id=<?=$entries->getAuthorId();?>"><?=$entries->getAuthorName();?></a> at 8:21pm
				</div>
			<?php 	endforeach;
				   else: ?>
				<p>There are no recent entries</p>
			<?php endif; ?>
		</aside>
		<script src="js/login-box.js"></script>
	</body>
</html>