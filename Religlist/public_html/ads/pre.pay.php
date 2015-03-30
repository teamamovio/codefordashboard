<?php

require_once dirname(__FILE__)."/../lib/config.php";

if (!( isset($_GET['id']) && is_string($_GET['id']) && preg_match('#^\\d{1,20}$#Duis', $_GET['id']) )) {
	header("Location: {$www_base}/ads/new.php");
	exit;
}
$adId = $_GET['id'];

$q = mysql_query(
	"SELECT ads.*, cats.price AS catPrice, cats.name AS catName ".
	"FROM ads LEFT JOIN cats ON ads.cat_id = cats.id ".
	"WHERE ads.id = '".mysql_real_escape_string($adId, $db)."'",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
$adRow = mysql_fetch_assoc($q);
mysql_free_result($q);

if ($adRow === false) {
	header("Location: {$www_base}/ads/new.php");
	exit;
}

if ($adRow['catPrice'] == 0) {
	header("Location: {$www_base}/ads/thanks.php?id={$adId}");
	exit;
}

if ($adRow['is_visible'] == 'yes') {
	header("Location: {$www_base}/ads/view.php?id={$adId}");
	exit;
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>You have chosen to post in a paid section of <?php h($siteName); ?>.</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

</title>
<body>

<div style="padding: 25px;">
<h1 align="center">You have chosen to post in a paid section of <?php h($siteName); ?>. Please make a $<?php h(preg_replace('#\\.00$#Duis', '', sprintf("%.2f", $adRow['catPrice']))); ?> payment via a secured website (PayPal) to complete your post.</h1>

<center>
<form method="get" action="<?php h("{$www_bases}/ads/pay.php"); ?>">
<input type="hidden" name="id" value="<?php h($adId); ?>" />
<button type="submit">Make a payment</button>
</form>
</center>
</div>

</body>
</html>