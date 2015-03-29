<?php

 date_default_timezone_set('America/Chicago');
 define('SQL_DATA_TYPE_INT', 0);
 define('SQL_DATA_TYPE_STR', 1);
 define('SQL_PARAM_IN', 1);
 define('SQL_PARAM_OUT', 2);

/* Windows Logon
$userName = $_SERVER['LOGON_USER'];
echo "<br>*** Server LOGON User is: $userName";
 */
 
 /* ************************************************************************** */
 
 /*
  * BUILD REDIRECT STRING.
 * AUTHENDICATE CURRENT USER AND CHECK USER PRIVILEGES (SecurePage.php).
 * SEND USER TO TO LOGIN SCREEN IF USER IS NOT ALREADY LOGGED IN, THE REDIRECT
 * BACK TO THIS PAGE.
 */
 /*
 if($pageType == 0)
 	$GETAPPENDS = "?id=".$orderId."%26order_type=2";
 else
 	if($pageType == 1)
 	$GETAPPENDS = "?id=".$orderId."%26order_type=2".
 	"%26lid=$lineItemId";
 
 //include("/includes/SecurePage.php");
  * 
  * 
  */
 /* ************************************************************************** */
  

include("Classes/ClassSnoopy.php");

include("ClassMySQLiConnection.php");
$conn=  new SnoopbConnection();
$objSnoopy = new Snoopy();
;




$objSnoopy->setDatabaseObject($conn);

// $startTime =  $_POST['startTime']; // '2015-01-02T02:22:00.000Z';
// $endTime 	= $_POST['endTime']; // '2015-01-02T02:42:00.000Z';

$startTime =  '2015-01-02T02:41:00.000Z';
$endTime 	= '2015-01-26T02:42:00.000Z';

$objSnoopy->getLogRecords($objSnoopy->fmtFromZuluTime($startTime), $objSnoopy->fmtFromZuluTime($endTime));



$local =  localtime(strtotime($startTime), true);

$year 	= $local['tm_year'] + 1900;
$month 	= $local['tm_mon'] + 1;
$day 	= $local['tm_mday'];
$hour 	= $local['tm_hour'];
$min	= $local['tm_min'];
$sec	= $local['tm_sec'];
$strTime = "$year-$month-$day $hour:$min:$sec";
$dateTime  = new DateTime($strTime);
$startTime = $dateTime->format('Y-m-d H:i:s');

//echo $startTime;

$local =  localtime(strtotime($endTime), true);
$year 	= $local['tm_year'] + 1900;
$month 	= $local['tm_mon'] + 1;
$day 	= $local['tm_mday'];
$hour 	= $local['tm_hour'];
$min	= $local['tm_min'];
$sec	= $local['tm_sec'];
$strTime = "$year-$month-$day $hour:$min:$sec";
$dateTime  = new DateTime($strTime);
$endTime = $dateTime->format('Y-m-d H:i:s');

$objSnoopy->getLogRecords($startTime, $endTime);

$records = array();
$objRecords =  array_reverse($objSnoopy->objWifiApObsArray ); 
foreach($objRecords as $obj)	{

	array_push($records,
			array(	"rec_id"=>$obj->id,
					"mac"=>$obj->macAddress, "time1"=>$obj->firstObservation,
					"time2"=>$obj->lastObservation, "beacon_count"=>$obj->numOfBeacons,
					"sunc"=>$obj->sunc, "runID"=>$obj->runID)
		);
}
	

foreach($records as $record)	{ 
	$mac = $record['mac'];
	$firstObs = $record['time1'];
//	echo "<br>Mac Address is $mac. First Observation was at $firstObs";
}

echo json_encode($records);