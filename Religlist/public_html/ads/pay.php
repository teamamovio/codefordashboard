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

require_once dirname(__FILE__)."/../lib/Paypal.php";
$paypal = new Paypal($PAYPAL['USER'], $PAYPAL['PWD'], $PAYPAL['SIGNATURE'], $PAYPAL['sandbox']);
$response = $paypal->request("SetExpressCheckout", array(
	'RETURNURL' => "{$www_base}/ads/paid.php",
	'CANCELURL' => "{$www_base}/ads/not.paid.php",
	
	'L_PAYMENTREQUEST_0_NAME0' => "Posting in paid category '{$adRow['catName']}'",
	'L_PAYMENTREQUEST_0_AMT0' => "{$adRow['catPrice']}",
	
	'PAYMENTREQUEST_0_AMT' => "{$adRow['catPrice']}",
	'NOSHIPPING' => '1',
	'PAYMENTREQUEST_0_CURRENCYCODE' => 'USD',
	
	'LOGOIMG' => "{$www_base}/logo2.png",
));
if (is_array($response) && $response['ACK'] == 'Success') {
	$_SESSION['paypal_adId'] = $adId;
	
	header("Location: https://{$paypal->domain}/webscr?cmd=_express-checkout&token={$response['TOKEN']}");
	exit;
} else {
	e("Paypal error: ");
	e('<pre>');print_r($response);e('</pre>');
	exit;
}