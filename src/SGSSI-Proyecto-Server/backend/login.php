<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');

session_start();
session_destroy();
session_start();

$usersData = file_get_contents(getcwd().'/json/pool.json');
$usersJson = json_decode($usersData, true);
$users = $usersJson['users'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['CONTENT_TYPE'] === 'application/json') {
	$data = json_decode(file_get_contents('php://input'), true);
	$username = $data['username'];
	$password = $data['password'];

	if (isset($users[$username]) && password_verify($password, $users[$username]["password"])) {
		$_SESSION['username'] = $username;
		if ($users[$username]["reset"])	{
			$_SESSION['reset'] = true;
			exit("resetPassword");
		}
		exit('loggedIn');
	} else {
		echo 'Wrong username or password';
	}
} else {
	echo 'Wrong request method';
}
