<?php

require_once dirname(__FILE__)."/../chk_admin.php";

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

$f = array();
$f['html'] = (isset($_POST['html']) && is_string($_POST['html'])) ? mb_trim($_POST['html']) : '';

$f['churches'] = array();
if (isset($_POST['churches']) && is_array($_POST['churches'])) {
	foreach ($_POST['churches'] as $churchId => $val) {
		if (in_array($churchId, $churchIds) && !in_array($churchId, $f['churches'])) $f['churches'][] = $churchId;
	}
}

$errors = array();
if (isset($_POST['submitted'])) {
	
	if ($f['html'] == '') {
		$errors[] = "HTML Code field cannot be empty";
	}
	
	if (count($f['churches']) == 0) {
		$errors[] = "Select at least one church";
	}
	
	if (count($errors) == 0) {
		mysql_query(
			"INSERT INTO banners SET ".
				"html = '".mysql_real_escape_string($f['html'], $db)."'",
			$db
		) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
		
		$q = mysql_query(
			"SELECT LAST_INSERT_ID() AS lid",
			$db
		) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
		$row = mysql_fetch_assoc($q);
		mysql_free_result($q);
		$bannerId = $row['lid'];
		
		foreach ($f['churches'] as $churchId) {
			mysql_query(
				"INSERT INTO banners_churches SET ".
					"banner_id = '".mysql_real_escape_string($bannerId, $db)."',".
					"church_id = '".mysql_real_escape_string($churchId, $db)."'",
				$db
			) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
		}
		
		header("Location: {$www_base}/admin/banners/view.php?id={$bannerId}");
		exit;
	}
	
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Add Banner</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

</head>
<body>

<?php include dirname(__FILE__)."/../menu.php"; ?>
<h1 align="center">Add Banner</h1>

<form method="post" action="<?php h($_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
<input type="hidden" name="submitted" value="yes" />

<table border="0" align="center"><tr><td align="left" valign="top">

<?php if (count($errors) > 0) { ?>
	<font color="red"><ul><?php foreach ($errors as $err) e("<li>{$err}</li>"); ?></ul></font>
<?php } ?>

<b>HTML Code:</b><br />
<textarea name="html" cols="80" rows="16"><?php h($f['html']); ?></textarea>
<br />

<button type="submit">Add Banner</button>

</td></tr></table>

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
								<input type="checkbox" name="churches[<?php h($church['id']); ?>]"<?php if (in_array($church['id'], $f['churches'])) e(" checked=\"checked\""); ?> />
								<a href="<?php h("{$www_base}/admin/churches/edit.php?church_id={$church['id']}"); ?>" target="_blank">
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

</form>

</body>
</html>