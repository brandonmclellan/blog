<?php
	class Comment {
		private $id;
		private $author_id;
		private $author_name;
		private $blog_id;
		private $publish_date;
		private $comment;
		
		protected function __construct($id, $author_id, $author_name, $blog_id, $publish_date, $comment) {
			$this->id = $id;
			$this->author_id = $author_id;
			$this->author_name = $author_name;
			$this->blog_id = $blog_id;
			$this->publish_date = $publish_date;
			$this->comment = $comment;
		}
		
		public function getId() {
			return $this->id;
		}
		
		public function getAuthorId() {
			return $this->author_id;
		}
		
		public function getAuthorName() {
			return $this->author_name;
		}
		
		public function getBlogId() {
			return $this->blog_id;
		}
		
		public function getPublishDate() {
			return date("F j, Y, g:i a", strtotime($this->publish_date));
		}
		
		public function getComment() {
			return $this->comment;
		}
		
		static public function Post($blog_id, $comment, $recaptcha_challenge, $recaptcha_response) {
			global $db;
			
			// Check to ensure user is logged in.
			if (!isset($_SESSION['user_id'])) {
				return false;
			}
			
			// Check for empty comment
			if (strlen($comment) == 0) {
				return false;
			}
			
			// Check to ensure blog id is valid and is open for comments.
			$blog = Blog::retrieve(array('id' => $blog_id), 1);
			if ($blog == null || $blog->isCommentsClosed()) {
				return false;
			}
			
			// Check recaptcha
			$resp = recaptcha_check_answer (PRIVATEKEY,
                                $_SERVER["REMOTE_ADDR"],
                                $recaptcha_challenge,
                                $recaptcha_response);
								
			if (!$resp->is_valid)
				return false;
								
			$sql = 'INSERT INTO comments (author_id, blog_id, publish_date, comment) VALUES(?, ?, NOW(), ?);';
			$sth = $db->prepare($sql);
			$sth->bindValue(1, $_SESSION['user_id'], PDO::PARAM_INT);
			$sth->bindValue(2, $blog_id, PDO::PARAM_INT);
			$sth->bindValue(3, htmlspecialchars($comment));
			
			return $sth->execute();
		}
		
		static public function Retrieve($blog_id) {
			global $db;
			
			$sql = 'SELECT c.id, c.author_id, u.name, c.blog_id, c.publish_date, c.comment FROM comments AS c LEFT JOIN users AS u ON c.author_id = u.id WHERE blog_id = ? ORDER BY publish_date DESC;';
			$sth = $db->prepare($sql);
			$sth->bindValue(1, $blog_id, PDO::PARAM_INT);
			$sth->execute();
			
			$comments = array();
			while($comment = $sth->fetch(PDO::FETCH_ASSOC)) {
				$comments[] = new Comment($comment['id'], $comment['author_id'], $comment['name'], $comment['blog_id'], $comment['publish_date'], $comment['comment']);
			}
			
			return $comments;	
		}
	}
?>