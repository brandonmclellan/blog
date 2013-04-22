<?php
	/**
	 *	Filename: blog.php
	 *	Author Name:	Brandon McLellan
	 *	Website Name:	Blogging Site
	 *	Description:
	 *		Handles all aspects of retrieving and posting blogs within the site.
	 */
	class Blog {
		private $id;
		private $title;
		private $author_id;
		private $author_name;
		private $contents;
		private $publish_date;
		private $closed;
		
		// Constructor used for stored blog instances.
		protected function __construct($id, $title, $author_id, $author_name, $contents, $publish_date, $closed) {
			$this->id = $id;
			$this->title = $title;
			$this->author_id = $author_id;
			$this->author_name = $author_name;
			$this->contents = $contents;
			$this->publish_date = $publish_date;
			$this->closed = $closed;
		}

		/**
		 *	Public Getters
		 */
		public function getId() {
			return $this->id;
		}
		
		public function getTitle() {
			return $this->title;
		}
		
		public function getAuthorId() {
			return $this->author_id;
		}
		
		public function getAuthorName() {
			return $this->author_name;
		}
		
		public function getContents() {
			return $this->contents;
		}
		
		public function getPublishDate() {
			return date("F j, Y, g:i a", strtotime($this->publish_date));
		}
		
		public function getDateTime() {
			return new DateTime($this->publish_date);
		}
		
		public function isCommentsClosed() {
			return $this->closed;
		}
		
		public function getComments() {
			return Comment::Retrieve($this->id);		
		}
		
		/** Blog::CloseComments($blog_id)
		 *		Closes comments for given blog.
		 */
		public static function CloseComments($blog_id) {
			global $db;
			
			$blog = Blog::Retrieve(array('id' => $blog_id), 1);
			
			// Verify blog exists and user is logged in.
			if (!$blog || !isset($_SESSION['user_id']))
				return false;
				
			// Verify person logged in is closing comments.
			if ($blog->getAuthorId() != $_SESSION['user_id'])
				return false;
				
			// Update database to reflect the comments being closed.
			$sth = $db->prepare('UPDATE blogs SET closed = 1 WHERE id = ?;');
			$sth->bindValue(1, $blog_id);
			return $sth->execute();
		}
		
		/** Blog::post($title, $contents, $closed, $recaptcha_challenge, $recaptcha_response)
		 *		Verifies the content from the web and inserts into the database.
		 */
		public static function Post($title, $contents, $closed, $recaptcha_challenge, $recaptcha_response) {
			global $db;
			
			// Check to ensure user is logged in.
			if (!isset($_SESSION['user_id'])) {
				return false;
			}
			
			// Make sure the title and contents aren't empty.
			if (!strlen($title) || !strlen($contents)) {
				return false;
			}
			
			// Check CAPTCHA response validity.
			$resp = recaptcha_check_answer (PRIVATEKEY,
                                $_SERVER["REMOTE_ADDR"],
                                $recaptcha_challenge,
                                $recaptcha_response);
								
			if (!$resp->is_valid) {
				return false;
			}
				
			$sql = "INSERT INTO blogs(author_id, title, publish_date, contents, closed) VALUES(?, ?, NOW(), ?, ?);";
			$sth = $db->prepare($sql);
			$sth->bindValue(1, $_SESSION['user_id'], PDO::PARAM_INT);
			$sth->bindValue(2, htmlspecialchars($title), PDO::PARAM_STR);
			$sth->bindValue(3, htmlspecialchars($contents), PDO::PARAM_STR);
			$sth->bindValue(4, $closed, PDO::PARAM_BOOL);
			
			$sth->execute();
			return $sth->execute();
		}
		
		/** Blog::retrieve($where=array(), $limit=1)
		 *		Queries database for blog posts and returns results in array.
		 */
		public static function Retrieve($where=array(), $limit=1) {
			global $db;
			
			// Pull blog information including author name from database.
			$sql = 'SELECT b.id, b.author_id, u.name, b.title, b.publish_date, b.contents, b.closed FROM blogs AS b LEFT JOIN users AS u ON b.author_id = u.id ';
			
			// Generates a WHERE clause based on given key=>value filters passed.
			$addedWhere = false;
			foreach($where as $key => $value) {
				if (!$addedWhere) {
					$sql .= ' WHERE ';
					$addedWhere = true;
				} else {
					$sql .= ' AND ';
				}
				$sql .= 'b.' . $key . ' = :' . $key . ' ';
			}
			
			// Always sort by latest post, limit is always added.
			$sql .= 'ORDER BY publish_date DESC LIMIT ' . (int)$limit;
		
			// Pass entire SQL string to be prepared
			$sth = $db->prepare($sql);
			
			// Bind values from where clause to ensure proper escaping.
			foreach($where as $key => $value) {
				$sth->bindValue(':' . $key, $value);
			}
			
			// Bind limit to query and execute
			$sth->execute();
			
			// Fetch every blog and create class instance.
			$blogs = array();
			while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				$blogs[] = new Blog($row['id'], $row['title'], $row['author_id'], $row['name'], $row['contents'], $row['publish_date'], $row['closed']);
			}
			
			// If there is a potential for multiple blogs, return array.
			if ($limit > 1) {
				return $blogs;
			}
				
			// If there is only ever going to be one blog, pass blog itself rather then array.
			return( count($blogs) > 0) ? $blogs[0] : false;
		}
	}
?>