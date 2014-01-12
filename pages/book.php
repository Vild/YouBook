<?php

global $P;
global $db;
global $config;

$msg = "";

if (!isset($_SESSION['id']))
	header("Location: /login");

if (!isset($P[1])) {
	echo "Need a booking id.";
}

if (isset($P[2]) && $P[2] === "remove")
	$msg = $db->RemoveBooking($_SESSION['id'], $P[1]);

$booking = $config->GetQuestions($P[1]);

if ($booking !== null) {
	if (isset($_POST['submit'])) {
		foreach ($booking as $q)
			if ($q->type !== "label" && $q->type !== "checkbox")
				if (!isset($_POST[$q->id]) || $_POST[$q->id] == "")
					$msg.= "Invalid ".$q->id."<br />\n";
		
		if ($msg == "") {
			$ret = null;
			$data = array();
			foreach ($booking as $q)
				if ($q->type === "checkbox")
					$data[$q->id] = isset($_POST[$q->id]);
				else if ($q->type !== "label")	
					$data[$q->id] = $_POST[$q->id];

			$datastr = json_encode($data);
			$msg = $db->AddBooking($_SESSION['id'], $P[1], $datastr);
		}
	}

	$data = $db->GetBooking($_SESSION['id'], $P[1]);
	if ($data !== null)
		$data = json_decode($data);

	function d($name)
	{
		global $data;
		if ($data !== null)
			return $data->$name;
		else
			return "";
	}

	echo "<div id='error'>$msg</div>";
	echo "<form action='/book/".$P[1]."' method='POST'><table id='book'>";
	foreach ($booking as $q) {
		echo "<tr>";
		if ($q->type == "label")
			echo "<td>".$q->text."</td>";
		else if ($q->type == "text")
			echo "<td>".$q->text."</td><td><input type='text' name='".$q->id."' id='".$q->id."' value='".d($q->id)."' /></td>";
		else if ($q->type == "checkbox")
			echo "<td>".$q->text."</td><td><input type='checkbox' name='".$q->id."' id='".$q->id."' ".(d($q->id) ? "checked ": "")."/></td>";
		else if ($q->type == "radiobutton") {
			echo "<td>".$q->text."</td><td>";
			foreach ($q->data as $key => $value)
				echo "\n<input type='radio' name='".$q->id."' value='$key' ".((d($q->id) === $key) ? "checked ": "")."/><label for='$key'>$value</label>\n";
			echo "</td>";
		} else if ($q->type == "list") {
			 echo "<td>".$q->text."</td><td><select id='".$q->id."' name='".$q->id."'>\n";
			foreach ($q->data as $key => $value)
				echo "<option value='$key' ".((d($q->id) === $key) ? "selected ": "").">$value</option>\n";
			echo "</select></td>";
		}
		echo "</tr>\n";
	}

	echo "<tr><td><input type='submit' name='submit' value='Register' /></td><td>".(($data !== null)?"<input type=\"button\" onclick=\"javascript: window.location = '/book/".$P[1]."/remove';\" value=\"Remove booking\" />" : "")."</td></tr>";
	echo "</table></form>";
}