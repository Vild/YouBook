<?php
global $P;
global $db;

if (!isset($_SESSION['id']) || $db->GetUser($_SESSION['id'])->access < 1) {
	require("pages/404.php");
} else {
	if (!isset($P[1])) {
		echo "Need a booking id.";
	}

	$questions = $config->GetQuestions($P[1]);

	if ($questions !== null) {
		echo "<table id='stats'><thead><tr>";

		echo "<td>Firstname</td><td>Lastname</td>";
		foreach ($questions as $q) {
			if ($q->type === "label")
				continue;
			echo "<td>".$q->id."</td>";
		}
		echo "\n</tr></thead><tbody>";

		$bookings = $db->GetAllBookings();
		$bookings = $bookings[$P[1]];

		foreach ($bookings as $book) {
			$dat = json_decode($book->data);
			$user = $db->GetUser($book->user);
			echo "<tr><td>".$user->firstname."</td><td>".$user->lastname."</td>";
			foreach ($questions as $q) {
				if ($q->type === "label")
					continue;
				$a = $q->id;
				if ($q->type == "checkbox")
					echo "<td>".($dat->$a ? "yes" : "no")."</td>";
				else
					echo "<td>".$dat->$a."</td>";
			}
			echo "</tr>\n";
		}

		echo "</tbody></table>";
	}
}