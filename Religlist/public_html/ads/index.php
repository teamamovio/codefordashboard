<?php

require_once dirname(__FILE__)."/../lib/config.php";
$churchInfo = getChurchInfo();

$itemsPerPage = 100;

if (!( isset($_GET['cat']) && is_string($_GET['cat']) && preg_match('#^\\d{1,20}$#Duis', $_GET['cat']) )) {
	$_GET['cat'] = '0';
}
$catId = $_GET['cat'];

if ($catId != 0) {
	$q = mysql_query(
		"SELECT * FROM cats ".
		"WHERE id = '".mysql_real_escape_string($catId, $db)."'",
		$db
	) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
	$catRow = mysql_fetch_assoc($q);
	mysql_free_result($q);
	
	if ($catRow === false) {
		header("Location: {$www_base}/");
		exit;
	}
	
	$catSql = " AND (".
		"ads.cat_id = '".mysql_real_escape_string($catRow['id'], $db)."' OR ".
		"cats.parent_id = '".mysql_real_escape_string($catRow['id'], $db)."'".
	") ";
} else {
	$catSql = '';
}

$qSql = '';
if (isset($_GET['q']) && is_string($_GET['q'])) {
	$_GET['q'] = mb_trim($_GET['q']);
	if ($_GET['q'] != '') {
		$qSql = " AND (ads.title LIKE '%".mysql_real_escape_string(like($_GET['q']), $db)."%' OR ads.body LIKE '%".mysql_real_escape_string($_GET['q'], $db)."%') ";
	}
}

$q = mysql_query(
	"SELECT COUNT(*) AS totalItems ".
	"FROM ads LEFT JOIN cats ON ads.cat_id = cats.id ".
	"WHERE ".
		"ads.church_id = '".mysql_real_escape_string($churchInfo['id'], $db)."' {$catSql} {$qSql} ".
		" AND ads.is_visible = 'yes' ",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
$row = mysql_fetch_assoc($q);
mysql_free_result($q);
$totalItems = $row['totalItems'];

$totalPages = intval($totalItems / $itemsPerPage);
if ($totalItems % $itemsPerPage != 0) $totalPages++;

$page = (isset($_GET['page']) && is_string($_GET['page']) && preg_match('#^\\d{1,20}$#Duis', $_GET['page'])) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
if ($page > $totalPages) $page = 1;

$ads = array();
$q = mysql_query(
	"SELECT ads.* ".
	"FROM ads LEFT JOIN cats ON ads.cat_id = cats.id ".
	"WHERE ".
		"ads.church_id = '".mysql_real_escape_string($churchInfo['id'], $db)."' {$catSql} {$qSql} ".
		" AND ads.is_visible = 'yes' ".
	"ORDER BY created_on DESC ".
	"LIMIT ".(($page - 1)*$itemsPerPage).", {$itemsPerPage}",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
while ($ad = mysql_fetch_assoc($q)) $ads[] = $ad;
mysql_free_result($q);

function getToUrl($get, $vars) {
	$ret = array();
	
	foreach ($vars as $key => $val) $get[$key] = $val;
	
	foreach ($get as $key => $val) $ret[] = urlencode($key)."=".urlencode($val);
	$ret = join("&", $ret);
	
	return $ret;
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>
	<?php if (isset($_GET['q']) && is_string($_GET['q']) && $_GET['q'] != '') { ?>
		Search for <?php h($_GET['q']); ?>
	<?php } else { ?>
		<?php h($catId == 0 ? 'See All' : $catRow['name']); ?>
	<?php } ?>
</title>

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
	<?php if (isset($_GET['q']) && is_string($_GET['q']) && $_GET['q'] != '') { ?>
		Search for <?php h($_GET['q']); ?>
	<?php } else { ?>
		<?php h($catId == 0 ? 'See All' : $catRow['name']); ?>
	<?php } ?>
</div>

<center>
	<?php if ($page > 1) { ?>
		<a href="<?php h("{$www_base}/ads/?".getToUrl($_GET, array('page' => ($page - 1)))); ?>">Prev</a>
	<?php } ?>
	&nbsp;&nbsp;&nbsp;
	Page <?php h($page); ?> of <?php h($totalPages); ?>
	&nbsp;&nbsp;&nbsp;
	<?php if ($page < $totalPages) { ?>
		<a href="<?php h("{$www_base}/ads/?".getToUrl($_GET, array('page' => ($page + 1)))); ?>">Next</a>
	<?php } ?>
</center>
<br /><br />

<?php if (count($ads) == 0) { ?>
	<center><i>No ads here</i></center>
<?php } else { ?>
	<?php $prevDate = ''; ?>
	<?php foreach ($ads as $ad) { ?>
		<?php /* if (ut($ad['created_on'], "D M j") != $prevDate) { ?>
			<?php $prevDate = ut($ad['created_on'], "D M j"); ?>
			<div class="list-date">
				<?php u($ad['created_on'], "D M j"); ?>
			</div>
		<?php } */ ?>
		<div class="list-item">
			<i><b><?php u($ad['created_on'], "D M j"); ?></b></i> -
			<a href="<?php h("{$www_base}/ads/view.php?id={$ad['id']}"); ?>"><?php h($ad['title']); ?></a>
			
			<?php if (isset($_SESSION['admin'])) { ?>
				<form method="post" action="<?php h("{$www_base}/admin/ads/del.php"); ?>" style="display:inline;">
				<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
				<input type="hidden" name="ret" value="<?php h($_SERVER['REQUEST_URI']); ?>" />
				<input type="hidden" name="title" value="<?php h($ad['title']); ?>" />
				<input type="hidden" name="id" value="<?php h($ad['id']); ?>" />
				<button type="submit" class="delete">Delete</button>
				</form>
			<?php } ?>
			
		</div>
	<?php } ?>
<?php } ?>

<?php include dirname(__FILE__)."/../footer.php"; ?>

</body>
</html>
