<?php

require_once dirname(__FILE__)."/../chk_admin.php";

if (!( isset($_POST['city_id']) && is_string($_POST['city_id']) && preg_match('#^\\d{1,20}$#Duis', $_POST['city_id']) )) {
	header("Location: ".((isset($_POST['ret']) && is_string($_POST['ret'])) ? $_POST['ret'] : "{$www_base}/admin/cities/"));
	exit;
}
$cityId = $_POST['city_id'];

mysql_query(
	"DELETE FROM cities WHERE id = '".mysql_real_escape_string($cityId, $db)."'",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);

header("Location: ".((isset($_POST['ret']) && is_string($_POST['ret'])) ? $_POST['ret'] : "{$www_base}/admin/cities/"));
exit;