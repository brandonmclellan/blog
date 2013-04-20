<?php
	
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
		
		/** Blog::retrieve($where='')
		 *		Queries database for blog posts and returns results in array.
		 */
		public static function retrieve($where=array(), $limit=1) {
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