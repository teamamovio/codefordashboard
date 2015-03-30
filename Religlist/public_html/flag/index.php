<?php

require_once dirname(__FILE__)."/../lib/config.php";

if (!( isset($_GET['type']) && is_string($_GET['type']) && in_array($_GET['type'], array('ad','event')) )) {
	header("Location: {$www_base}/");
	exit;
}
$type = $_GET['type'];

if (!( isset($_GET['id']) && is_string($_GET['id']) && preg_match('#^\\d{1,20}$#Duis', $_GET['id']) )) {
	header("Location: {$www_base}/");
	exit;
}
$id = $_GET['id'];

$dbTable = $type == 'ad' ? 'ads' : 'events';
$q = mysql_query(
	"SELECT * FROM {$dbTable} ".
	"WHERE id = '".mysql_real_escape_string($id, $db)."'",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
$postRow = mysql_fetch_assoc($q);
mysql_free_result($q);

if ($postRow === false) {
	header("Location: {$www_base}/");
	exit;
}

$f = array();
$f['email'] = (isset($_POST['email']) && is_string($_POST['email'])) ? mb_trim($_POST['email']) : '';
$f['why'] = (isset($_POST['why']) && is_string($_POST['why'])) ? mb_trim($_POST['why']) : '';
$f['captcha'] = (isset($_POST['captcha']) && is_string($_POST['captcha'])) ? mb_trim($_POST['captcha']) : '';

$errors = array();
if (isset($_POST['submitted'])) {
	if ($f['email'] == '') {
		$errors[] = "Email field cannot be empty";
	} elseif (mb_strlen($f['email']) > 255) {
		$errors[] = "Email field cannot contain more than 255 characters";
	} elseif (!preg_match('#^[^@]+@[^@]+$#Duis', $f['email'])) {
		$errors[] = "Invalid email address";
	}
	
	if ($f['why'] == '') {
		$errors[] = "Please describe why this post is inappropriate";
	}
	
	require_once dirname(__FILE__)."/../securimage/securimage.php";
	$securimage = new Securimage();
	if ($securimage->check($f['captcha']) == false) {
		$errors[] = "Incorrect captcha";
	}
	
	if (count($errors) == 0) {
		$q = mysql_query(
			"SELECT sval FROM static WHERE skey = 'admin_email'",
			$db
		) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
		$row = mysql_fetch_assoc($q);
		mysql_free_result($q);
		$admin_email = $row['sval'];
		
		if ($type == 'ad') {
			$postUrl = "{$www_base}/ads/view.php?id={$id}";
		} else {
			$postUrl = "{$www_base}/events/view.php?id={$id}";
		}
		
		mail(
			$admin_email,
			"Inappropriate post",
			<<<EOF

Someone reported

{$postUrl}

as inappropriate:

{$f['why']}

EOF
,
			"Reply-To: {$f['email']}"
		);
		
		header("Location: {$_SERVER['REQUEST_URI']}&sent=true");
		exit;
	}
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Flat post as inappropriate</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

</head>
<body>

<div class="bread-crumb">
	<a href="<?php h("{$www_base}/"); ?>"><?php h($siteName); ?></a>
	&gt;
	Flag post as inappropriate
</div>

<?php if (isset($_GET['sent'])) { ?>
	<center><b><font color="green">Thank you! Administrator will receive your message!</font></b></center>
<?php } else { ?>

<form method="post" action="<?php h($_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
<input type="hidden" name="submitted" value="yes" />
<table border="0" align="center"><tr><td align="left" valign="top">

<?php if (count($errors) > 0) { ?>
	<font color="red"><ul><?php foreach ($errors as $err) e("<li>{$err}</li>"); ?></ul></font>
<?php } ?>

<b>Your Email:</b><br />
<input type="text" name="email" value="<?php h($f['email']); ?>" size="60" maxlength="255" /><br /><br />

<b>Please describe why this post is inappropriate:</b><br />
<textarea name="why" cols="60" rows="16"><?php h($f['why']); ?></textarea><br /><br />

<img id="captcha" src="<?php h("{$www_base}/securimage/securimage_show.php"); ?>" alt="Captcha Image" /><br />
<input type="text" name="captcha" size="10" maxlength="6" value="<?php h($f['captcha']); ?>" /><br />
<a href="#" onclick="document.getElementById('captcha').src='<?php h("{$www_base}/securimage/securimage_show.php"); ?>?' + Math.random(); return false;">[Different Image]</a><br />

<button type="submit">Report</button>

</td></tr></table>
</form>

<?php } ?>

</body>
</html>