<?php

require_once dirname(__FILE__)."/../lib/config.php";

if (!isset($_SESSION['admin'])) exit;

?>

<p align="center">
	<a href="<?php h("{$www_base}/admin/"); ?>">Home</a> |
	<a href="<?php h("{$www_base}/admin/states/"); ?>">States</a> |
	<a href="<?php h("{$www_base}/admin/cities/"); ?>">Cities</a> |
	<a href="<?php h("{$www_base}/admin/churches/"); ?>">Churches</a> |
	<a href="<?php h("{$www_base}/admin/cats/"); ?>">Categories</a> |
	<a href="<?php h("{$www_base}/admin/banners/"); ?>">Banners</a> |
	<a href="<?php h("{$www_base}/admin/static/"); ?>">Static Pages</a> |
	<a href="<?php h("{$www_base}/admin/passwd.php"); ?>">Change Password</a> |
	<a href="<?php h("{$www_base}/admin/logout.php"); ?>">Logout</a> (<?php h($_SESSION['admin']['username']); ?>)
</p>