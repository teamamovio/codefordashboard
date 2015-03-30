<?php

require_once dirname(__FILE__)."/../lib/config.php";

if (!( isset($_GET['id']) && is_string($_GET['id']) && preg_match('#^\\d{1,20}$#Duis', $_GET['id']) )) {
	header("Location: {$www_base}/ads/");
	exit;
}
$eventId = $_GET['id'];

$q = mysql_query(
	"SELECT ".
		"events.*, churches.name AS churchName, ".
		"cities.name AS cityName, states.name AS stateName ".
	"FROM events ".
		"LEFT JOIN churches ON events.church_id = churches.id ".
		"LEFT JOIN cities ON churches.city_id = cities.id ".
		"LEFT JOIN states ON cities.state_id = states.id ".
	"WHERE events.id = '".mysql_real_escape_string($eventId, $db)."'",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
$eventRow = mysql_fetch_assoc($q);
mysql_free_result($q);

if ($eventRow === false) {
	header("Location: {$www_base}/events/");
	exit;
}

$eventRow['evt_from'] = preg_replace('#-#Duis', '/', $eventRow['evt_from']);
$eventRow['evt_to'] = preg_replace('#-#Duis', '/', $eventRow['evt_to']);

$eventRow['evt_from2'] = preg_replace('#[^0-9]#Duis', '', $eventRow['evt_from']);
$eventRow['evt_to2'] = preg_replace('#[^0-9]#Duis', '', $eventRow['evt_to']);

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title><?php h($eventRow['title']); ?></title>

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
	<?php if (!isset($_SESSION['churchId']) || $_SESSION['churchId'] != $eventRow['church_id']) { ?>
		<a href="<?php h("{$www_base}/?churchId={$eventRow['church_id']}"); ?>"><?php h($eventRow['churchName']); ?></a>
	<?php } else { ?>
		<a href="<?php h("{$www_base}/"); ?>"><?php h($eventRow['churchName']); ?></a>
	<?php } ?>
	&gt;
	<a href="<?php h("{$www_base}/events/?churchId={$eventRow['church_id']}&d={$eventRow['evt_from2']}"); ?>">
		events for <?php h($eventRow['evt_from']); ?>
	</a>
	&gt;
	<?php h($eventRow['title']); ?>
</div>

<table border="0" align="center">
<tr>
	<td align="left" valign="top" style="width: 650px;">

<div class="location">
	<span class="one"><?php h("{$eventRow['stateName']} / {$eventRow['cityName']}"); ?></span><br />
	<span class="two"><?php h($eventRow['churchName']); ?></span>
</div>

<div style="font-size: 20pt; text-align: center; margin-bottom: 10px;"><?php h($eventRow['title']); ?></div>
<div style="text-align: center; font-size: 16pt;">From <?php h($eventRow['evt_from']); ?> to <?php h($eventRow['evt_to']); ?></div>

<br />

<div style="background-color: #f4f4f4; border: 1px solid #d5d5d5; padding: 10px;">
<?php if (is_file(dirname(__FILE__)."/../event_img/{$eventRow['id']}")) { ?>
	<a href="<?php h("{$www_base}/event_img/{$eventRow['id']}"); ?>" target="_blank">
		<img src="<?php h("{$www_base}/event_img/{$eventRow['id']}"); ?>" style="max-width: 200px; height: auto; float: left; margin-right: 10px;" />
	</a>
<?php } ?>
<?php e(nl2br(t2h($eventRow['body']))); ?>
<div style="clear:both;"></div>
</div>

<br />

<center>
<form method="get" action="<?php h("{$www_base}/events/reply.php"); ?>">
<input type="hidden" name="id" value="<?php h($eventRow['id']); ?>" />
<button type="submit">Reply</button>
</form>
</center>

<br /><br />

<?php if (isset($_SESSION['admin'])) { ?>
	<form method="post" action="<?php h("{$www_base}/admin/event/del.php"); ?>" style="display:inline;">
	<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
	<input type="hidden" name="ret" value="<?php h($_SERVER['REQUEST_URI']); ?>" />
	<input type="hidden" name="title" value="<?php h($eventRow['title']); ?>" />
	<input type="hidden" name="id" value="<?php h($eventRow['id']); ?>" />
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