<?php

require_once dirname(__FILE__)."/chk_admin.php";

$f = array();
$f['old_password'] = (isset($_POST['old_password']) && is_string($_POST['old_password'])) ? $_POST['old_password'] : '';
$f['new_password'] = (isset($_POST['new_password']) && is_string($_POST['new_password'])) ? $_POST['new_password'] : '';
$f['new_password2'] = (isset($_POST['new_password2']) && is_string($_POST['new_password2'])) ? $_POST['new_password2'] : '';
$errors = array();
if (isset($_POST['submitted'])) {
	if ($f['old_password'] == '') {
		$errors[] = "Old Password field is required";
	} elseif ($f['old_password'] != $_SESSION['admin']['password']) {
		$errors[] = "Wrong old password";
	}
	
	if ($f['new_password'] == '') {
		$errors[] = "New Password field is required";
	} elseif (strlen($f['new_password']) < 8) {
		$errors[] = "New Password must contain at least 8 characters";
	} elseif (strlen($f['new_password']) > 255) {
		$errors[] = "New Password cannot contain more than 255 characters";
	}
	
	if ($f['new_password'] != $f['new_password2']) {
		$errors[] = "Passwords don't match";
	}
	
	if (count($errors) == 0) {
		mysql_query(
			"UPDATE admins SET ".
				"password = '".mysql_real_escape_string($f['new_password'], $db)."' ".
			"WHERE ".
				"id = '".mysql_real_escape_string($_SESSION['admin']['id'], $db)."'",
			$db
		) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
		
		header("Location: {$www_base}/admin/logout.php");
		exit;
	}
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Change Password</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

</head>
<body>

<?php include dirname(__FILE__)."/menu.php"; ?>
<h1 align="center">Change Password</h1>

<form method="post" action="<?php h($_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
<input type="hidden" name="submitted" value="yes" />
<table border="0" align="center"><tr><td align="left" valign="top">

<?php if (count($errors) > 0) { ?>
	<font color="red"><ul><?php foreach ($errors as $err) e("<li>{$err}</li>"); ?></ul></font>
<?php } ?>

<b>Old Password:</b><br />
<input type="password" name="old_password" value="<?php h($f['old_password']); ?>" size="60" maxlength="255" />
<br /><br />

<b>New Password:</b><br />
<input type="password" name="new_password" value="<?php h($f['new_password']); ?>" size="60" maxlength="255" />
<br /><br />

<b>Repeat New Password:</b><br />
<input type="password" name="new_password2" value="<?php h($f['new_password2']); ?>" size="60" maxlength="255" />
<br /><br />

<button type="submit">Change Password</button>

</td></tr></table>
</form>

</body>
</html>