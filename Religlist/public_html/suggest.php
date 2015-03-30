<?php

require_once dirname(__FILE__)."/lib/config.php";

if (!( isset($_GET['q']) && is_string($_GET['q']) )) {
	header("Location: {$www_base}/");
	exit;
}
$_GET['q'] = mb_trim($_GET['q']);
if ($_GET['q'] == '') {
	header("Location: {$www_base}/");
	exit;
}

$f = array();
$f['email'] = (isset($_POST['email']) && is_string($_POST['email'])) ? mb_trim($_POST['email']) : '';
$f['q'] = (isset($_POST['q']) && is_string($_POST['q'])) ? mb_trim($_POST['q']) : $_GET['q'];
$f['address'] = (isset($_POST['address']) && is_string($_POST['address'])) ? mb_trim($_POST['address']) : '';
$f['city'] = (isset($_POST['city']) && is_string($_POST['city'])) ? mb_trim($_POST['city']) : '';
$f['state'] = (isset($_POST['state']) && is_string($_POST['state'])) ? mb_trim($_POST['state']) : '';
$f['msg'] = (isset($_POST['msg']) && is_string($_POST['msg'])) ? mb_trim($_POST['msg']) : '';

$errors = array();
if (isset($_POST['submitted'])) {
	
	if ($f['email'] == '') {
		$errors[] = "Email field cannot be empty";
	} elseif (mb_strlen($f['email']) > 255) {
		$errors[] = "Email field cannot contain more than 255 characters";
	} elseif (!preg_match('#^[^@]+@[^@]+$#Duis', $f['email'])) {
		$errors[] = "Invalid email";
	}
	
	if ($f['q'] == '') {
		$errors[] = "Church name cannot be empty";
	}
	
	if (count($errors) == 0) {
		$q = mysql_query(
			"SELECT sval FROM static WHERE skey = 'admin_email'",
			$db
		) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
		$row = mysql_fetch_assoc($q);
		mysql_free_result($q);
		$admin_email = $row['sval'];
		
		mail(
			$admin_email,
			"Church Suggested",
			<<<EOF

Someone suggested to add church {$f['q']}

Email: {$f['email']}
Church Name: {$f['q']}
Address: {$f['address']}
City: {$f['city']}
State: {$f['state']}

User Message:

{$f['msg']}

EOF
			,
			"Reply-To: {$f['email']}"
		);
		
		header("Location: {$www_base}/suggested.php");
		exit;
	}
	
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Suggest church</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

</head>
<body>

<div class="bread-crumb">
<a href="<?php h("{$www_base}/churches.php"); ?>"><?php h($siteName); ?></a>
&gt;
Suggest church
</div>

<h1 align="center">Suggest church</h1>

<form method="post" action="<?php h($_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
<input type="hidden" name="submitted" value="yes" />
<table border="0" align="center"><tr><td align="left" valign="top">

<?php if (count($errors) > 0) { ?>
	<font color="red"><ul><?php foreach ($errors as $err) e("<li>{$err}</li>"); ?></ul></font>
<?php } ?>

<b>Your Email:</b><br />
<input type="text" name="email" value="<?php h($f['email']); ?>" size="60" maxlength="255" /><br /><br />

<b>Church Name:</b><br />
<input type="text" name="q" value="<?php h($f['q']); ?>" size="60" maxlength="255" /><br /><br />

<i>Address:</i><br />
<input type="text" name="address" value="<?php h($f['address']); ?>" size="60" /><br /><br />

<i>City:</i><br />
<input type="text" name="city" value="<?php h($f['city']); ?>" size="60" /><br /><br />

<i>State:</i><br />
<input type="text" name="state" value="<?php h($f['state']); ?>" size="60" /><br /><br />

<i>Message:</i><br />
<textarea name="msg" cols="60" rows="16"><?php h($f['msg']); ?></textarea><br /><br />

<button type="submit">Suggest church</button>

</td></tr></table>
</form>

</body>
</html>