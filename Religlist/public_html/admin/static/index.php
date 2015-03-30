<?php

require_once dirname(__FILE__)."/../chk_admin.php";

$pages = array('admin_email','about','terms','feature','feedback','partner','contact',);

$values = array();
$q = mysql_query(
	"SELECT * FROM static",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
while ($row = mysql_fetch_assoc($q)) $values[$row['skey']] = $row['sval'];
mysql_free_result($q);

if (isset($_POST['submitted'])) {
	foreach ($pages as $page) {
		if (isset($_POST[$page]) && is_string($_POST[$page])) {
			mysql_query(
				"UPDATE static SET ".
					"sval = '".mysql_real_escape_string($_POST[$page], $db)."' ".
				"WHERE ".
					"skey = '".mysql_real_escape_string($page, $db)."'",
				$db
			) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
		}
	}
	
	header("Location: {$_SERVER['REQUEST_URI']}");
	exit;
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Static Pages</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

</head>
<body>

<?php include dirname(__FILE__)."/../menu.php"; ?>
<h1 align="center">Static Pages</h1>

<form method="post" action="<?php h($_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
<input type="hidden" name="submitted" value="yes" />
<table border="0" align="center"><tr><td align="left" valign="top">

<?php foreach ($pages as $page) { ?>
	<b><?php h(ucfirst(preg_replace('#_#Duis', ' ', $page))); ?>:</b><br />
	<?php if ($page == 'admin_email') { ?>
		<input type="text" name="<?php h($page); ?>" value="<?php h($values[$page]); ?>" size="60" /><br /><br />
	<?php } else { ?>
		<textarea name="<?php h($page); ?>" cols="60" rows="16"><?php h($values[$page]); ?></textarea><br /><br />
	<?php } ?>
<?php } ?>

<button type="submit">Save</button>

</td></tr></table>
</form>

<br /><br />

</body>
</html>