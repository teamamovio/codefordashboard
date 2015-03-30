<?php

require_once dirname(__FILE__)."/../chk_admin.php";

$states = array();
$q = mysql_query(
	"SELECT * FROM states ".
	"ORDER BY name ASC",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
while ($state = mysql_fetch_assoc($q)) $states[$state['id']] = $state;
mysql_free_result($q);
$stateIds = array_keys($states);

$cities = array();
foreach ($states as $state) $cities[$state['id']] = array();

$q = mysql_query(
	"SELECT * FROM cities ORDER BY name ASC",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);

while ($city = mysql_fetch_assoc($q)) {
	if (isset($cities[$city['state_id']])) {
		$cities[$city['state_id']][$city['id']] = $city;
	}
}
mysql_free_result($q);

require_once dirname(__FILE__)."/../../lib/forms/admin/cities/NewCityForm.php";
$formStates = array();
foreach ($states as $state) $formStates[$state['id']] = $state['name'];
$newCity = new NewCityForm('newCity', $formStates);
$newCity->run();

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Cities</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

<script type="text/javascript" src="<?php h("{$www_base}/jquery.js"); ?>"></script>
<script type="text/javascript">
$(document).ready(function () {
	$('[name="del"]').on('click', function () {
		return confirm("Delete " + $(this).parent().parent().parent().find('.name').text().trim() + "?");
	});
});
</script>

</head>
<body>

<?php include dirname(__FILE__)."/../menu.php"; ?>
<h1 align="center">Cities</h1>

<form method="post" action="<?php h($_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
<input type="hidden" name="submitted" value="newCity" />
<table border="0" align="center"><tr><td align="left" valign="top">

<?php $newCity->displayField('name'); ?>

<?php $newCity->displayField('state_id'); ?>

<button type="submit">Add City</button>

</td></tr></table>
</form>
<br />

<?php foreach ($cities as $stateId => $stateCities) { ?>
	<?php if (count($stateCities) > 0) { ?>
		<h2 align="center"><?php h($states[$stateId]['name']); ?></h2>
		<table border="1" align="center">
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>&nbsp;</th>
		</tr>
		<?php foreach ($stateCities as $city) { ?>
			<tr>
				<td><?php h($city['id']); ?></td>
				<td class="name">
					<a href="<?php h("{$www_base}/admin/cities/edit.php?city_id={$city['id']}"); ?>">
						<?php h($city['name']); ?>
					</a>
				</td>
				<td>
					
					<form method="post" action="<?php h("{$www_base}/admin/cities/del.php"); ?>" style="display:inline;">
					<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
					<input type="hidden" name="city_id" value="<?php h($city['id']); ?>" />
					<input type="hidden" name="ret" value="<?php h($_SERVER['REQUEST_URI']); ?>" />
					<button type="submit" name="del">Delete</button>
					</form>
					
				</td>
			</tr>
		<?php } ?>
		</table>
	<?php } ?>
<?php } ?>

</body>
</html>