<?php

require_once dirname(__FILE__)."/../chk_admin.php";

if (!( isset($_GET['church_id']) && is_string($_GET['church_id']) && preg_match('#^\\d{1,20}$#Duis', $_GET['church_id']) )) {
	header("Location: {$www_base}/admin/churches/");
	exit;
}
$churchId = $_GET['church_id'];

$q = mysql_query(
	"SELECT * FROM churches ".
	"WHERE id = '".mysql_real_escape_string($churchId, $db)."'",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
$churchRow = mysql_fetch_assoc($q);
mysql_free_result($q);

if ($churchRow === false) {
	header("Location: {$www_base}/admin/churches/");
	exit;
}



$ret = "{$www_base}/admin/churches/";
if (isset($_GET['ret']) && is_string($_GET['ret'])) $ret = $_GET['ret'];




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
$f['city_id'] = (isset($_POST['city_id']) && is_string($_POST['city_id'])) ? $_POST['city_id'] : $churchRow['city_id'];
$f['name'] = (isset($_POST['name']) && is_string($_POST['name'])) ? mb_trim($_POST['name']) : $churchRow['name'];

$errors = array();
if (isset($_POST['submitted'])) {
	if (isset($_POST['del'])) {
		
		mysql_query(
			"DELETE FROM churches ".
			"WHERE id = '".mysql_real_escape_string($churchId, $db)."'",
			$db
		) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
		
		header("Location: {$ret}");
		exit;
		
	} else {
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
				"UPDATE churches SET ".
					"city_id = '".mysql_real_escape_string($f['city_id'], $db)."',".
					"name = '".mysql_real_escape_string($f['name'], $db)."' ".
				"WHERE ".
					"id = '".mysql_real_escape_string($churchId, $db)."'",
				$db
			);
			
			if ($q === false) {
				$errors[] = "Church with specifed name already exists in ".$states[$cities[$f['city_id']]['state_id']]['name']." / ".$cities[$f['city_id']]['name'].".";
			} else {
				header("Location: {$ret}");
				exit;
			}
		}
	}
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title><?php h($churchRow['name']); ?></title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

<script type="text/javascript" src="<?php h("{$www_base}/jquery.js"); ?>"></script>
<script type="text/javascript">
$(document).ready(function () {
	$('[name="del"]').on('click', function () {
		return confirm('Delete church?');
	});
});
</script>

</head>
<body>

<?php include dirname(__FILE__)."/../menu.php"; ?>
<h1 align="center"><?php h($churchRow['name']); ?></h1>

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

<button type="submit">Update</button>

&nbsp;&nbsp;&nbsp;

<button type="submit" name="del" value="del">Delete</button>

</td></tr></table>
</form>

</body>
</html>