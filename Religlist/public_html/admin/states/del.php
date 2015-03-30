<?php

require_once dirname(__FILE__)."/../chk_admin.php";

if (!( isset($_POST['state_id']) && is_string($_POST['state_id']) && preg_match('#^\\d{1,20}$#Duis', $_POST['state_id']) )) {
	header("Location: {$www_base}/admin/states/");
	exit;
}
$stateId = $_POST['state_id'];

mysql_query(
	"DELETE FROM states ".
	"WHERE id = '".mysql_real_escape_string($stateId, $db)."'",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);

header("Location: {$www_base}/admin/states/");
exit;