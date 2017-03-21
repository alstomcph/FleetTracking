<?php 
	$acct = $_GET["acct"];
	$dev = $_GET["dev"];
	$code = $_GET["code"];
	$gprmcE = explode(',', $_GET["gprmc"]);
	
	$data['wgs84_lat'] = DMStoDEC($gprmcE[3]);
	$data['wgs84_lng'] = DMStoDEC($gprmcE[5]);

	$servername = "localhost";
	$username = "SEKO";
	$password = "SEKO";
	$dbname = "gps";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
	else{
		$sql = "INSERT INTO points (acct, dev, latitude, longitude, speed, angle)
		VALUES ('" .$acct. "','" .$dev. "','" .$data['wgs84_lat']. "','" .$data['wgs84_lng']. "'," .$gprmcE[7]. "," .$gprmcE[8].")";

		if ($conn->query($sql) === TRUE) {
			echo "OK";
		} 
		else {
			echo "ERROR";
		}
		$conn->close();
	}

	function DMStoDEC($dms){
		$dotpos = strpos($dms,'.');

		if($dotpos == 4){
			$deg = substr($dms, 0, 2);
			$min = substr($dms, 2);
		}
		else{
			$deg = substr($dms, 0, 3);
			$min = substr($dms, 3);
		}

		return $deg+($min/60);
	}

	/*
	1    = UTC of position fix
	2    = Data status (V=navigation receiver warning)
	3    = Latitude of fix
	4    = N or S
	5    = Longitude of fix
	6    = E or W
	7    = Speed over ground in knots
	8    = Track made good in degrees True
	9    = UT date
	10   = Magnetic variation degrees (Easterly var. subtracts from true course)
	11   = E or W
	12   = Checksum
	*/
?>

