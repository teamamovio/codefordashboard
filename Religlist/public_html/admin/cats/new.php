<?php

require_once dirname(__FILE__)."/../chk_admin.php";

if (!( isset($_GET['parent_id']) && is_string($_GET['parent_id']) && preg_match('#^\\d{1,20}$#Duis', $_GET['parent_id']) )) {
	header("Location: {$www_base}/admin/cats/");
	exit;
}
$parentId = $_GET['parent_id'];

$q = mysql_query(
	"SELECT * FROM cats ".
	"WHERE parent_id = 0 AND id = '".mysql_real_escape_string($parentId, $db)."'",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
$parentRow = mysql_fetch_assoc($q);
mysql_free_result($q);
if ($parentRow === false) {
	header("Location: {$www_base}/admin/cats/");
	exit;
}



$f = array();
$f['name'] = (isset($_POST['name']) && is_string($_POST['name'])) ? mb_trim($_POST['name']) : '';
$f['price'] = (isset($_POST['price']) && is_string($_POST['price'])) ? mb_trim($_POST['price']) : '0.00';

$errors = array();
if (isset($_POST['submitted'])) {
	if ($f['name'] == '') {
		$errors[] = "Category Name cannot be empty";
	} elseif (mb_strlen($f['name']) > 255) {
		$errors[] = "Category Name cannot contain more than 255 characters";
	} elseif ($f['name'] == 'events') {
		$errors[] = "'events' is special name and cannot be created";
	}
	
	if (!preg_match('#^\\d+(?:\\.\\d+)?$#Duis', $f['price'])) {
		$errors[] = "Price should be decimal number";
	}
	
	if (count($errors) == 0) {
		mysql_query(
			"INSERT INTO cats SET ".
				"name = '".mysql_real_escape_string($f['name'], $db)."',".
				"price = '".mysql_real_escape_string($f['price'], $db)."',".
				"parent_id = '".mysql_real_escape_string($parentId, $db)."'",
			$db
		) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
		header("Location: {$_SERVER['REQUEST_URI']}");
		exit;
	}
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Add Subcategory to category '<?php h($parentRow['name']); ?>'</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

<script type="text/javascript" src="<?php h("{$www_base}/jquery.js"); ?>"></script>
<script type="text/javascript">
$(document).ready(function () {
	$('[name="name"]').focus();
});
</script>

</head>
<body>

<?php include dirname(__FILE__)."/../menu.php"; ?>
<h1 align="center">Add Subcategory to category '<?php h($parentRow['name']); ?>'</h1>

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
		<b>Name:</b>
	</td>
	<td width="5px">&nbsp;</td>
	<td align="left" valign="top">
		<input type="text" name="name" value="<?php h($f['name']); ?>" size="40" maxlength="255" />
	</td>
</tr>
<tr>
	<td align="right" valign="top">
		<i>Price:</i>
	</td>
	<td width="5px">&nbsp;</td>
	<td align="left" valign="top">
		<input type="text" name="price" value="<?php h($f['price']); ?>" size="40" maxlength="255" />
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td align="left" valign="top">
		<button type="submit">Add Subcategory</button>
	</td>
</tr>
</table>
</form>

<h2 align="center">Existing Subcategories</h2>

<table border="1" align="center">
<tr>
	<th>ID</th>
	<th>Name</th>
</tr>
<?php
$q = mysql_query(
	"SELECT * FROM cats ".
	"WHERE parent_id = '".mysql_real_escape_string($parentId, $db)."' ".
	"ORDER BY name ASC",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
while ($row = mysql_fetch_assoc($q)) {
	?>
	<tr>
		<td><?php h($row['id']); ?></td>
		<td><a href="<?php h("{$www_base}/admin/cats/edit.php?id={$row['id']}"); ?>"><?php h($row['name']); ?></a></td>
	</tr>
	<?php
}
mysql_free_result($q);
?>
</table>


</body>
</html>