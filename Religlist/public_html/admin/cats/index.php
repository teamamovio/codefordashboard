<?php

require_once dirname(__FILE__)."/../chk_admin.php";

$parentCats = array();
$q = mysql_query(
	"SELECT * FROM cats ".
	"WHERE parent_id = 0 ".
	"ORDER BY name ASC",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
while ($row = mysql_fetch_assoc($q)) $parentCats[$row['id']] = $row;
mysql_free_result($q);
$parentCats_ids = array_keys($parentCats);

$defParentId = (isset($_GET['parent_id']) && is_string($_GET['parent_id']) && in_array($_GET['parent_id'], $parentCats_ids)) ? $_GET['parent_id'] : '0';

$f = array();
$f['name'] = (isset($_POST['name']) && is_string($_POST['name'])) ? mb_trim($_POST['name']) : '';
$f['parent_id'] = (isset($_POST['parent_id']) && is_string($_POST['parent_id'])) ? $_POST['parent_id'] : $defParentId;
$f['col'] = (isset($_POST['col']) && is_string($_POST['col'])) ? mb_trim($_POST['col']) : 1;
$f['row'] = (isset($_POST['row']) && is_string($_POST['row'])) ? mb_trim($_POST['row']) : 1;
$f['price'] = (isset($_POST['price']) && is_string($_POST['price'])) ? mb_trim($_POST['price']) : '0.00';

$errors = array();
if (isset($_POST['submitted'])) {
	if ($f['name'] == '') {
		$errors[] = "Category Name cannot be empty";
	} elseif (mb_strlen($f['name']) > 255) {
		$errors[] = "Category Name cannot contain more than 255 characters";
	} elseif ($f['name'] == 'events') {
		$errors[] = "'events' is special name and cannot be created";
	}
	
	if ($f['parent_id'] != '0') {
		if (!in_array($f['parent_id'], $parentCats_ids)) {
			$errors[] = "Specified Parent Category doesn't exist";
		}
	}
	
	if ($f['parent_id'] == '0') {
		if (!in_array($f['col'], array('1','2','3'))) {
			$errors[] = "Column must be 1, 2, or 3";
		}
		
		if (!preg_match('#^\\d{1,20}$#Duis', $f['row'])) {
			$errors[] = "Row must be integer";
		}
	}
	
	if (!preg_match('#^\\d+(?:\\.\\d+)?$#Duis', $f['price'])) {
		$errors[] = "Price should be decimal number";
	}
	
	if (count($errors) == 0) {
		mysql_query(
			"INSERT INTO cats SET ".
				"name = '".mysql_real_escape_string($f['name'], $db)."',".
				"parent_id = '".mysql_real_escape_string($f['parent_id'], $db)."',".
				"col = '".mysql_real_escape_string($f['col'], $db)."',".
				"row = '".mysql_real_escape_string($f['row'], $db)."',".
				"price = '".mysql_real_escape_string($f['price'], $db)."'",
			$db
		) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
		header("Location: {$www_base}/admin/cats/");
		exit;
	}
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Categories</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

<script type="text/javascript" src="<?php h("{$www_base}/jquery.js"); ?>"></script>
<script type="text/javascript">
$(document).ready(function () {
	$('[name="parent_id"]').on('change', function () {
		var parent_id = $(this).val();
		if (parent_id == 0) {
			$('#col').show();
			$('#row').show();
			$('#price').hide();
		} else {
			$('#col').hide();
			$('#row').hide();
			$('#price').show();
		}
	}).change();
});
</script>

</head>
<body>

<?php include dirname(__FILE__)."/../menu.php"; ?>
<h1 align="center">Categories</h1>

<form method="post" action="<?php h($_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
<input type="hidden" name="submitted" value="yes" />
<table border="0" align="center">
<?php if (count($errors) > 0) { ?>
	<tr>
		<td colspan="3" align="left" valign="top">
			<font color="red"><ul><?php foreach ($errors as $err) e("<li>{$err}</li>"); ?></ul></font>
		</td>
	</tr>
<?php } ?>
<tr>
	<td align="right" valign="top">
		<b>Name:</b>
	</td>
	<td width="5px">&nbsp;</td>
	<td align="left" valign="top">
		<input type="text" name="name" value="<?php h($f['name']); ?>" size="40" maxlength="255" />
	</td>
</tr>
<tr>
	<td align="right" valign="top">
		<b>Parent Category:</b>
	</td>
	<td width="5px">&nbsp;</td>
	<td align="left" valign="top">
		<select name="parent_id">
			<option value="0"<?php if ($f['parent_id'] == '0') e(" selected=\"selected\""); ?>>None</option>
			<?php foreach ($parentCats as $pc) { ?>
				<option value="<?php h($pc['id']); ?>"<?php if ($f['parent_id'] == $pc['id']) e(" selected=\"selected\""); ?>>
					<?php h($pc['name']); ?>
				</option>
			<?php } ?>
		</select>
	</td>
</tr>
<tr id="col">
	<td align="right" valign="top">
		<b>Column:</b>
	</td>
	<td width="5px">&nbsp;</td>
	<td align="left" valign="top">
		<input type="text" name="col" value="<?php h($f['col']); ?>" size="5" maxlength="1" />
	</td>
</tr>
<tr id="row">
	<td align="right" valign="top">
		<b>Row:</b>
	</td>
	<td width="5px">&nbsp;</td>
	<td align="left" valign="top">
		<input type="text" name="row" value="<?php h($f['row']); ?>" size="5" maxlength="20" />
	</td>
</tr>
<tr id="price">
	<td align="right" valign="top">
		<i>Price:</i>
	</td>
	<td width="5px">&nbsp;</td>
	<td align="left" valign="top">
		<input type="text" name="price" value="<?php h($f['price']); ?>" size="10" maxlength="6" />
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td align="left" valign="top">
		<button type="submit">Add Category</button>
	</td>
</tr>
</table>
</form>

<table border="0" align="center">
<tr>
	<?php for ($col = 1; $col <= 3; $col++) { ?>
		<td align="center" valign="top">
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
			<a href="<?php h("{$www_base}/admin/cats/edit.php?id={$rootCat['id']}"); ?>">
				<?php h($rootCat['name']); ?>
			</a>
		</div>
	</div>
	
	<?php foreach ($subCats as $subCat) { ?>
		<div class="norm-element">
			<a href="<?php h("{$www_base}/admin/cats/edit.php?id={$subCat['id']}"); ?>">
				<?php h($subCat['name']); ?>
			</a>
		</div>
	<?php } ?>
</div>

				<?php
			}
			?>
		</td>
		<?php if ($col != 3) { ?>
			<td width="20px">&nbsp;</td>
		<?php } ?>
	<?php } ?>
</tr>
</table>

</body>
</html>