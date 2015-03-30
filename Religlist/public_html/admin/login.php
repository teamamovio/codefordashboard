<?php

require_once dirname(__FILE__)."/../lib/config.php";

$f = array();
$f['username'] = (isset($_POST['username']) && is_string($_POST['username'])) ? mb_trim($_POST['username']) : '';
$f['password'] = (isset($_POST['password']) && is_string($_POST['password'])) ? mb_trim($_POST['password']) : '';

$errors = array();
if (isset($_POST['submitted'])) {
	$q = mysql_query(
		"SELECT * FROM admins ".
		"WHERE ".
			"username = '".mysql_real_escape_string($f['username'], $db)."' AND ".
			"password = '".mysql_real_escape_string($f['password'], $db)."'",
		$db
	) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
	$adminRow = mysql_fetch_assoc($q);
	mysql_free_result($q);
	
	if ($adminRow === false) {
		$errors[] = "Access Denied!";
	} else {
		$_SESSION['admin'] = $adminRow;
		
		$ret = (isset($_GET['ret']) && is_string($_GET['ret'])) ? $_GET['ret'] : "{$www_base}/admin/";
		
		header("Location: {$ret}");
		exit;
	}
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Admin Login</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

</head>
<body>

<h1 align="center">Admin Login</h1>

<form method="post" action="<?php h($_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
<input type="hidden" name="submitted" value="yes" />
<table border="0" align="center">
<?php if (count($errors) > 0) { ?>
	<tr>
		<td colspan="3" align="left" valign="top">
			<font color="red"><ul><?php foreach ($errors as $err) e("<li>{$err}</li>"); ?></ul></font>
		</td>
	</tr>
<?php } ?>
<tr>
	<td align="right" valign="top">
		<b>Username:</b>
	</td>
	<td width="5px">&nbsp;</td>
	<td align="left" valign="top">
		<input type="text" name="username" value="<?php h($f['username']); ?>" size="40" maxlength="255" />
	</td>
</tr>
<tr>
	<td align="right" valign="top">
		<b>Password:</b>
	</td>
	<td width="5px">&nbsp;</td>
	<td align="left" valign="top">
		<input type="password" name="password" value="<?php h($f['password']); ?>" size="40" maxlength="255" />
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td align="left" valign="top">
		<button type="submit">Login</button>
	</td>
</tr>
</table>
</form>

</body>
</html>