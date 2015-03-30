<?php

if (!isset($churchInfo)) {
	if (isset($churchRow)) {
		$churchInfo = $churchRow;
	} else {
		$churchInfo = getChurchInfo();
	}
}

$q = mysql_query(
	"SELECT banners.* ".
	"FROM banners_churches LEFT JOIN banners ON banners_churches.banner_id = banners.id ".
	"WHERE banners_churches.church_id = '".mysql_real_escape_string($churchInfo['id'], $db)."' ".
	"ORDER BY RAND() LIMIT 1",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
$bannerRow = mysql_fetch_assoc($q);
mysql_free_result($q);

if ($bannerRow !== false) {
	e($bannerRow['html']);
}