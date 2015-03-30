<?php

require_once dirname(__FILE__)."/lib/config.php";

$ret = "{$www_base}/";
if ( isset($_GET['ret']) && is_string($_GET['ret']) ) $ret = $_GET['ret'];

$states = array();
$q = mysql_query(
	"SELECT * FROM states ORDER BY name ASC",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
while ($state = mysql_fetch_assoc($q)) {
	$state['cities'] = array();
	$states[$state['id']] = $state;
}
mysql_free_result($q);
$stateIds = array_keys($states);

$cities = array();
$q = mysql_query(
	"SELECT * FROM cities ORDER BY name ASC",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
while ($city = mysql_fetch_assoc($q)) {
	if (in_array($city['state_id'], $stateIds)) {
		$cities[$city['id']] = $city;
		
		$city['churches'] = array();
		$states[$city['state_id']]['cities'][$city['id']] = $city;
	}
}
mysql_free_result($q);
$cityIds = array_keys($cities);

$churches = array();
$q = mysql_query(
	"SELECT * FROM churches ORDER BY name ASC",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
while ($church = mysql_fetch_assoc($q)) {
	if (in_array($church['city_id'], $cityIds)) {
		$churches[$church['id']] = $church;
		
		$states[$cities[$church['city_id']]['state_id']]['cities'][$church['city_id']]['churches'][] = $church;
	}
}
mysql_free_result($q);
$churchIds = array_keys($churches);

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Select Your Church</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

</head>
<body>

<center><a href="<?php h("{$www_base}/churches.php"); ?>"><img src="<?php h("{$www_base}/logo2.png"); ?>" /></a></center>

<h1 align="center">Select Your Church</h1>

<table border="0" align="center">
<?php
foreach ($states as $state) {
	if (count($state['cities']) == 0) continue;
	
	$containsChurches = false;
	foreach ($state['cities'] as $city) {
		if (count($city['churches']) > 0) {
			$containsChurches = true;
			break;
		}
	}
	if (!$containsChurches) continue;
	
	?>
	
	<tr>
		<td align="left" valign="top" colspan="4">
			<div class="state"><?php h($state['name']); ?></div>
		</td>
	</tr>
	<?php for ($col = 0; $col < 4; $col++) { ?>
		<td align="left" valign="top" style="width: 200px;">
			<?php
			$stateCities = array();
			foreach ($state['cities'] as $city) {
				if (count($city['churches']) == 0) continue;
				$stateCities[] = $city;
			}
			
			$i = 0;
			foreach ($stateCities as $city) {
				if ($i % 4 == $col) {
					?>
					
					<div class="location-block">
						<div class="city"><?php h($city['name']); ?></div>
						
						<?php foreach ($city['churches'] as $church) { ?>
							<div class="church">
								<a href="<?php h("{$www_base}/set_church.php?church_id={$church['id']}&ret=".urlencode($ret)); ?>">
									<?php h($church['name']); ?>
								</a>
							</div>
						<?php } ?>
					</div>
					
					<?php
				}
				
				$i++;
			}
			?>
		</td>
	<?php } ?>
	</tr>
	
	<?php
}
?>
</table>

<?php include dirname(__FILE__)."/footer.php"; ?>

</body>
</html>