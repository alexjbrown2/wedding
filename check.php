<?php

include_once('.secrets.php');
include('includes/db_functions.php');
include('includes/config.php');

$un = $_POST['username'];
$pw = $_POST['password'];

db_connect();
$query = "SELECT username, password FROM user";
$check = db_query($query);

	
while($row = row_fetch_assoc($check)){
	$username = $row['username'];
	$password = $row['password'];
}
if($un == $username && $pw == $password){
	$payload = array(
	"success" => "true"
	);
}
else{
	$payload = array(
	"success" => "false"
	);
}

echo json_encode($payload);
//return $payload;
//db_close();
?>
