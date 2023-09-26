<?php
session_start();
if (!isset($_SESSION['username'])) {
	exit;
}
// Si es get, es para obtener el poolKey
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$usersData = file_get_contents(getcwd().'/json/pool.json');
	$usersJson = json_decode($usersData, true);
	$poolKey = $usersJson['poolKey'];
	echo $poolKey;
}
// Si es post, es para cambiar el poolKey
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$usersData = file_get_contents(getcwd().'/json/pool.json');
	$usersJson = json_decode($usersData, true);
	$usersJson['poolKey'] = json_decode(file_get_contents('php://input'), true)['poolKey'];
	$usersData = json_encode($usersJson);
	file_put_contents(getcwd().'/json/pool.json', $usersData);
	echo '1';
}