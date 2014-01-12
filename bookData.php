<?php

class bookData
{
	public $user;
	public $data;

	function __construct($user, $data)
	{
		$this->user = $user;
		$this->data = $data;
	}
}