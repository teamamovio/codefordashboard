<?php

require_once dirname(__FILE__)."/chk_admin.php";

?>

<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Admin Panel</title>

<link rel="stylesheet" type="text/css" href="<?php h("{$www_base}/site.css"); ?>" />

</head>
<body>

<?php include dirname(__FILE__)."/menu.php"; ?>
<h1 align="center">Welcome, Admin!</h1>

</body>
</html>