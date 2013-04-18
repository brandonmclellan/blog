<?php
	require 'common.php';
	
	$blogs = Blog::Retrieve();
	
	foreach($blogs as $blog) {
		print($blog->getTitle() . "<br />");
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Blog</title>
		
		<link rel="stylesheet" href="css/main.css">
	</head>
	<body>
		<div id="main-header">
			<h1>Blog Title</h1>
			
			<nav>
				<a href="login.php"><li>Login</li></a>
			</nav>
		</div>
		<article>
			<section>
				<h2>Blog Entry Title</h2>
				<div class="content">Blahblahblah</div>
				
				<p class="posted">Posted: <p class="date">April 18th, 2013 8:43pm</p>
				
			</section>
		</article>
	</body>
</html>