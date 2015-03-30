<?php

require_once dirname(__FILE__)."/../lib/config.php";
$churchInfo = getChurchInfo();

if (!isset($_GET['d'])) $_GET['d'] = date("Ymd");
if (!( is_string($_GET['d']) && preg_match('#^\\d{8,8}$#Duis', $_GET['d']) )) {
	header("Location: {$www_base}/events/");
	exit;
}

$year = intval(substr($_GET['d'], 0, 4));
$month = intval(substr($_GET['d'], 4, 2));
$day = intval(substr($_GET['d'], 6, 2));

if (!checkdate($month, $day, $year)) {
	header("Location: {$www_base}/events/");
	exit;
}

$events = array();
$q = mysql_query(
	"SELECT events.* ".
	"FROM events ".
	"WHERE ".
		"church_id = '".mysql_real_escape_string($churchInfo['id'], $db)."' AND ".
		"'{$year}-{$month}-{$day}' >= events.evt_from AND ".
		"'{$year}-{$month}-{$day}' <= events.evt_to ".
	"ORDER BY events.created_on DESC",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
while ($event = mysql_fetch_assoc($q)) $events[] = $event;
mysql_free_result($q);

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>events for <?php h("{$year}/{$month}/{$day}"); ?></title>

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
	<a href="<?php h("{$www_base}/?churchId={$churchInfo['id']}"); ?>"><?php h($churchInfo['name']); ?></a>
	&gt;
	events for <?php h("{$year}/{$month}/{$day}"); ?>
</div>

<?php if (count($events) == 0) { ?>
	<center><i>No events here</i></center>
<?php } else { ?>
	<?php foreach ($events as $event) { ?>
		<?php
		$event['evt_from'] = preg_replace('#-#Duis', '/', $event['evt_from']);
		$event['evt_to'] = preg_replace('#-#Duis', '/', $event['evt_to']);
		
		$from = preg_split('#/#Duis', $event['evt_from']);
		$to = preg_split('#/#Duis', $event['evt_to']);
		
		?>
		<div class="list-item">
			<?php h(intval($from[1])."/".intval($from[2])); ?>-<?php h(intval($to[1])."/".intval($to[2])); ?>:
			<a href="<?php h("{$www_base}/events/view.php?id={$event['id']}"); ?>"><?php h($event['title']); ?></a>
			
			<?php if (isset($_SESSION['admin'])) { ?>
				<form method="post" action="<?php h("{$www_base}/admin/events/del.php"); ?>" style="display:inline;">
				<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
				<input type="hidden" name="ret" value="<?php h($_SERVER['REQUEST_URI']); ?>" />
				<input type="hidden" name="title" value="<?php h($event['title']); ?>" />
				<input type="hidden" name="id" value="<?php h($event['id']); ?>" />
				<button type="submit" class="delete">Delete</button>
				</form>
			<?php } ?>
			
		</div>
	<?php } ?>
<?php } ?>

<?php include dirname(__FILE__)."/../footer.php"; ?>

</body>
</html>