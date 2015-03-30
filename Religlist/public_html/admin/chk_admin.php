<?php

require_once dirname(__FILE__)."/../lib/config.php";

if (!isset($_SESSION['admin'])) {
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		header("Location: {$www_base}/admin/login.php?ret=".urlencode($_SERVER['REQUEST_URI']));
	} else {
		header("Location: {$www_base}/admin/login.php");
	}
	exit;
}