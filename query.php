<?php

	define("DB_HOST",'localhost',true);
	define("DB_USER",'root',true);
	define("DB_PW",'root',true);
	define("DB_DB",'politics',true);

	date_default_timezone_set("UTC");

	if (empty($_GET)) {
    // no data passed by get
		$start = urlencode('1990-01-01 00:00:00');
		$end = urlencode('2990-01-01 00:00:00');
	}else{
		$_GET = array_change_key_case($_GET, CASE_LOWER);
		$start = $_GET['start'];
		$end = $_GET['end'];
	}

	if(isset($_GET['last'])){
			if(!strcmp($_GET['last'],"")){
				$last = "50";
			}else{
				$last = $_GET['last'];
			}
			$query1 = "SELECT
				SUM(`AL`) as `AL`,
				SUM(`AK`) as `AK`,
				SUM(`AZ`) as `AZ`,
				SUM(`AR`) as `AR`,
				SUM(`CA`) as `CA`,
				SUM(`CO`) as `CO`,
				SUM(`CT`) as `CT`,
				SUM(`DE`) as `DE`,
				SUM(`FL`) as `FL`,
				SUM(`GA`) as `GA`,
				SUM(`HI`) as `HI`,
				SUM(`ID`) as `ID`,
				SUM(`IL`) as `IL`,
				SUM(`IN`) as `IN`,
				SUM(`IA`) as `IA`,
				SUM(`KS`) as `KS`,
				SUM(`KY`) as `KY`,
				SUM(`LA`) as `LA`,
				SUM(`ME`) as `ME`,
				SUM(`MD`) as `MD`,
				SUM(`MA`) as `MA`,
				SUM(`MI`) as `MI`,
				SUM(`MN`) as `MN`,
				SUM(`MS`) as `MS`,
				SUM(`MO`) as `MO`,
				SUM(`MT`) as `MT`,
				SUM(`NE`) as `NE`,
				SUM(`NV`) as `NV`,
				SUM(`NH`) as `NH`,
				SUM(`NJ`) as `NJ`,
				SUM(`NM`) as `NM`,
				SUM(`NY`) as `NY`,
				SUM(`NC`) as `NC`,
				SUM(`ND`) as `ND`,
				SUM(`OH`) as `OH`,
				SUM(`OK`) as `OK`,
				SUM(`OR`) as `OR`,
				SUM(`PA`) as `PA`,
				SUM(`RI`) as `RI`,
				SUM(`SC`) as `SC`,
				SUM(`SD`) as `SD`,
				SUM(`TN`) as `TN`,
				SUM(`TX`) as `TX`,
				SUM(`UT`) as `UT`,
				SUM(`VT`) as `VT`,
				SUM(`VA`) as `VA`,
				SUM(`WA`) as `WA`,
				SUM(`WV`) as `WV`,
				SUM(`WI`) as `WI`,
				SUM(`WY`) as `WY`
			FROM 
			(SELECT * from party
			ORDER BY `party_id` DESC
			LIMIT ".$last.") as subquery";
	}else{
			$query1 = "SELECT
				SUM(`AL`) as `AL`,
				SUM(`AK`) as `AK`,
				SUM(`AZ`) as `AZ`,
				SUM(`AR`) as `AR`,
				SUM(`CA`) as `CA`,
				SUM(`CO`) as `CO`,
				SUM(`CT`) as `CT`,
				SUM(`DE`) as `DE`,
				SUM(`FL`) as `FL`,
				SUM(`GA`) as `GA`,
				SUM(`HI`) as `HI`,
				SUM(`ID`) as `ID`,
				SUM(`IL`) as `IL`,
				SUM(`IN`) as `IN`,
				SUM(`IA`) as `IA`,
				SUM(`KS`) as `KS`,
				SUM(`KY`) as `KY`,
				SUM(`LA`) as `LA`,
				SUM(`ME`) as `ME`,
				SUM(`MD`) as `MD`,
				SUM(`MA`) as `MA`,
				SUM(`MI`) as `MI`,
				SUM(`MN`) as `MN`,
				SUM(`MS`) as `MS`,
				SUM(`MO`) as `MO`,
				SUM(`MT`) as `MT`,
				SUM(`NE`) as `NE`,
				SUM(`NV`) as `NV`,
				SUM(`NH`) as `NH`,
				SUM(`NJ`) as `NJ`,
				SUM(`NM`) as `NM`,
				SUM(`NY`) as `NY`,
				SUM(`NC`) as `NC`,
				SUM(`ND`) as `ND`,
				SUM(`OH`) as `OH`,
				SUM(`OK`) as `OK`,
				SUM(`OR`) as `OR`,
				SUM(`PA`) as `PA`,
				SUM(`RI`) as `RI`,
				SUM(`SC`) as `SC`,
				SUM(`SD`) as `SD`,
				SUM(`TN`) as `TN`,
				SUM(`TX`) as `TX`,
				SUM(`UT`) as `UT`,
				SUM(`VT`) as `VT`,
				SUM(`VA`) as `VA`,
				SUM(`WA`) as `WA`,
				SUM(`WV`) as `WV`,
				SUM(`WI`) as `WI`,
				SUM(`WY`) as `WY`
			FROM party
			WHERE added > '".urldecode($start)."' AND added < '".urldecode($end)."';";
	}

	
	$res = db_query($query1,"array");
	foreach($res[0] as $key => $value) {
		if(is_numeric($key)) unset($res[0][$key]);
	}
	echo(json_encode($res[0]));


function db_query($query,$type="array"){
	if(defined('DB_PORT')){
		$conn = new mysqli(DB_HOST,DB_USER,DB_PW,DB_DB,DB_PORT);
	}else{
		$conn = new mysqli(DB_HOST,DB_USER,DB_PW,DB_DB);
	}

	if (mysqli_connect_errno()) {
		die('Connect Error (' . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
		return false;
	}

	$conn->query("SET NAMES 'utf8';");
	
	if ($result = $conn->query($query)) {
		switch($type){
			case "row":
				$res = $result->fetch_row();
				$result->close();
				break;
			case "array":
				$res = array();
				while($row = $result->fetch_array()){
					$res = array_merge($res,array($row));
				};
				$result->close();
				break;
			case "all":
				$res = $result->fetch_all();
				$result->close();
				break;
			case "none":
				$res = "none";
		}
	}else{
		$res = 'Error';
	}
	$conn->close();
	return $res;
}

?>