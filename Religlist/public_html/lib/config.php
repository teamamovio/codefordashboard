<?php

require_once dirname(__FILE__)."/conf.php";
require_once dirname(__FILE__)."/func.php";
require_once dirname(__FILE__)."/db.php";

date_default_timezone_set("UTC");
mb_internal_encoding("UTF-8");

$www_proto = "http";
$www_host = $_SERVER['SERVER_NAME'];
$www_base = "{$www_proto}://{$www_host}{$www_path}";

// Start session
session_set_cookie_params(
	$session_lifetime,
	"{$www_path}/",
	$www_host,
	false, // secure
	true   // HTTP only
);
if (!session_id()) session_start();

// Remove magic quotes if present
if (get_magic_quotes_runtime()) set_magic_quotes_runtime(false);
if (get_magic_quotes_gpc()) {
	function removeMagic($a) {
		if (is_array($a)) {
			foreach ($a as $key => $val) $a[$key] = removeMagic($val);
		} else {
			$a = stripslashes($a);
		}
		return $a;
	}
	$_GET = removeMagic($_GET);
	$_POST = removeMagic($_POST);
	$_COOKIE = removeMagic($_COOKIE);
}

// Protect against CSRF attacks
if (!isset($_SESSION['csrf'])) $_SESSION['csrf'] = genRand(255);
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
	if (!( isset($_POST['csrf']) && is_string($_POST['csrf']) && $_POST['csrf'] === $_SESSION['csrf'] )) {
		exit("CSRF attack");
	}
}

// Return time in $default_timezone or timezone specified in $_SESSION['timezone']
function ut($time, $format = "Y/m/d H:i:s") {
	global $default_timezone;
	
	if (is_null($time)) return "N/A";
	$tz = isset($_SESSION['timezone']) ? $_SESSION['timezone'] : $default_timezone;
	
	$time = new DateTime($time);
	$time->setTimezone(new DateTimeZone($tz));
	return $time->format($format);
}
function u($time, $format = "Y/m/d H:i:s") { h(ut($time, $format)); }

function getChurchInfo() {
	global $db, $www_base;
	
	$ret = ($_SERVER['REQUEST_METHOD'] == 'GET') ? $_SERVER['REQUEST_URI'] : "{$www_base}/";
	
	$churchId = false;
	if (isset($_GET['churchId']) && is_string($_GET['churchId']) && preg_match('#^\\d{1,20}$#Duis', $_GET['churchId'])) {
		$churchId = $_GET['churchId'];
	} elseif (isset($_SESSION['churchId'])) {
		$churchId = $_SESSION['churchId'];
	}
	if ($churchId === false) {
		header("Location: {$www_base}/churches.php?ret=".urlencode($ret));
		exit;
	}
	
	$q = mysql_query(
		"SELECT * FROM churches ".
		"WHERE id = '".mysql_real_escape_string($churchId, $db)."'",
		$db
	) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
	$churchRow = mysql_fetch_assoc($q);
	mysql_free_result($q);
	
	if ($churchRow === false) {
		header("Location: {$www_base}/churches.php?ret=".urlencode($ret));
		exit;
	}
	
	$q = mysql_query(
		"SELECT * FROM cities ".
		"WHERE id = '".mysql_real_escape_string($churchRow['city_id'], $db)."'",
		$db
	) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
	$cityRow = mysql_fetch_assoc($q);
	mysql_free_result($q);
	
	$q = mysql_query(
		"SELECT * FROM states ".
		"WHERE id = '".mysql_real_escape_string($cityRow['state_id'], $db)."'",
		$db
	) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
	$stateRow = mysql_fetch_assoc($q);
	mysql_free_result($q);
	
	$churchRow['cityRow'] = $cityRow;
	$churchRow['stateRow'] = $stateRow;
	
	return $churchRow;
}