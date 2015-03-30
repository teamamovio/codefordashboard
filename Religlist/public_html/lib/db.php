<?php

require_once dirname(__FILE__)."/conf.php";
require_once dirname(__FILE__)."/func.php";

$db = mysql_connect($db_host, $db_user, $db_pass, true) or err("Couldn't connect DB: ".mysql_error(), __FILE__, __LINE__);
mysql_select_db($db_name, $db) or err("Couldn't select DB: ".mysql_error($db), __FILE__, __LINE__);
mysql_set_charset("UTF8", $db) or err("Couldn't set DB charset: ".mysql_error($db), __FILE__, __LINE__);
mysql_query("SET time_zone = '+0:00'", $db) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);