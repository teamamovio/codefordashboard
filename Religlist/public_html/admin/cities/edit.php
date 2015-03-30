<?php

require_once dirname(__FILE__)."/../chk_admin.php";

if (!( isset($_GET['city_id']) && is_string($_GET['city_id']) && preg_match('#^\\d{1,20}$#Duis', $_GET['city_id']) )) {
	header("Location: {$www_base}/admin/cities/");
	exit;
}
$cityId = $_GET['city_id'];

$q = mysql_query(
	"SELECT * FROM cities ".
	"WHERE id = '".mysql_real_escape_string($cityId, $db)."'",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
$cityRow = mysql_fetch_assoc($q);
mysql_free_result($q);
if ($cityRow === false) {
	header("Location: {$www_base}/admin/cities/");
	exit;
}

$states = array();
$q = mysql_query(
	"SELECT * FROM states ".
	"ORDER BY name ASC",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
while ($state = mysql_fetch_assoc($q)) $states[$state['id']] = $state;
mysql_free_result($q);
$stateIds = array_keys($states);


require_once dirname(__FILE__)."/../../lib/forms/admin/cities/EditCityForm.php";
$formStates = array();
foreach ($states as $state) $formStates[$state['id']] = $state['name'];
$editCity = new EditCityForm('editCity', $formStates, $cityRow);
$editCity->run();

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Edit city of '<?php h($cityRow['name']); ?>'</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

</head>
<body>

<?php include dirname(__FILE__)."/../menu.php"; ?>
<h1 align="center">Edit city of '<?php h($cityRow['name']); ?>'</h1>

<form method="post" action="<?php h($_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
<input type="hidden" name="submitted" value="editCity" />
<table border="0" align="center"><tr><td align="left" valign="top">

<?php $editCity->displayField('name'); ?>
<?php $editCity->displayField('state_id'); ?>

<button type="submit">Save</button>

</td></tr></table>
</form>

</body>
</html>