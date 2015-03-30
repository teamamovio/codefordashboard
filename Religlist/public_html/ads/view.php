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
	"WHERE ".
		"ads.id = '".mysql_real_escape_string($adId, $db)."' AND ".
		"is_visible = 'yes'",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
$adRow = mysql_fetch_assoc($q);
mysql_free_result($q);

if ($adRow === false) {
	header("Location: {$www_base}/ads/");
	exit;
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title><?php h($adRow['title']); ?></title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

<script type="text/javascript" src="<?php h("{$www_base}/jquery.js"); ?>"></script>
<script type="text/javascript">
$(document).ready(function () {
	$('.delete').on('click', function () {
		return confirm('Delete ' + $(this).parent().find('[name="title"]').val() + '?');
	});
});
</script>

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
	<?php h($adRow['title']); ?>
</div>

<table border="0" align="center">
<tr>
	<td align="left" valign="top" style="width: 650px;">

<div class="location">
	<span class="one"><?php h("{$adRow['stateName']} / {$adRow['cityName']}"); ?></span><br />
	<span class="two"><?php h($adRow['churchName']); ?></span>
</div>

<div style="font-size: 20pt; text-align: center; margin-bottom: 10px;"><?php h($adRow['title']); ?></div>

<div style="background-color: #f4f4f4; border: 1px solid #d5d5d5; padding: 10px;">
<?php if (is_file(dirname(__FILE__)."/../img/{$adRow['id']}")) { ?>
	<a href="<?php h("{$www_base}/img/{$adRow['id']}"); ?>" target="_blank">
		<img src="<?php h("{$www_base}/img/{$adRow['id']}"); ?>" style="max-width: 200px; height: auto; float: left; margin-right: 10px;" />
	</a>
<?php } ?>

<?php e(nl2br(t2h($adRow['body']))); ?>
<div style="clear:both;"></div>
</div>

<br />

<center>

<form method="get" action="<?php h("{$www_base}/ads/reply.php"); ?>" style="display:inline;">
<input type="hidden" name="id" value="<?php h($adRow['id']); ?>" />
<button type="submit">Reply</button>
</form>

&nbsp;&nbsp;

<form method="get" action="<?php h("{$www_base}/flag/"); ?>" style="display:inline;">
<input type="hidden" name="type" value="ad" />
<input type="hidden" name="id" value="<?php h($adRow['id']); ?>" />
<button type="submit">Flag post as inappropriate</button>
</form>

</center>

<br /><br />

<?php if (isset($_SESSION['admin'])) { ?>
	<form method="post" action="<?php h("{$www_base}/admin/ads/del.php"); ?>" style="display:inline;">
	<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
	<input type="hidden" name="ret" value="<?php h($_SERVER['REQUEST_URI']); ?>" />
	<input type="hidden" name="title" value="<?php h($adRow['title']); ?>" />
	<input type="hidden" name="id" value="<?php h($adRow['id']); ?>" />
	<button type="submit" class="delete">Delete</button>
	</form>
<?php } ?>

	</td>
	<td style="width:10px;">&nbsp;</td>
	<td align="center" valign="top" class="side-column">
		<?php include dirname(__FILE__)."/../banner.php"; ?>
	</td>
</tr>
</table>

<?php include dirname(__FILE__)."/../footer.php"; ?>

</body>
</html>