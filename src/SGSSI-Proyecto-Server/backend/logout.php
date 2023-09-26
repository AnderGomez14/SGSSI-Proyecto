<?php
//Cierre de sesión
session_start();
if (isset($_SESSION['username'])) {
	session_destroy();
	header("Location: /");
	exit;
}