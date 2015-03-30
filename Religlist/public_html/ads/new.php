<?php

require_once dirname(__FILE__)."/../lib/config.php";

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



$f = array();
$f['cat'] = (isset($_POST['cat']) && is_string($_POST['cat'])) ? mb_trim($_POST['cat']) : '';
$f['email'] = (isset($_POST['email']) && is_string($_POST['email'])) ? mb_trim($_POST['email']) : '';
$f['title'] = (isset($_POST['title']) && is_string($_POST['title'])) ? mb_trim($_POST['title']) : '';
$f['body'] = (isset($_POST['body']) && is_string($_POST['body'])) ? mb_trim($_POST['body']) : '';

$errors = array();
if (isset($_POST['submitted'])) {
	
	if (!in_array($f['cat'], $catIds)) {
		$errors[] = "Please select posting category from the list";
	}
	
	if ($f['email'] == '') {
		$errors[] = "Email field cannot be empty";
	} elseif (mb_strlen($f['email']) > 255) {
		$errors[] = "Email field cannot contain more than 255 characters";
	}
	
	if ($f['title'] == '') {
		$errors[] = "Title field cannot be empty";
	} elseif (mb_strlen($f['title']) > 255) {
		$errors[] = "Title field cannot contain more than 255 characters";
	}
	
	if ($f['body'] == '') {
		$errors[] = "Body field cannot be empty";
	}
	
	if (count($errors) == 0) {
		$is_visible = ($cats[$f['cat']]['price'] == 0) ? 'yes' : 'no';
		
		mysql_query(
			"INSERT INTO ads SET ".
				"church_id = '".mysql_real_escape_string($churchRow['id'], $db)."',".
				"cat_id = '".mysql_real_escape_string($f['cat'], $db)."',".
				"email = '".mysql_real_escape_string($f['email'], $db)."',".
				"title = '".mysql_real_escape_string($f['title'], $db)."',".
				"body = '".mysql_real_escape_string($f['body'], $db)."',".
				"is_visible = '{$is_visible}',".
				"created_on = NOW()",
			$db
		) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
		
		$q = mysql_query(
			"SELECT LAST_INSERT_ID() AS lid",
			$db
		) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
		$row = mysql_fetch_assoc($q);
		mysql_free_result($q);
		$adId = $row['lid'];
		
		if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
			move_uploaded_file($_FILES['image']['tmp_name'], dirname(__FILE__)."/../img/{$adId}");
		}
		
		if ($is_visible == 'no') {
			header("Location: {$www_base}/ads/pre.pay.php?id={$adId}");
			exit;
		}
		
		header("Location: {$www_base}/ads/thanks.php?id={$adId}");
		exit;
	}
	
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Post Ad</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

<script type="text/javascript" src="<?php h("{$www_base}/jquery.js"); ?>"></script>
<script type="text/javascript">
$(document).ready(function () {
	$('[name="cat"]').on('change', function () {
		if ($(this).val() == 'events') {
			$(this).parent().parent().attr('action', '<?php h("{$www_base}/events/new.php"); ?>');
			$('#from').show();
			$('#to').show();
		} else {
			$(this).parent().parent().attr('action', '<?php h($_SERVER['REQUEST_URI']); ?>');
			$('#from').hide();
			$('#to').hide();
		}
	}).change();
});
</script>

</head>
<body>

<div class="bread-crumb">
	<a href="<?php h("{$www_base}/churches.php"); ?>"><?php h($siteName); ?></a>
	&gt;
	<?php if (!isset($_SESSION['churchId']) || $_SESSION['churchId'] != $churchRow['id']) { ?>
		<a href="<?php h("{$www_base}/?churchId={$churchRow['id']}"); ?>"><?php h($churchRow['name']); ?></a>
	<?php } else { ?>
		<a href="<?php h("{$www_base}/"); ?>"><?php h($churchRow['name']); ?></a>
	<?php } ?>
	&gt;
	post ad
</div>

<table border="0" align="center">
<tr>
	<td align="left" valign="top">

<div class="location">
	<span class="one"><?php h("{$churchRow['stateRow']['name']} / {$churchRow['cityRow']['name']}"); ?></span><br />
	<span class="two"><?php h($churchRow['name']); ?></span>
</div>

<div>
	<form method="post" action="<?php h($_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data">
	<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
	<input type="hidden" name="submitted" value="yes" />
	
	<?php if (count($errors) > 0) { ?>
		<font color="red"><ul><?php foreach ($errors as $err) e("<li>{$err}</li>"); ?></ul></font>
	<?php } ?>
	
	<div class="field">
		<b>Category:</b><br />
		<select name="cat">
		<?php foreach ($superCats as $superCat) { ?>
			<optgroup label="<?php h($superCat['name']); ?>">
				<?php if ($superCat['name'] == 'community') { ?>
					<option value="events">events</option>
				<?php } ?>
				<?php foreach ($cats as $cat) { ?>
					<?php if ($cat['parent_id'] == $superCat['id']) { ?>
						<option value="<?php h($cat['id']); ?>"<?php if ($cat['id'] == $f['cat']) e(" selected=\"selected\""); ?>>
							<?php h($cat['name']); ?>
						</option>
					<?php } ?>
				<?php } ?>
			</optgroup>
		<?php } ?>
		</select>
	</div>
	
	<div class="field" id="from">
		<b>From:</b>
		<input type="text" name="from" value="" size="16" maxlength="10" />
		<span class="tip">(yyyy/mm/dd)</span>
	</div>
	
	<div class="field" id="to">
		<b>To:</b>
		<input type="text" name="to" value="" size="16" maxlength="10" />
		<span class="tip">(yyyy/mm/dd)</span>
	</div>
	
	<div class="field">
		<b>Your Email:</b><br />
		<input type="text" name="email" value="<?php h($f['email']); ?>" style="width: 100%;" maxlength="255" />
	</div>
	
	<div class="field">
		<b>Title:</b><br />
		<input type="text" name="title" value="<?php h($f['title']); ?>" style="width: 100%;" maxlength="255" />
	</div>
	
	<div class="field">
		<b>Type your post:</b><br />
		<textarea name="body" style="width: 100%;" rows="16"><?php h($f['body']); ?></textarea>
	</div>
	
	<div class="field">
		<i>Image (optional):</i>
		<input type="file" name="image" />
	</div>
	
	<button type="submit">Post Ad</button>
	
	</form>
</div>

	</td>
	<td style="width:10px;">&nbsp;</td>
	<td align="center" valign="top" class="side-column">
		<?php include dirname(__FILE__)."/../banner.php"; ?>
	</td>
</tr>
</table>

<?php include dirname(__FILE__)."/../footer.php"; ?>

</body>
</html>