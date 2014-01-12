<?php
ob_start();
require("global.php");
global $P;
global $db;

$page = "pages/main.php";

if (isset($P[0]))
	$page = "pages/".$P[0].".php";
?>

<!DOCTYPE HTML>
<html>
<head>
	<title><?=SITE_NAME;?></title>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="/css/main.css">
</head>
<body>
	<div id="wrapper">
		<header><?=SITE_NAME;?></header>
		<nav>
			<span><a href="/">Home</a></span>
			<?php
			$tmp = $config->GetBookings();
			foreach ($tmp as $value) {
				if (!isset($_SESSION['id']))
					echo "<span><a href=\"/book/$value\" class='notloggedina'>$value</a></span>\n";
				else
					echo "<span><a href=\"/book/$value\">$value</a></span>\n";
			}
			?>
			<?php
			if (isset($_SESSION['id'])) {
				$user = $db->GetUser($_SESSION['id']);
			?>
			<span><a href="/logout">Logout</a></span>
			<?php
		  	} else {
			?>
			<span><a href="/login">Login</a></span>
			<span><a href="/register">Register</a></span>
			<?php
			}
			?>
		</nav>
		<div id="content">
			<?php
			if (file_exists($page))
				require($page);
			else
				require("pages/404.php");
			?>
		</div>
		<footer>
			Copyright &copy; 2014 Dan "WildN00b" Printzell.<br />
			License: <a href="https://www.apache.org/licenses/LICENSE-2.0">Apache License, Version 2.0</a>
		</footer>
	</div>
</body>
</html>
<?php
ob_end_flush();