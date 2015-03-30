<?php

require_once dirname(__FILE__)."/../chk_admin.php";

if (!( isset($_GET['id']) && is_string($_GET['id']) && preg_match('#^\\d{1,20}$#Duis', $_GET['id']) )) {
	header("Location: {$www_base}/admin/cats/");
	exit;
}
$catId = $_GET['id'];

$q = mysql_query(
	"SELECT * FROM cats ".
	"WHERE id = '".mysql_real_escape_string($catId, $db)."'",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
$catRow = mysql_fetch_assoc($q);
mysql_free_result($q);

if ($catRow === false) {
	header("Location: {$www_base}/admin/cats/");
	exit;
}

$parentCats = array();
$q = mysql_query(
	"SELECT * FROM cats ".
	"WHERE ".
		"parent_id = 0 AND ".
		"id <> '".mysql_real_escape_string($catId, $db)."' ".
	"ORDER BY name ASC",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
while ($row = mysql_fetch_assoc($q)) $parentCats[$row['id']] = $row;
mysql_free_result($q);
$parentCats_ids = array_keys($parentCats);

$f = array();
$f['name'] = (isset($_POST['name']) && is_string($_POST['name'])) ? mb_trim($_POST['name']) : $catRow['name'];
$f['parent_id'] = (isset($_POST['parent_id']) && is_string($_POST['parent_id'])) ? $_POST['parent_id'] : $catRow['parent_id'];
$f['col'] = (isset($_POST['col']) && is_string($_POST['col'])) ? mb_trim($_POST['col']) : $catRow['col'];
$f['row'] = (isset($_POST['row']) && is_string($_POST['row'])) ? mb_trim($_POST['row']) : $catRow['row'];
$f['price'] = (isset($_POST['price']) && is_string($_POST['price'])) ? mb_trim($_POST['price']) : $catRow['price'];

$errors = array();
if (isset($_POST['submitted'])) {
	if (isset($_POST['delete'])) {
		mysql_query(
			"DELETE FROM cats ".
			"WHERE ".
				"id = '".mysql_real_escape_string($catId, $db)."' OR ".
				"parent_id = '".mysql_real_escape_string($catId, $db)."'",
			$db
		) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
		header("Location: {$www_base}/admin/cats/");
		exit;
	} else {
		if ($f['name'] == '') {
			$errors[] = "Category name cannot be empty";
		} elseif (mb_strlen($f['name']) > 255) {
			$errors[] = "Category name cannot contain more than 255 characters";
		} elseif ($f['name'] == 'events') {
			$errors[] = "'events' is special name and cannot be created";
		}
		
		if ($f['parent_id'] != '0') {
			if (!in_array($f['parent_id'], $parentCats_ids)) {
				$errors[] = "Specified parent category doesn't exist";
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
				"UPDATE cats SET ".
					"name = '".mysql_real_escape_string($f['name'], $db)."',".
					"parent_id = '".mysql_real_escape_string($f['parent_id'], $db)."' ".
				"WHERE id = '".mysql_real_escape_string($catId, $db)."'",
				$db
			) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
			
			if ($f['parent_id'] == '0') {
				mysql_query(
					"UPDATE cats SET ".
						"col = '".mysql_real_escape_string($f['col'], $db)."',".
						"row = '".mysql_real_escape_string($f['row'], $db)."' ".
					"WHERE id = '".mysql_real_escape_string($catId, $db)."'",
					$db
				) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
			} else {
				mysql_query(
					"UPDATE cats SET ".
						"price = '".mysql_real_escape_string($f['price'], $db)."' ".
					"WHERE id = '".mysql_real_escape_string($catId, $db)."'",
					$db
				) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
			}
			
			header("Location: {$www_base}/admin/cats/");
			exit;
		}
	}
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Category "<?php h($catRow['name']); ?>"</title>

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
	
	<?php if ($catRow['parent_id'] == 0) { ?>
		$('#add_sub').on('click', function () {
			window.location.href = "<?php h("{$www_base}/admin/cats/new.php?parent_id={$catId}"); ?>";
		});
	<?php } ?>
});
</script>

</head>
<body>

<?php include dirname(__FILE__)."/../menu.php"; ?>
<h1 align="center">Category "<?php h($catRow['name']); ?>"</h1>

<form method="post" action="<?php h($_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
<input type="hidden" name="submitted" value="yes" />
<table border="0" align="center">
<?php if (count($errors) > 0) { ?>
	<tr>
		<td align="left" valign="top" colspan="3">
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
		<b>Parent Location:</b>
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
		<button type="submit">Save</button>
		<button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this category?');">Delete</button>
		<?php if ($catRow['parent_id'] == 0) { ?>
			<button type="button" id="add_sub">Add Subcategory</button>
		<?php } ?>
	</td>
</tr>
</table>
</form>

</body>
</html>