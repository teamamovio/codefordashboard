<?php

require_once dirname(__FILE__)."/../lib/config.php";

if (!( isset($_GET['id']) && is_string($_GET['id']) && preg_match('#^\\d{1,20}$#Duis', $_GET['id']) )) {
	header("Location: {$www_base}/ads/");
	exit;
}
$adId = $_GET['id'];

$q = mysql_query(
	"SELECT ".
		"ads.*, churches.name AS churchName, cats.name AS catName, ".
		"cities.name AS cityName, states.name AS stateName ".
	"FROM ads ".
		"LEFT JOIN churches ON ads.church_id = churches.id ".
		"LEFT JOIN cats ON ads.cat_id = cats.id ".
		"LEFT JOIN cities ON churches.city_id = cities.id ".
		"LEFT JOIN states ON cities.state_id = states.id ".
	"WHERE ads.id = '".mysql_real_escape_string($adId, $db)."'",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
$adRow = mysql_fetch_assoc($q);
mysql_free_result($q);

if ($adRow === false) {
	header("Location: {$www_base}/ads/");
	exit;
}

$f = array();
$f['email'] = (isset($_POST['email']) && is_string($_POST['email'])) ? mb_trim($_POST['email']) : '';
$f['msg'] = (isset($_POST['msg']) && is_string($_POST['msg'])) ? mb_trim($_POST['msg']) : '';
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
	
	if ($f['msg'] == '') {
		$errors[] = "Message field cannot be empty";
	}
	
	require_once dirname(__FILE__)."/../securimage/securimage.php";
	$securimage = new Securimage();
	if ($securimage->check($f['captcha']) == false) {
		$errors[] = "Incorrect captcha";
	}
	
	if (count($errors) == 0) {
		mail(
			$adRow['email'],
			"Re: {$adRow['title']}",
			$f['msg'],
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
<title><?php h($adRow['title']); ?></title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

</head>
<body>

<div class="bread-crumb">
	<a href="<?php h("{$www_base}/churches.php"); ?>"><?php h($siteName); ?></a>
	&gt;
	<?php if (!isset($_SESSION['churchId']) || $_SESSION['churchId'] != $adRow['church_id']) { ?>
		<a href="<?php h("{$www_base}/?churchId={$adRow['church_id']}"); ?>"><?php h($adRow['churchName']); ?></a>
	<?php } else { ?>
		<a href="<?php h("{$www_base}/"); ?>"><?php h($adRow['churchName']); ?></a>
	<?php } ?>
	&gt;
	<a href="<?php h("{$www_base}/ads/?churchId={$adRow['church_id']}&cat={$adRow['cat_id']}"); ?>">
		<?php h($adRow['catName']); ?>
	</a>
	&gt;
	<a href="<?php h("{$www_base}/ads/view.php?id={$adRow['id']}"); ?>">
		<?php h($adRow['title']); ?>
	</a>
	&gt;
	reply
</div>

<div style="margin-left: 100px; margin-right: 100px;">
<?php if (isset($_GET['sent'])) { ?>
	<center><font color="green"><b>Message Sent!</b></font></center>
<?php } else { ?>
<!-- 	<div style="font-size: 20pt; margin-top: 25px;">Reply to this ad:</div> -->
	
	<form method="post" action="<?php h($_SERVER['REQUEST_URI']); ?>">
	<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
	<input type="hidden" name="submitted" value="yes" />
	
	<?php if (count($errors) > 0) { ?>
		<font color="red"><ul><?php foreach ($errors as $err) e("<li>{$err}</li>"); ?></ul></font>
	<?php } ?>
	
	<b>Your Email:</b><br />
	<input type="text" name="email" value="<?php h($f['email']); ?>" style="width: 100%;" maxlength="255" /><br />
	
	<b>Message:</b><br />
	<textarea name="msg" style="width: 100%;" rows="16"><?php h($f['msg']); ?></textarea><br />
	
	<img id="captcha" src="<?php h("{$www_base}/securimage/securimage_show.php"); ?>" alt="Captcha Image" /><br />
	<input type="text" name="captcha" size="10" maxlength="6" value="<?php h($f['captcha']); ?>" /><br />
	<a href="#" onclick="document.getElementById('captcha').src='<?php h("{$www_base}/securimage/securimage_show.php"); ?>?' + Math.random(); return false;">[Different Image]</a><br />
	
	<button type="submit">Reply</button>
	
	</form>
<?php } ?>

<?php include dirname(__FILE__)."/../footer.php"; ?>

</div>

</body>
</html>