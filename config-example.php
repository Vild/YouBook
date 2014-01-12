<?php

define("SITE_NAME", "YouBook");
define("RECAPTCHA_PUBLIC", "");
define("RECAPTCHA_PRIVATE", "");

define("MYSQL_HOST", "");
define("MYSQL_USERNAME", "");
define("MYSQL_PASSWORD", "");
define("MYSQL_DATABASE", "");

define("PASSWORD_SALT", "");

class q { //short for question
	private $id;
	private $text;
	private $type; //Can be: label, text, checkbox, radiobutton, list
	private $data; //Data for radiobutton and lisr

	function __construct($id, $text, $type, $data = null) {
		$this->id = $id;
		$this->text = $text;
		$this->type = $type;
		$this->data = $data;
	}
}

class config
{
	public function GetBookings()
	{
		return array("Book1", "Book2");
	}

	public function GetQuestions($book) {
		if ($book === "Book1")
			return array(
				new q("id1", "Label", "label"),
				new q("id2", "Text", "text"),
				new q("id3", "Checkbox", "checkbox"),
				new q("id4", "Radiobuttons", "radiobutton", array("button1" => "Button 1", "button2" => "Button 2", "button3" => "Button 3", "button4" => "Button 4")),
				new q("id5", "List", "list", array("item1" => "Item 1", "item2" => "Item 2", "item3" => "Item 3")));
		else if ($book == "Book2")
			return array(
				new q("title", "Book a seat", "label"),
				new q("name", "Your fullname?", "text"),
				new q("line", "Which buss line do you want to book?", "list", array("line1" => "Line 1", "line2" => "Line 2", "line45" => "Line 45", "line99" => "Line 99")),
				new q("pilow", "Do you want a pillow?", 'checkbox'),
				new q("floor", "Which floor do you want to sit on?", "radiobutton", array("roof" => "Roof", "ground" => "Ground")));
		else
			return null;
	}
}