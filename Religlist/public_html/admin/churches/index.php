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
$f['city_id'] = (isset($_POST['city_id']) && is_string($_POST['city_id'])) ? $_POST['city_id'] : '';
$f['name'] = (isset($_POST['name']) && is_string($_POST['name'])) ? mb_trim($_POST['name']) : '';

$errors = array();
if (isset($_POST['submitted'])) {
	if (!in_array($f['city_id'], $cityIds)) {
		$errors[] = "Please select city from the list";
	}
	
	if ($f['name'] == '') {
		$errors[] = "Church Name cannot be empty";
	} elseif (mb_strlen($f['name']) > 255) {
		$errors[] = "Church Name cannot contain more than 255 characters";
	}
	
	if (count($errors) == 0) {
		$q = @ mysql_query(
			"INSERT INTO churches SET ".
				"city_id = '".mysql_real_escape_string($f['city_id'], $db)."',".
				"name = '".mysql_real_escape_string($f['name'], $db)."'",
			$db
		);
		
		if ($q === false) {
			$errors[] = "Church with specifed name already exists in ".$states[$cities[$f['city_id']]['state_id']]['name']." / ".$cities[$f['city_id']]['name'].".";
		} else {
			header("Location: {$_SERVER['REQUEST_URI']}");
			exit;
		}
	}
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Churches</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

</head>
<body>

<?php include dirname(__FILE__)."/../menu.php"; ?>
<h1 align="center">Churches</h1>

<form method="post" action="<?php h($_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
<input type="hidden" name="submitted" value="yes" />
<table border="0" align="center"><tr><td align="left" valign="top">

<?php if (count($errors) > 0) { ?>
	<font color="red"><ul><?php foreach ($errors as $err) e("<li>{$err}</li>"); ?></ul></font>
<?php } ?>

<input type="text" name="name" value="<?php h($f['name']); ?>" size="40" maxlength="255" />

<select name="city_id">
<?php foreach ($states as $state) { ?>
	<?php if (count($state['cities']) > 0) { ?>
		<optgroup label="<?php h($state['name']); ?>">
		<?php foreach ($state['cities'] as $city) { ?>
			<option value="<?php h($city['id']); ?>"<?php if ($city['id'] == $f['city_id']) e(" selected=\"selected\""); ?>>
				<?php h($city['name']); ?>
			</option>
		<?php } ?>
		</optgroup>
	<?php } ?>
<?php } ?>
</select>

<button type="submit">Add Church</button>

</td></tr></table>
</form>

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
								<a href="<?php h("{$www_base}/admin/churches/edit.php?church_id={$church['id']}"); ?>">
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

</body>
</html>