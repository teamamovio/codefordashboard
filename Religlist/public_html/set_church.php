<?php

require_once dirname(__FILE__)."/lib/config.php";

if (!( isset($_GET['church_id']) && is_string($_GET['church_id']) && preg_match('#^\\d{1,20}$#Duis', $_GET['church_id']) )) {
	header("Location: {$www_base}/churches.php");
	exit;
}

$_SESSION['churchId'] = $_GET['church_id'];

$ret = (isset($_GET['ret']) && is_string($_GET['ret'])) ? $_GET['ret'] : "{$www_base}/";

header("Location: {$ret}");
exit;