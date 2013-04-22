<?php
	/**
	 *	Filename: user.php
	 *	Author Name:	Brandon McLellan
	 *	Website Name:	Blogging Site
	 *	Description:
	 *		Handles all aspects of authentication, registration and retrival for users.
	 */
	class User {
		private $id;
		private $email;
		private $name;
		
		// Protected constructor called by Retrieve
		protected function __construct($id, $email, $name) {
			$this->id = $id;
			$this->email = $email;
			$this->name = $name;
		}
		
		// Public Getters
		public function getId() {
			return $this->id;
		}
		
		public function getEmail() {
			return $this->email;
		}
		
		public function getName() {
			return $this->name;
		}
		
		/** User::Retrieve($user_id)
		 *		Queries database for given user id and returns user class.
		 */
		public static function Retrieve($user_id) {
			global $db;
			
			// Query for user
			$sth = $db->prepare('SELECT id, email, name FROM users WHERE id = ?');
			$sth->bindValue(1, $user_id);
			$sth->execute();
			
			$info = $sth->fetch(PDO::FETCH_ASSOC);
			if (!$info)
				return false;
			
			// Create new User instance and return it.
			return new User($info['id'], $info['email'], $info['name']);
		}
		
		/**	User::Register($email, $password, $username, $recaptcha_challenge, $recaptcha_response)
		 *		Verifies all information from user and creates new user account.
		 */
		public static function Register($email, $password, $username, $recaptcha_challenge, $recaptcha_response) {
			global $db;
			
			$errors = array();
			
			if (strlen($username) == 0) {
				$errors[] = 'You must enter a username.';
			}
			
			// Check password length requirements
			if (strlen($password) < 2) {
				$errors[] = 'You must enter a password at least 2 characters long.';
			}
			
			// Check email validity.
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$errors[] = 'Entered email is invalid.';
			}
			
			// Query for email to see if it already exists
			$eth = $db->prepare('SELECT email FROM users WHERE email LIKE ?');
			$eth->bindValue(1, $email);
			$eth->execute();
			
			// Check if the email exists.
			if ($eth->fetch()) {
				$errors[] = 'Email address already exists in system.';
			}
			
			// Check recaptcha
			$resp = recaptcha_check_answer (PRIVATEKEY,
                                $_SERVER["REMOTE_ADDR"],
                                $recaptcha_challenge,
                                $recaptcha_response);
								
			if (!$resp->is_valid)
				$errors[] = 'Captcha is invalid.';
				
			if (count($errors) > 0)
				return $errors;
				
			$sth = $db->prepare('INSERT INTO users(email, password, name) VALUES (?, ?, ?);');
			$sth->bindValue(1, $email);
			$sth->bindValue(2, md5($password));
			$sth->bindValue(3, $username);
			$sth->execute();
			return $errors;
		}
		
		/** User::Authenticate($email, $password)
		 *		Checks user's credinatals against database and authenticates if successful.
		 */
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
		
		/** User::Logout()
		 *		Destroys users session and logs them out.
		 */
		public static function Logout() {
			// Simply unset the user id from session which is used to track login state.
			if (isset($_SESSION['user_id']))
				unset($_SESSION['user_id']);
		}
	};
?>