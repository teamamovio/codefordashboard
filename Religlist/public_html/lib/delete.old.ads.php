#!/usr/bin/env php
<?php

require_once dirname(__FILE__)."/db.php";

mysql_query(
	"DELETE FROM ads ".
	"WHERE TIMESTAMPDIFF(DAY, created_on, NOW()) > 30",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);

mysql_query(
	"DELETE FROM events ".
	"WHERE TIMESTAMPDIFF(DAY, created_on, NOW()) > 30",
	$db
) or err("MySQL error: ".mysql_error($db), __FILE__, __LINE__);