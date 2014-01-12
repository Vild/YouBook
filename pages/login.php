<?php
global $db;

$error = "";

if (isset($_POST['submit'])) {
	if (!isset($_POST['username']) || $_POST['username'] == "")
		$error .= "Invalid Username<br />\n";
	if (!isset($_POST['password']) || $_POST['password'] == "")
		$error .= "Invalid Password<br />\n";
	if ($error == "") {
		$ret = $db->Login($_POST['username'], $_POST['password']);

		if (is_numeric($ret)) {
			$_SESSION['id'] = intval($ret);
			header("Location: /");
		} else
			$error = $ret;
	}
}

?>

<div>
	<form method="POST" action="/login">
		<table id="loginstandalone">
			<tr><td>Username:</td><td><input type="text" name="username" value="<?=isset($_POST['username'])?$_POST['username']:"";?>" /></td></tr>
			<tr><td>Password:</td><td><input type="password" name="password" /></td></tr>
			<tr><td><input type="submit" name="submit" value="Login" /></td><td><input type="button" name="register" value="Register" onclick="window.location='/register';" /></td></tr>
		</table>
	</form>
</div>
<div id="error"><?=$error?></div>