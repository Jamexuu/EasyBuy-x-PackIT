<?php
  $dbhost = "localhost";
	$dbuser = "root";
	$dbpass = "";
    //dito lalagay yung name ng db
	$db = "sis";

	$conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $conn -> error);

	if(!$conn)
	{
		die("Connection Failed. ". mysqli_connect_error());
	}

  function executeQuery($query){
    $conn = $GLOBALS['conn'];
    return mysqli_query($conn, $query);
  }
?>