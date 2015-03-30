<?php

require_once dirname(__FILE__)."/../chk_admin.php";

$ret = (isset($_POST['ret']) && is_string($_POST['ret'])) ? $_POST['ret'] : "{$www_base}/admin/banners/";

if (!( isset($_POST['id']) && is_string($_POST['id']) && preg_match('#^\\d{1,20}$#Duis', $_POST['id']) )) {
	header("Location: {$ret}");
	exit;
}
$bannerId = $_POST['id'];

mysql_query(
	"DELETE FROM banners_churches ".
	"WHERE banner_id = '".mysql_real_escape_string($bannerId, $db)."'",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);

mysql_query(
	"DELETE FROM banners ".
	"WHERE id = '".mysql_real_escape_string($bannerId, $db)."'",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);

header("Location: {$ret}");
exit;