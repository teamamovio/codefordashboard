<?php

require_once dirname(__FILE__)."/../../lib/config.php";

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Terms of use</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

</head>
<body>

<div class="bread-crumb">
	<a href="<?php h("{$www_base}/"); ?>"><?php h($siteName); ?></a>
	&gt;
	Terms of use
</div>

<table border="0" align="center">
<tr>
	<td align="left" valign="top" style="width: 650px; padding: 10px;">

<p align="justify">
<?php
$q = mysql_query(
	"SELECT sval FROM static WHERE skey = 'terms'",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
$row = mysql_fetch_assoc($q);
mysql_free_result($q);
e(nl2br($row['sval']));
?>
</p>

<br /><br />
<?php include dirname(__FILE__)."/../../footer.php"; ?>

	</td>
	<td style="width:10px;">&nbsp;</td>
	<td align="center" valign="top" class="side-column">
		<?php include dirname(__FILE__)."/../../banner.php"; ?>
	</td>
</tr>
</table>

</body>
</html>