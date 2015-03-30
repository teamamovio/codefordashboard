<?php

require_once dirname(__FILE__)."/../lib/config.php";

if (isset($_SESSION['paypal_adId'])) {
	mysql_query(
		"DELETE FROM ads ".
		"WHERE id = '".mysql_real_escape_string($_SESSION['paypal_adId'], $db)."'",
		$db
	) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
}

header("Location: {$www_base}/");
exit;