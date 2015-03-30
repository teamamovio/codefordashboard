<?php

require_once dirname(__FILE__)."/../chk_admin.php";

require_once dirname(__FILE__)."/../../lib/forms/admin/states/NewStateForm.php";
$nsf = new NewStateForm('nsf');
$nsf->run();

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>States</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

<script type="text/javascript" src="<?php h("{$www_base}/jquery.js"); ?>"></script>
<script type="text/javascript">
$(document).ready(function () {
	$('[name="name"]').focus();
	
	$('[name="del"]').on('click', function () {
		return confirm("Delete " + ($(this).parent().parent().parent().find('.name').text().trim()) + "?");
	});
});
</script>

</head>
<body>

<?php include dirname(__FILE__)."/../menu.php"; ?>
<h1 align="center">States</h1>

<form method="post" action="<?php h($_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
<input type="hidden" name="submitted" value="nsf" />
<table border="0" align="center"><tr><td align="left" valign="top">

<?php $nsf->displayField('name'); ?>

<button type="submit">Add State</button>

</td></tr></table>
</form>
<br />

<table border="1" align="center">
<tr>
	<th>ID</th>
	<th>Name</th>
	<th>&nbsp;</th>
</tr>
<?php
$q = mysql_query(
	"SELECT * FROM states ORDER BY name ASC",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
while ($state = mysql_fetch_assoc($q)) {
	?>
	<tr>
		<td><?php h($state['id']); ?></td>
		<td class="name">
			<a href="<?php h("{$www_base}/admin/states/edit.php?state_id={$state['id']}");?>">
				<?php h($state['name']); ?>
			</a>
		</td>
		<td>
			
			<form method="post" action="<?php h("{$www_base}/admin/states/del.php"); ?>" style="display:inline;">
			<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
			<input type="hidden" name="state_id" value="<?php h($state['id']); ?>" />
			<button type="submit" name="del">Delete</button>
			</form>
			
		</td>
	</tr>
	<?php
}
mysql_free_result($q);
?>
</table>

</body>
</html>