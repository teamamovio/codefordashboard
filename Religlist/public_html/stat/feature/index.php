<?php

require_once dirname(__FILE__)."/../../lib/config.php";

$q = mysql_query(
	"SELECT sval FROM static WHERE skey = 'admin_email'",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
$row = mysql_fetch_assoc($q);
mysql_free_result($q);
$admin_email = $row['sval'];

$f = array();
$f['email'] = (isset($_POST['email']) && is_string($_POST['email'])) ? mb_trim($_POST['email']) : '';
$f['msg'] = (isset($_POST['msg']) && is_string($_POST['msg'])) ? mb_trim($_POST['msg']) : '';
$errors = array();
if (isset($_POST['submitted'])) {
	if ($f['email'] == '') {
		$errors[] = "Email field is required";
	} elseif (mb_strlen($f['email']) > 255) {
		$errors[] = "Email field cannot contain more than 255 characters";
	} elseif (!preg_match('#^[^@]+@[^@]+$#Duis', $f['email'])) {
		$errors[] = "Invalid email";
	}
	
	if ($f['msg'] == '') {
		$errors[] = "Please enter the message";
	}
	
	if (count($errors) == 0) {
		mail(
			$admin_email,
			"Feature and ad",
			$f['msg'],
			"Reply-To: {$f['email']}"
		);
		header("Location: {$_SERVER['REQUEST_URI']}?succ=true");
		exit;
	}
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Feature an ad</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

</head>
<body>

<div class="bread-crumb">
	<a href="<?php h("{$www_base}/"); ?>"><?php h($siteName); ?></a>
	&gt;
	Feature an ad
</div>

<table border="0" align="center">
<tr>
	<td align="left" valign="top" style="width: 650px; padding: 10px;">

<p align="justify">
<?php
$q = mysql_query(
	"SELECT sval FROM static WHERE skey = 'feature'",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
$row = mysql_fetch_assoc($q);
mysql_free_result($q);
e(nl2br($row['sval']));
?>
</p>

<?php if (isset($_GET['succ'])) { ?>
	<center><b><font color="green">Message Sent</font></b></center>
<?php } else { ?>
	<form method="post" action="<?php h($_SERVER['REQUEST_URI']); ?>">
	<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
	<input type="hidden" name="submitted" value="yes" />
	<table border="0" align="left" width="100%"><tr><td align="left" valign="top">
	
	<?php if (count($errors) > 0) { ?>
		<font color="red"><ul><?php foreach ($errors as $err) e("<li>{$err}</li>"); ?></ul></font>
	<?php } ?>
	
	<b>Email:</b><br />
	<input type="text" name="email" value="<?php h($f['email']); ?>" style="width: 100%;" maxlength="255" /><br /><br />
	
	<b>Message:</b><br />
	<textarea name="msg" style="width: 100%;" rows="16"><?php h($f['msg']); ?></textarea><br /><br />
	
	<button type="submit">Send</button>
	
	</td></tr></table>
	</form>
<?php } ?>

<br /><br />
<?php include dirname(__FILE__)."/../../footer.php"; ?>

	</td>
	<td style="width:10px;">&nbsp;</td>
	<td align="center" valign="top" class="side-column">
		<?php include dirname(__FILE__)."/../../banner.php"; ?>
	</td>
</tr>
</table>

</body>
</html>