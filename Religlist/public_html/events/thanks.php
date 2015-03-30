<?php

require_once dirname(__FILE__)."/../lib/config.php";

if (!( isset($_GET['id']) && is_string($_GET['id']) && preg_match('#^\\d{1,20}$#Duis', $_GET['id']) )) {
	header("Location: {$www_base}/events/new.php");
	exit;
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Thank you for posting with <?php h($siteName); ?>!</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

</head>
<body>

<h1 align="center">Thank you for posting with <?php h($siteName); ?>!</h1>
<p align="center"><a href="<?php h("{$www_base}/events/view.php?id={$_GET['id']}"); ?>">See your post</a></p>

</body>
</html>