<?php

require_once dirname(__FILE__)."/../chk_admin.php";

$ret = (isset($_POST['ret']) && is_string($_POST['ret'])) ? $_POST['ret'] : "{$www_base}/ads/";

if (!( isset($_POST['id']) && is_string($_POST['id']) && preg_match('#^\\d{1,20}$#Duis', $_POST['id']) )) {
	header("Location: {$ret}");
	exit;
}
$adId = $_POST['id'];

mysql_query(
	"DELETE FROM ads ".
	"WHERE id = '".mysql_real_escape_string($adId, $db)."'",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);

header("Location: {$ret}");
exit;