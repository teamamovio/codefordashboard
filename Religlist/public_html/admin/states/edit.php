<?php

require_once dirname(__FILE__)."/../chk_admin.php";

if (!( isset($_GET['state_id']) && is_string($_GET['state_id']) && preg_match('#^\\d{1,20}$#Duis', $_GET['state_id']) )) {
	header("Location: {$www_base}/admin/states/");
	exit;
}
$stateId = $_GET['state_id'];

$q = mysql_query(
	"SELECT * FROM states ".
	"WHERE id = '".mysql_real_escape_string($stateId, $db)."'",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
$stateRow = mysql_fetch_assoc($q);
mysql_free_result($q);

if ($stateRow === false) {
	header("Location: {$www_base}/admin/states/");
	exit;
}

require_once dirname(__FILE__)."/../../lib/forms/admin/states/EditStateForm.php";
$esf = new EditStateForm('esf', $stateRow);
$esf->run();


$stateCities = array();
$q = mysql_query(
	"SELECT * FROM cities ".
	"WHERE state_id = '".mysql_real_escape_string($stateId, $db)."' ".
	"ORDER BY name ASC",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
while ($city = mysql_fetch_assoc($q)) $stateCities[$city['id']] = $city;
mysql_free_result($q);

require_once dirname(__FILE__)."/../../lib/forms/admin/cities/NewCityForm.php";
class NewCityForm2 extends NewCityForm {
	public function __construct($formName, $states) {
		$this->conf['fields']['state_id']['html'] = 'input.hidden';
		
		global $stateId;
		$this->conf['fields']['state_id']['def'] = $stateId;
		
		parent::__construct($formName, $states);
	}
}
$newCity = new NewCityForm2('newCity', array($stateRow['id'] => $stateRow['name']));
$newCity->run();

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Edit state '<?php h($stateRow['name']); ?>'</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

<script type="text/javascript" src="<?php h("{$www_base}/jquery.js"); ?>"></script>
<script type="text/javascript">
$(document).ready(function () {
	$('[name="del"]').on('click', function () {
		return confirm('Delete ' + $(this).parent().parent().parent().find('.name').text().trim() + '?');
	});
});
</script>

</head>
<body>

<?php include dirname(__FILE__)."/../menu.php"; ?>
<h1 align="center">Edit state '<?php h($stateRow['name']); ?>'</h1>

<form method="post" action="<?php h($_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
<input type="hidden" name="submitted" value="esf" />
<table border="0" align="center"><tr><td align="left" valign="top">

<?php $esf->displayField('name'); ?>

<button type="submit">Save</button>

</td></tr></table>
</form>

<h2 align="center"><?php h($stateRow['name']); ?>'s cities</h2>

<form method="post" action="<?php h($_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
<input type="hidden" name="submitted" value="newCity" />
<table border="0" align="center"><tr><td align="left" valign="top">

<?php $newCity->displayField('name'); ?>
<?php $newCity->displayField('state_id'); ?>

<button type="submit">Add City</button>

</td></tr></table>
</form>

<?php if (count($stateCities) == 0) { ?>
	<center><i>no cities</i></center>
<?php } else { ?>
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
				<a href="<?php h("{$www_base}/admin/cities/edit.php?city_id={$city['id']}&ret=".urlencode($_SERVER['REQUEST_URI'])); ?>">
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

</body>
</html>