<?php
	
	class Blog {
		private $id;
		private $title;
		private $author_id;
		private $contents;
		private $publish_date;
		
		// Constructor used for stored blog instances.
		protected function __construct($id, $title, $author_id, $contents, $publish_date) {
			$this->id = $id;
			$this->title = $title;
			$this->author_id = $author_id;
			$this->contents = $contents;
			$this->publish_date = $publish_date;
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
		
		public function getContents() {
			return $this->contents;
		}
		
		public function getPublishDate() {
			return $this->publish_date;
		}
		
		/** Blog::retrieve($where='')
		 *		Queries database for blog posts and returns results in array.
		 */
		public static function retrieve($where='') {
			global $db;
			
			// Pull blogs from database using passed filter.
			$sql = 'SELECT id, author_id, title, publish_date, contents FROM blogs' . ((strlen($where) > 0) ? ' WHERE ' . $where : '');
			$sth = $db->prepare($sql);
			$sth->execute();
			
			
			// Fetch every blog and create class instance.
			$blogs = array();
			while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				$blogs[] = new Blog($row['id'], $row['title'], $row['author_id'], $row['publish_date'], $row['contents']);
			}
			
			return $blogs;
		}
	}
?>