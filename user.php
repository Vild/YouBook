<?php

class user {
	public $id;
	public $username;
	public $password;
	public $email;
	public $firstname;
	public $lastname;
	public $access;

	function __construct($id, $username, $password, $email, $firstname, $lastname, $access) {
		$this->id = $id;
		$this->username = $username;
		$this->password = $password;
		$this->email = $email;
		$this->firstname = $firstname;
		$this->lastname = $lastname;
		$this->access = $access;
	}

}