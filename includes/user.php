<?php
	class User {
		private $id;
		private $email;
		private $name;
		
		public function __construct($id, $email, $name) {
			$this->id = $id;
			$this->email = $email;
			$this->name = $name;
		}
		
		public function getId() {
			return $this->id;
		}
		
		public function getEmail() {
			return $this->email;
		}
		
		public function getName() {
			return $this->name;
		}
		
		public static function Retrieve($user_id) {
			global $db;
			
			$sth = $db->prepare('SELECT id, email, name FROM users WHERE id = ?');
			$sth->bindValue(1, $user_id);
			$sth->execute();
			
			$info = $sth->fetch(PDO::FETCH_ASSOC);
			if (!$info)
				return false;
			
			return new User($info['id'], $info['email'], $info['name']);
		}
		
		public static function Authenticate($email, $password) {
			global $db;
			
			// Query database to check login information.
			$sth = $db->prepare('SELECT id, email, name FROM users WHERE email LIKE ? AND password = ?');
			$sth->bindValue(1, $email);
			$sth->bindValue(2, md5($password));
			$sth->execute();
			
			$info = $sth->fetch(PDO::FETCH_ASSOC);
			// If no results are returned, login failed.
			if (!$info) {
				return false;
			}
			
			$_SESSION['user_id'] = $info['id'];
			
			return new User($info['id'], $info['email'], $info['name']);
		}
		
		public static function Logout() {
			// Simply unset the user id from session which is used to track login state.
			if (isset($_SESSION['user_id']))
				unset($_SESSION['user_id']);
		}
	};
?>