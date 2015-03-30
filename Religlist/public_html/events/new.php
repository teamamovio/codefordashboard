<?php

require_once dirname(__FILE__)."/../lib/config.php";
$churchInfo = getChurchInfo();

$f = array();
$f['from'] = (isset($_POST['from']) && is_string($_POST['from'])) ? mb_trim($_POST['from']) : '';
$f['to'] = (isset($_POST['to']) && is_string($_POST['to'])) ? mb_trim($_POST['to']) : '';
$f['email'] = (isset($_POST['email']) && is_string($_POST['email'])) ? mb_trim($_POST['email']) : '';
$f['title'] = (isset($_POST['title']) && is_string($_POST['title'])) ? mb_trim($_POST['title']) : '';
$f['body'] = (isset($_POST['body']) && is_string($_POST['body'])) ? mb_trim($_POST['body']) : '';

$errors = array();
if (isset($_POST['submitted'])) {
	
	if ($f['from'] == '') {
		$errors[] = "From field cannot be empty";
	} else {
		$from = preg_split('#\\s*/\\s*#Duis', $f['from']);
		if (count($from) != 3) {
			$errors[] = "Invalid format of 'from' field";
		} else {
			$fromYear = intval($from[0]);
			$fromMonth = intval($from[1]);
			$fromDay = intval($from[2]);
			if (!checkdate($fromMonth, $fromDay, $fromYear)) {
				$errors[] = "Invalid from date";
			}
		}
	}
	
	if ($f['to'] == '') {
		$f['to'] = $f['from'];
	}
	$to = preg_split('#\\s*/\\s*#Duis', $f['to']);
	if (count($to) != 3) {
		$errors[] = "Invalid format of 'to' field";
	} else {
		$toYear = intval($to[0]);
		$toMonth = intval($to[1]);
		$toDay = intval($to[2]);
		if (!checkdate($toMonth, $toDay, $toYear)) {
			$errors[] = "Invalid to date";
		}
	}
	
	if ($f['email'] == '') {
		$errors[] = "Email field cannot be empty";
	} elseif (mb_strlen($f['email']) > 255) {
		$errors[] = "Email field cannot contain more than 255 characters";
	} elseif (!preg_match('#^[^@]+@[^@]+$#Duis', $f['email'])) {
		$errors[] = "Email field has invalid format";
	}
	
	if ($f['title'] == '') {
		$errors[] = "Event title cannot be empty";
	} elseif (mb_strlen($f['title']) > 255) {
		$errors[] = "Event title cannot contain more than 255 characters";
	}
	
	if ($f['body'] == '') {
		$errors[] = "Event body cannot be empty";
	}
	
	if (count($errors) == 0) {
		mysql_query(
			"INSERT INTO events SET ".
				"church_id = '".mysql_real_escape_string($churchInfo['id'], $db)."',".
				"evt_from = '{$fromYear}/{$fromMonth}/{$fromDay}',".
				"evt_to = '{$toYear}/{$toMonth}/{$toDay}',".
				"email = '".mysql_real_escape_string($f['email'], $db)."',".
				"title = '".mysql_real_escape_string($f['title'], $db)."',".
				"body = '".mysql_real_escape_string($f['body'], $db)."',".
				"created_on = NOW()",
			$db
		) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
		
		$q = mysql_query(
			"SELECT LAST_INSERT_ID() AS lid",
			$db
		) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
		$row = mysql_fetch_assoc($q);
		mysql_free_result($q);
		$eventId = $row['lid'];
		
		if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
			move_uploaded_file($_FILES['image']['tmp_name'], dirname(__FILE__)."/../event_img/{$eventId}");
		}
		
		header("Location: {$www_base}/events/thanks.php?id={$eventId}");
		exit;
	}
	
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>post event</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

</head>
<body>

<div class="bread-crumb">
	<a href="<?php h("{$www_base}/churches.php"); ?>"><?php h($siteName); ?></a>
	&gt;
	<a href="<?php h("{$www_base}/"); ?>"><?php h($churchInfo['name']); ?></a>
	&gt;
	post event
</div>

<table border="0" align="center">
<tr>
	<td align="left" valign="top">

<div class="location">
	<span class="one"><?php h("{$churchInfo['stateRow']['name']} / {$churchInfo['cityRow']['name']}"); ?></span><br />
	<span class="two"><?php h($churchInfo['name']); ?></span>
</div>

<div>
	<form method="post" action="<?php h($_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data">
	<input type="hidden" name="csrf" value="<?php h($_SESSION['csrf']); ?>" />
	<input type="hidden" name="submitted" value="yes" />
	
	<?php if (count($errors) > 0) { ?>
		<font color="red"><ul><?php foreach ($errors as $err) e("<li>{$err}</li>"); ?></ul></font>
	<?php } ?>
	
	<div class="field">
		<b>From:</b>
		<input type="text" name="from" value="<?php h($f['from']); ?>" size="16" maxlength="10" />
		<span class="tip">(yyyy/mm/dd)</span>
	</div>
	
	<div class="field">
		<b>To:</b>
		<input type="text" name="to" value="<?php h($f['to']); ?>" size="16" maxlength="10" />
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
	
	<button type="submit">Post Event</button>
	
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