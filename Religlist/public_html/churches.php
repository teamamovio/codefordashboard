<?php

require_once dirname(__FILE__)."/lib/config.php";

$f = array();
$f['q'] = (isset($_GET['q']) && is_string($_GET['q'])) ? mb_trim($_GET['q']) : '';

$q = preg_split('#[^a-z]+#Duis', $f['q'], -1, PREG_SPLIT_NO_EMPTY);

$churches = array();
$qSql = array();
foreach ($q as $x) {
	$qSql[] = " churches.name LIKE '%".mysql_real_escape_string(like($x), $db)."%' ";
	$qSql[] = " states.name LIKE '%".mysql_real_escape_string(like($x), $db)."%' ";
	$qSql[] = " cities.name LIKE '%".mysql_real_escape_string(like($x), $db)."%' ";
}
if (count($qSql) > 0) {
	$qSql = "(".join(" OR ", $qSql).")";
	$q = mysql_query(
		"SELECT ".
			"churches.*, states.name AS stateName, cities.name AS cityName ".
		"FROM ".
			"churches ".
			"LEFT JOIN cities ON churches.city_id = cities.id ".
			"LEFT JOIN states ON cities.state_id = states.id ".
		"WHERE {$qSql} ".
		"ORDER BY churches.name ASC",
		$db
	) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);
	while ($row = mysql_fetch_assoc($q)) $churches[] = $row;
	mysql_free_result($q);
}

if (isset($_GET['suggest']) && $f['q'] != '') {
	header("Location: {$www_base}/suggest.php?q=".urlencode($f['q']));
	exit;
}

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Find your church</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

</head>
<body>

<br /><br />

<table border="0" align="center">
<tr>
	<td align="left" valign="top" width="800px">
		
		<div style="text-align:center;"><img src="<?php h("{$www_base}/logo2.png"); ?>" /></div>
		
		<a style="color: #f7921e; font-size: 1.2em; font-weight: bold; text-decoration: underline;" href="<?php h("{$www_base}/all.php"); ?>">View Full Catalog</a>
		<br />
		
		<form method="get" action="<?php h($_SERVER['PHP_SELF']); ?>">
		<input type="text" name="q" value="<?php h($f['q']); ?>" style="width:600px; height:38px; border: 1px solid #bbbdbf; font-size: 1.4em; background-image: url('<?php h("{$www_base}/search.png"); ?>'); background-repeat: no-repeat; background-position: 5px center; padding-left: 60px;" placeholder="Find your church" />
		<button type="submit" style="background-color: #f7921e; border: 1px solid #bbbdbf; height:42px; color: white;  font-size: 1.2em;">Search</button><br />
		<button type="submit" name="suggest" style="background-color: #f7921e; border: 1px solid #bbbdbf; height:42px; color: white;  font-size: 1.2em; margin-top: 10px;">Suggest your location to be added</button>
		</form>
		
		<br />
		
		<?php
		if ($f['q'] != '') {
			if (count($churches) == 0) {
				?><span style="color:#8700ee; font-size:1.1em;">Sorry no results found..</span><?php
			} else {
				foreach ($churches as $church) {
					?>
					<div style="background-color: #eeeeee; margin-bottom: 20px; padding: 10px;">
						<a style="color: #652d90; font-size: 1.4em;" href="<?php h("{$www_base}/set_church.php?church_id={$church['id']}"); ?>" style="">
							<?php h($church['name']); ?>
						</a>
						<br />
						<span style="color: #c66f18;"><?php h("{$church['stateName']} / {$church['cityName']}"); ?></span>
					</div>
					<?php
				}
			}
		}
		?>
		
	</td>
</tr>
</table>

</body>
</html>