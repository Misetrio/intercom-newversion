<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$login = 'eurofon';
	$pass = '70bura07%%han';
	$host = '77.244.144.51';

	$id_client = $_POST['id'];
	$summa = $_POST['summ'];

	mysql_connect($host,$login,$pass) or die("code=3 Error connect");
	mysql_select_db("voipswitch") or die("code=3 Error select db");
	$query = "Update clientsshared set account_state=account_state+" .$summa." where id_client=".$id_client;
	mysql_query($query) or die("code=3 Error update".mysql_error());

	echo 'ok';
} else {
	header("Location: http://intercom.tj");
}
