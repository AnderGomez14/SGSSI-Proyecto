<?php
session_start();
if (!isset($_SESSION['username'])) {
	exit;
}
header('Content-Type: application/json');
$json = [];
if ($_GET["file"] == "hashrates" && file_exists(getcwd()."/json/hash-rates.json")) {
	$json = json_decode(file_get_contents(getcwd().'/json/hash-rates.json'), true);
}elseif ($_GET["file"] == "green" && file_exists(getcwd()."/json/green.json")) {
	$json = json_decode(file_get_contents(getcwd().'/json/green.json'), true);
}elseif ($_GET["file"] == "candidato" && file_exists(getcwd()."/json/candidato.json")) {
	$json = json_decode(file_get_contents(getcwd().'/json/candidato.json'), true);
}
if ($json == []) die;
else echo json_encode($json);