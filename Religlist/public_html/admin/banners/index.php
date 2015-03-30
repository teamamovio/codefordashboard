<?php

require_once dirname(__FILE__)."/../chk_admin.php";

$banners = array();
$q = mysql_query(
	"SELECT * FROM banners ".
	"ORDER BY id DESC",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
while ($banner = mysql_fetch_assoc($q)) $banners[] = $banner;
mysql_free_result($q);

foreach ($banners as &$banner) {
	$banner['churches'] = array();
	$q = mysql_query(
		"SELECT churches.id, churches.name ".
		"FROM banners_churches LEFT JOIN churches ON banners_churches.church_id = churches.id ".
		"WHERE banners_churches.banner_id = '".mysql_real_escape_string($banner['id'], $db)."' ".
		"ORDER BY churches.name ASC",
		$db
	) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
	while ($church = mysql_fetch_assoc($q)) $banner['churches'][] = $church;
	mysql_free_result($q);
}
unset($banner);

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Banners</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

<script type="text/javascript" src="<?php h("{$www_base}/jquery.js"); ?>"></script>
<script type="text/javascript">
$(document).ready(function () {
	$('[name="delete"]').on('click', function () {
		return confirm("Delete Banner?");
	});
});
</script>

</head>
<body>

<?php include dirname(__FILE__)."/../menu.php"; ?>
<h1 align="center">Banners</h1>

<center>
<form method="get" action="<?php h("{$www_base}/admin/banners/new.php"); ?>">
<button type="submit">Add Banner</button>
</form>
</center>

<br />

<table border="1" align="center">
<tr>
	<th>HTML Code</th>
	<th>Churches</th>
	<th>&nbsp;</th>
</tr>
<?php foreach ($banners as $banner) { ?>
	<tr>
		<td align="left" valign="top"><?php h($banner['html']); ?></td>
		<td align="left" valign="top">
			<ul>
				<?php foreach ($banner['churches'] as $church) { ?>
					<li><?php h($church['name']); ?></li>
				<?php } ?>
			</ul>
		</td>
		<td align="left" valign="top">
			
			<form method="get" action="<?php h("{$www_base}/admin/banners/view.php"); ?>" style="display:inline;">
			<input type="hidden" name="id" value="<?php h($banner['id']); ?>" />
			<button type="submit">Edit</button>
			</form>
			
			<br /><br />
			
			<form method="post" action="<?php h("{$www_base}/admin/banners/del.php"); ?>" style="display:inline;">
			<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
			<input type="hidden" name="id" value="<?php h($banner['id']); ?>" />
			<button type="submit" name="delete">Delete</button>
			</form>
			
		</td>
	</tr>
<?php } ?>
</table>

</body>
</html>