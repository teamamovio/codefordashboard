<?php

require_once dirname(__FILE__)."/../lib/config.php";
if (!isset($_SESSION['paypal_adId'])) exit;
$adId = $_SESSION['paypal_adId'];

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

if (isset($_GET['token']) && !empty($_GET['token'])) {
	require_once dirname(__FILE__)."/../lib/Paypal.php";
	$paypal = new Paypal($PAYPAL['USER'], $PAYPAL['PWD'], $PAYPAL['SIGNATURE'], $PAYPAL['sandbox']);
	$response = $paypal->request("DoExpressCheckoutPayment", array(
		'TOKEN' => $_GET['token'],
		'PAYMENTACTION' => 'Sale',
		'PAYERID' => $_GET['PayerID'],
		'PAYMENTREQUEST_0_AMT' => "{$adRow['catPrice']}",
		'PAYMENTREQUEST_0_CURRENCYCODE' => "USD",
	));
	if (is_array($response) && $response['ACK'] == 'Success') {
		unset($_SESSION['paypal_adId']);
		mysql_query(
			"UPDATE ads SET ".
				"is_visible = 'yes' ".
			"WHERE ".
				"id = '".mysql_real_escape_string($adId, $db)."'",
			$db
		) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
		header("Location: {$www_base}/ads/thanks.php?id={$adId}");
		exit;
	} else {
		e("Paypal error: ");
		print_r($response);
	}
}
