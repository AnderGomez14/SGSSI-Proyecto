<?php

# Resetar password

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');

session_start();
if(!isset($_SESSION['username'])) {
    exit('notLoggedIn');
}

$usersData = file_get_contents(getcwd().'/json/pool.json');
$usersJson = json_decode($usersData, true);
$users = $usersJson['users'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['CONTENT_TYPE'] === 'application/json') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $_SESSION['username'];
    $password = $data['password'];
    $newPassword = $data['newPassword'];

    if (isset($users[$username]) && password_verify($password, $users[$username]["password"])) {
        $users[$username]["password"] = password_hash($newPassword, PASSWORD_DEFAULT);
        $users[$username]["reset"] = false;
        $usersJson['users'] = $users;
        file_put_contents(getcwd().'/json/pool.json', json_encode($usersJson));
        exit('passwordReset');
    } else {
        echo 'Wrong username or password';
    }
} else {
    echo 'Wrong request method';
}