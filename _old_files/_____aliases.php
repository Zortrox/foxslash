<?php

header('Content-Type: application/json;charset=utf-8');
header("Access-Control-Allow-Origin: *");

function getAliases($showAll, $gameAliases = array()) {
	//connect
	$db = new mysqli('mysql.foxslash.com', '', '', "steam_checker");
	if ($db ->connect_errno) { 
		die('Could not connect: ' . mysqli_error());
	}
	mysqli_set_charset($db, "utf8");

	$values = "";
	$loops = 0;

	if (!$showAll) {
		$values = " WHERE ";
		foreach ($gameAliases as $value) {
			if ($loops != 0) $values .= " OR ";
			$values .= "alias=\"" . mysqli_real_escape_string($db, $value) . "\"";
			$loops++;
		}
	}
	
	//get the steam ids
	$aliases = array();
	$question = "";
	$aliasQuery = $db->query("SELECT * FROM alias" . $values);
	if ($aliasQuery) {
		$numRows = $aliasQuery->num_rows;
		if ($numRows > 0) {
			while ($row = $aliasQuery->fetch_assoc()) {
				if (!array_key_exists($row["id"], $aliases)) {
					$aliases[$row["id"]] = array();
				}
				array_push($aliases[$row["id"]], $row["name"]);
			}
		}
	}

	$db->close();

	return $aliases;
}

if ( $_SERVER['REQUEST_METHOD'] === 'GET' ) {
	$gameAliases = isset($_GET["a"]) ? $_GET["a"] : 0;
	$showAll = isset($_GET["all"]) ? true : false;

	//get steam game IDs of all aliases
	if ($gameAliases || $showAll) {
		$aliases = getAliases($showAll, $gameAliases);

		echo json_encode($aliases);

	} else {
		//POST poll id wrong
		echo "{}";
	}
} else if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	$json = file_get_contents('php://input');
	$data = json_decode( $json );

	//var_dump($data->names);

	if ( $data ) {
		$aliases = array();
		if ( $data->type == "all" ) {
			$aliases = getAliases(true);
		} else {
			$aliases = getAliases(false, $data->names);
		}

		echo json_encode($aliases);
	} else {
		echo "{}";
	}
} else {
	echo "{}";
}

?>