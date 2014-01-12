<?php
session_start();

require("config.php");
require("recaptcha.php");
require("database.php");
require("user.php");
require("bookData.php");

global $config;
$config = new config();
global $db;
$db = new database();

global $P;
$P = array();
if (isset($_GET['p'])) {
	$tmp = preg_split("/\//", $_GET['p']);
	foreach ($tmp as $value) 
		$P[] = $value;
}
