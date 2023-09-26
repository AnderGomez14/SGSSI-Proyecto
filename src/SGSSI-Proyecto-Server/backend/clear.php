<?php
session_start();
if (!isset($_SESSION['username'])) {
	exit;
}
unlink(getcwd()."/json/green.json");
if ($_GET["modo"] == "reset") {
    unlink(getcwd()."/json/candidato.json");
    unlink(getcwd()."/json/hash-rates.json");
}
