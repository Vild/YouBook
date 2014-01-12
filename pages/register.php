<?php
global $db;

$error = "";
$capError = null;

if (isset($_POST['submit'])) {
	if (!isset($_POST['username']) || $_POST['username'] == "")
		$error .= "Invalid Username<br />\n";
	if (!isset($_POST['password']) || $_POST['password'] == "")
		$error .= "Invalid Password<br />\n";
	if (!isset($_POST['password2']) || $_POST['password2'] == "")
		$error .= "Invalid Password again<br />\n";
	if ($_POST['password'] !== $_POST['password2'])
		$error .= "The Passwords must be the same<br />\n";
	if (!isset($_POST['email']) || $_POST['email'] == "")
		$error .= "Invalid Email<br />\n";
	if (!isset($_POST['firstname']) || $_POST['firstname'] == "")
		$error .= "Invalid Firstname<br />\n";
	if (!isset($_POST['lastname']) || $_POST['lastname'] == "")
		$error .= "Invalid Lastname<br />\n";
	if (!isset($_POST['accept']) || $_POST['accept'] == false) 
		$error .= "You must accept the Terms of service agreement<br />\n";
	if ($error == "") {
		$ret = $db->Register($_POST['username'], $_POST['password'], $_POST['email'], $_POST['firstname'], $_POST['lastname']);

		if (is_numeric($ret)) {
			$_SESSION['id'] = intval($ret);
			header("Location: /");
		} else if (strncmp($ret, "Recaptcha: ", strlen("Recaptcha: ")) === 0)  {
			$capError = substr($ret, strlen("Recaptcha: "));
			$error = "Invalid captcha";
		} else
			$error = $ret;
	}
}

?>

<div id="error"><?=$error?></div>
<form method="POST" action="/register">
	<table id="register">
		<tr><td>Username:</td><td><input type="text" name="username" value="<?=isset($_POST['username'])?$_POST['username']:"";?>" /></td></tr>
		<tr><td>Password:</td><td><input type="password" name="password" /></td></tr>
		<tr><td>Password again:</td><td><input type="password" name="password2" /></td></tr>
		<tr><td>Email:</td><td><input type="email" name="email" value="<?=isset($_POST['email'])?$_POST['email']:"";?>" /></td></tr>
		<tr><td>Firstname:</td><td><input type="text" name="firstname" value="<?=isset($_POST['firstname'])?$_POST['firstname']:"";?>" /></td></tr>
		<tr><td>Lastname:</td><td><input type="text" name="lastname" value="<?=isset($_POST['lastname'])?$_POST['lastname']:"";?>" /></td></tr>
		<tr><td>Captcha:</td><td><?=recaptcha_get_html(RECAPTCHA_PUBLIC);?></td></tr>
		<tr><td>Terms of service:</td><td><textarea id="licensetext" readonly>YOU ACCEPT THAT WE CAN KEEP YOUR INFORMATION THAT YOU SUBMIT TO US.</textarea></td></tr>
		<tr><td>I accept the Terms of service:</td><td><input type="checkbox" name="accept" <?=isset($_POST['accept'])?"checked ":"";?>/></td></tr>
		<tr><td><input type="submit" name="submit" value="Register" /></td></tr>
	</table>
</form>