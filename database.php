<?php

class database
{
	private $mysql;
	function __construct()
	{
		$this->mysql = null;
	}

	function __destruct() {
		if ($this->mysql !== null)
			$this->mysql->close();
		$this->mysql = null;
	}

	private function connect() {
		if ($this->mysql == null) {
			$this->mysql = new mysqli(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);
			if ($this->mysql->connect_errno) {
				return "Failed to connect to MySQL: " . $this->mysql->connect_error;
			}

			if ($this->mysql->query("CREATE TABLE IF NOT EXISTS `users` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `username` text NOT NULL,
				  `password` text NOT NULL,
				  `email` text NOT NULL,
				  `firstname` text NOT NULL,
				  `lastname` text NOT NULL,
				  `access` int(11) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;") !== TRUE)
			return "Couldn't create table 'users'";

			if ($this->mysql->query("CREATE TABLE IF NOT EXISTS `books` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `user` int(11) NOT NULL,
				  `book` text NOT NULL,
				  `data` text NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;") !== TRUE)
			return "Couldn't create table 'books'";
		}
		return true;
	}

	private function dataFix($data) {
		return mysql_escape_string(addslashes(htmlentities($data, ENT_QUOTES, "UTF-8")));
	}

	public function GetID($username) {
		if (($tmp = $this->connect()) !== true)
			return $tmp;

		$username = $this->dataFix($username);

		$result = $this->mysql->query("SELECT * FROM `users`
			WHERE (
				`username` LIKE '$username'
			)
			LIMIT 0 , 1 ;");

		if ($result->num_rows !== 1) {
			$result->close();
			return -1;
		}

		$row = $result->fetch_assoc();
		$ret = $row['id'];

    	$result->close();

		return $ret;
	}

	public function GetUser($id) {
		if (($tmp = $this->connect()) !== true)
			return $tmp;

		$result = $this->mysql->query("SELECT * FROM `users`
			WHERE (
				`id` LIKE '$id'
			)
			LIMIT 0 , 1 ;");

		if ($result->num_rows !== 1) {
			$result->close();
			return -1;
		}

		$row = $result->fetch_assoc();
		$user = new User($row['id'], $row['username'], $row['password'], $row['email'], $row['firstname'], $row['lastname'], $row['access']);

    	$result->close();

		return $user;
	}

	public function Login($username, $password) {
		if (($tmp = $this->connect()) !== true)
			return $tmp;

		$username = $this->dataFix($username);
		$password = crypt($this->dataFix($password).PASSWORD_SALT, PASSWORD_SALT);

		$result = $this->mysql->query("SELECT * FROM `users`
			WHERE (
				`username` LIKE '$username' AND
				`password` LIKE '$password'
			)
			LIMIT 0 , 1 ;");
		if ($result->num_rows !== 1) {
			$result->close();
			return "Invalid username or password!";
		}

		$row = $result->fetch_assoc();
		$ret = $row['id'];

    $result->close();

		return $ret;
	}

	public function Register($username, $password, $email, $firstname, $lastname) {
		if (($tmp = $this->connect()) !== true)
			return $tmp;

		if ($this->GetID($username) !== -1)
			return "Username taken!";

		$username = $this->dataFix($username);
		$password = crypt($this->dataFix($password).PASSWORD_SALT, PASSWORD_SALT);
		$email = $this->dataFix($email);
		$firstname = $this->dataFix($firstname);
		$lastname = $this->dataFix($lastname);

		if (!isset($_POST["recaptcha_response_field"]))
			return "Recaptcha data not found!";

		$resp = recaptcha_check_answer(RECAPTCHA_PRIVATE, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
		if (!$resp->is_valid)
			return "Recaptcha: ".$resp->error;

		list($_, $mailDomain) = preg_split("/\@/", $email); 

		if (!checkdnsrr($mailDomain, "MX"))
			return "Invalid email server";

		if ($this->mysql->query("INSERT INTO `users` ( 
				`id` ,
				`username` ,
				`password` ,
				`email` ,
				`firstname` ,
				`lastname` ,
				`access`
			)
			VALUES (
				NULL , '$username', '$password', '$email', '$firstname', '$lastname', '0'
			);") === true)
			return $this->GetID($username);
		else
			return "Failed to query database";
	}

	public function GetBooking($user, $book)
	{
		if (($tmp = $this->connect()) !== true)
			return $tmp;

		$result = $this->mysql->query("SELECT * FROM `books`
			WHERE (
				`user` LIKE '$user' AND
				`book` Like '$book'
			)
			LIMIT 0 , 1 ;");

		if ($result->num_rows !== 1) {
			$result->close();
			return null;
		} else {
			$row = $result->fetch_assoc();
			$ret = $row['data'];
			$result->close();
			return $ret;
		}
	}
	public function AddBooking($user, $book, $data)
	{
		if (($tmp = $this->connect()) !== true)
			return $tmp;

		$book = $this->dataFix($book);

		if ($this->GetBooking($user, $book) !== null)
			return $this->ChangeBooking($user, $book, $data);

		if ($this->mysql->query("INSERT INTO `books` ( 
				`id` ,
				`user` ,
				`book` ,
				`data`
			)
			VALUES (
				NULL , '$user', '$book', '$data'
			);") === true)
			return "Your booking was added";
		else
			return "Failed to query database";
	}

	public function RemoveBooking($user, $book)
	{
		if (($tmp = $this->connect()) !== true)
			return $tmp;

		$book = $this->dataFix($book);

		if ($this->GetBooking($user, $book) == null)
			return "Your booking couldn't be found";

		if ($this->mysql->query("DELETE FROM `books`
			WHERE (
				`books`.`user` LIKE '$user' AND
				`books`.`book` LIKE '$book'
			) ;") === true)
			return "Your booking was removed";
		else
			return "Failed to query database";
	}

	private function ChangeBooking($user, $book, $data)
	{
		if (($tmp = $this->connect()) !== true)
			return $tmp;

		if ($this->mysql->query("UPDATE `books`
			SET `user` = '$user', `book` = '$book', `data` = '$data'
			WHERE (
				`books`.`user` = '$user' AND
				`books`.`book` = '$book'
			) ;") === true)
			return "Your booking was changed";
		else
			return "Failed to query database";
	}

	public function GetAllBookings()
	{
		if (($tmp = $this->connect()) !== true)
			return $tmp;

		$result = $this->mysql->query("SELECT * FROM `books`;");

		$ret = array();
		
		for ($i = 0; $i < $result->num_rows; $i++) { 
			$row = $result->fetch_assoc();
			$ret[$row['book']][] = new bookData($row['user'], $row['data']);
		}

		$result->close();
		return $ret;
	}
}


