<?php

require_once dirname(__FILE__)."/lib/config.php";

$churchRow = getChurchInfo();

$superCats = array();
$q = mysql_query(
	"SELECT * FROM cats WHERE parent_id = 0 ORDER BY name ASC",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
while ($cat = mysql_fetch_assoc($q)) $superCats[$cat['id']] = $cat;
mysql_free_result($q);

$cats = array();
$q = mysql_query(
	"SELECT * FROM cats WHERE parent_id <> 0 ORDER BY name ASC",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
while ($cat = mysql_fetch_assoc($q)) $cats[$cat['id']] = $cat;
mysql_free_result($q);
$catIds = array_keys($cats);

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Religlist</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-49842306-1', 'religlist.com');
ga('require', 'displayfeatures');
  ga('send', 'pageview');

</script>
</head>
<body>
<?php include_once("analyticstracking.php") ?>
<table border="0" align="center">
<tr>
	<td align="center" valign="top" class="side-column">
		<div>
			<a href="<?php h("{$www_base}/churches.php"); ?>">
				<img src="<?php h("{$www_base}/logo.png"); ?>" />
			</a>
		</div>
		<div>
			<?php
			$postUrl = "{$www_base}/ads/new.php";
			if (!isset($_SESSION['churchId']) || $_SESSION['churchId'] != $churchRow['id']) {
				$postUrl .= "?churchId={$churchRow['id']}";
			}
			?>
			<br />
			<a href="<?php h($postUrl); ?>">Create A Post</a><br /><br />
			<a href="<?php h("{$www_base}/churches.php"); ?>">Change Church</a><br /><br />
			<a href="<?php h("{$www_base}/ads/?cat=0"); ?>">See All Posts</a>
			
			<div style="margin-top: 20px;">
				<form method="get" action="<?php h("{$www_base}/ads/index.php"); ?>">
				<input type="text" name="q" value="" style="width: 160px; border: 1px solid #adadad; font-weight: bold;" /><br />
				<select name="cat" style="background-color: white; border: 1px solid #adadad; width: 137px; font-weight: bold;">
					<option value="0">All</option>
					<?php foreach ($superCats as $superCat) { ?>
						<optgroup label="<?php h($superCat['name']); ?>">
							<?php foreach ($cats as $cat) { ?>
								<?php if ($cat['parent_id'] == $superCat['id']) { ?>
									<option value="<?php h($cat['id']); ?>"><?php h($cat['name']); ?></option>
								<?php } ?>
							<?php } ?>
						</optgroup>
					<?php } ?>
				</select>
				<button type="submit" style="background-color: #f7921e; border: 1px solid #adadad; color: white; font-weight: bold; width: 20px;">&gt;</button>
				</form>
			</div>
			
			<br /><br />
			
			<div><a href="<?php h("{$www_base}/events/?churchId={$churchRow['id']}"); ?>">event calendar</a></div>
			<?php
			$tm = time();
			$dayOfWeek = date("D", $tm);
			if ($dayOfWeek == "Sun") {
				$startTm = $tm;
			} elseif ($dayOfWeek == "Mon") {
				$startTm = $tm - 24*3600;
			} elseif ($dayOfWeek == "Tue") {
				$startTm = $tm - 24*3600 * 2;
			} elseif ($dayOfWeek == "Wed") {
				$startTm = $tm - 24*3600 * 3;
			} elseif ($dayOfWeek == "Thu") {
				$startTm = $tm - 24*3600 * 4;
			} elseif ($dayOfWeek == "Fri") {
				$startTm = $tm - 24*3600 * 5;
			} elseif ($dayOfWeek == "Sat") {
				$startTm = $tm - 24*3600 * 6;
			}
			?>
			<table class="events">
				<tr>
					<th>S</th>
					<th>M</th>
					<th>T</th>
					<th>W</th>
					<th>T</th>
					<th>F</th>
					<th>S</th>
				</tr>
				<?php for ($row = 0; $row < 4; $row++) { ?>
					<tr>
						<?php for ($col = 0; $col < 7; $col++) { ?>
							<td>
								<a href="<?php h("{$www_base}/events/?d=".date('Ymd', $startTm + 24*3600 * ($row * 7 + $col))."&churchId={$churchRow['id']}"); ?>">
									<?php h(date('j', $startTm + 24*3600 * ($row * 7 + $col))); ?>
								</a>
							</td>
						<?php } ?>
					</tr>
				<?php } ?>
			</table>
			
			<br />
			
			<a href="<?php h("{$www_base}/events/new.php?churchId={$churchRow['id']}"); ?>">post event</a>
			
		</div>
	</td>
	<td style="width:10px;">&nbsp;</td>
	<td align="left" valign="top">

<div class="location">
	<span class="one"><?php h("{$churchRow['stateRow']['name']} / {$churchRow['cityRow']['name']}"); ?></span><br />
	<span class="two"><?php h($churchRow['name']); ?></span>
</div>

<table border="0" align="center">
<tr>
	<?php for ($col = 1; $col <= 3; $col++) { ?>
		<td align="center" valign="top" width="33%">
			<?php
			$rootCats = array();
			$q = mysql_query(
				"SELECT * FROM cats ".
				"WHERE ".
					"parent_id = 0 AND ".
					"col = {$col} ".
				"ORDER BY row ASC",
				$db
			) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
			while ($row = mysql_fetch_assoc($q)) $rootCats[] = $row;
			mysql_free_result($q);
			
			foreach ($rootCats as $rootCat) {
				$subCats = array();
				$q = mysql_query(
					"SELECT * FROM cats ".
					"WHERE parent_id = '".mysql_real_escape_string($rootCat['id'], $db)."' ".
					"ORDER BY name ASC",
					$db
				) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
				while ($row = mysql_fetch_assoc($q)) $subCats[] = $row;
				mysql_free_result($q);
				?>

<div class="listing-block">
	
	<div class="super-box">
		<div class="bg1"></div><div class="bg2"></div>
		<div class="text">
			<a href="<?php h("{$www_base}/ads/?churchId={$churchRow['id']}&cat={$rootCat['id']}"); ?>">
				<?php h($rootCat['name']); ?>
			</a>
		</div>
	</div>
	
	<?php if ($rootCat['name'] == 'community') { ?>
		<div class="norm-element">
			<a href="<?php h("{$www_base}/events/?churchId={$churchRow['id']}"); ?>">
				events
			</a>
		</div>
	<?php } ?>
	
	<?php foreach ($subCats as $subCat) { ?>
		<div class="norm-element">
			<a href="<?php h("{$www_base}/ads/?churchId={$churchRow['id']}&cat={$subCat['id']}"); ?>">
				<?php h($subCat['name']); ?>
			</a>
		</div>
	<?php } ?>
</div>

				<?php
			}
			?>
		</td>
	<?php } ?>
</tr>
</table>

<?php include dirname(__FILE__)."/footer.php"; ?>

	</td>
	<td style="width:10px;">&nbsp;</td>
	<td align="center" valign="top" class="side-column">
		<?php include dirname(__FILE__)."/banner.php"; ?>
	</td>
</tr>
</table>

</body>
</html>