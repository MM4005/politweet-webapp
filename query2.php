<?php

date_default_timezone_set('UTC');

// Database configuration
$db_username = 'root';
$db_password = 'root';
$db_host     = 'localhost';
$db_name     = 'politics';

// Connect to database
try {
	$db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
	var_dump($e->getMessage());
}

$statement = $db->query("SELECT * FROM party ORDER BY `party_id` DESC LIMIT 10000");
$statement->execute();
$response = $statement->fetchAll(PDO::FETCH_ASSOC);

$states = ['AL' => 0,'AK' => 0,'AZ' => 0,'AR' => 0,'CA' => 0,'CO' => 0,'CT' => 0,'DE' => 0,'FL' => 0,'GA' => 0,'HI' => 0,'ID' => 0,'IL' => 0,'IN' => 0,'IA' => 0,'KS' => 0,'KY' => 0,'LA' => 0,'ME' => 0,'MD' => 0,'MA' => 0,'MI' => 0,'MN' => 0,'MS' => 0,'MO' => 0,'MT' => 0,'NE' => 0,'NV' => 0,'NH' => 0,'NJ' => 0,'NM' => 0,'NY' => 0,'NC' => 0,'ND' => 0,'OH' => 0,'OK' => 0,'OR' => 0,'PA' => 0,'RI' => 0,'SC' => 0,'SD' => 0,'TN' => 0,'TX' => 0,'UT' => 0,'VT' => 0,'VA' => 0,'WA' => 0,'WV' => 0,'WI' => 0,'WY' => 0];

$states_array[] = $states;

foreach($response as $timepoint_array) {
	unset($timepoint_array['party_id']);
	unset($timepoint_array['added']);
	foreach($timepoint_array as $key => $value) {
		if(is_null($value)) $value = 0;
		$states[$key] = $states[$key] + $value;
	}
	$states_array[] = $states;
}

echo json_encode($states_array);