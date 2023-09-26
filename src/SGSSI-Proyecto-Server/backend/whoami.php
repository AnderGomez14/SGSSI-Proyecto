<?php
session_start();
if (!isset($_SESSION['username'])) {
	exit;
}
echo $_SESSION['username'];