<?php

 date_default_timezone_set('America/Chicago');
 define('SQL_DATA_TYPE_INT', 0);
 define('SQL_DATA_TYPE_STR', 1);
 define('SQL_PARAM_IN', 1);
 define('SQL_PARAM_OUT', 2);


// $startTime = $_POST['startTime'];   
// $endTime 	= $_POST['endTime'];  
// $startTime=date("Y-m-d h:i:s",strtotime($_POST['startTime']));
// $endTime=date("Y-m-d h:i:s",strtotime($_POST['endTime']));

$local =  localtime(strtotime($_POST['startTime']), true);

$year 	= $local['tm_year'] + 1900;
$month 	= $local['tm_mon'] + 1;
$day 	= $local['tm_mday'];
$hour 	= $local['tm_hour'];
$min	= $local['tm_min'];
$sec	= $local['tm_sec'];
$strTime = "$year-$month-$day $hour:$min:00";
$dateTime  = new DateTime($strTime); 
$startTime = $dateTime->format('Y-m-d H:i:s');

//echo $startTime;

$local =  localtime(strtotime($_POST['endTime']), true);
$year 	= $local['tm_year'] + 1900;
$month 	= $local['tm_mon'] + 1;
$day 	= $local['tm_mday'];
$hour 	= $local['tm_hour'];
$min	= $local['tm_min'];
$sec	=  $local['tm_sec'];
$strTime = "$year-$month-$day $hour:$min:00";
$dateTime  = new DateTime($strTime); 
$endTime = $dateTime->format('Y-m-d H:i:s');

/*
 $startTime=date("Y-m-d h:i:s",strtotime('2015-01-02T01:15:00.000Z'));
 $endTime=date("Y-m-d h:i:s",  strtotime('1/1/2015 8:45:00 PM'));
 echo "Start Time is: $startTime - End Time is $endTime";

// $startTime =  '2015-01-01 20:30:00'; 
// $endTime 	= '2015-01-01 20:45:00'; 
 */

// echo "<br> Start time is: $startTime and End Time is $endTime";
// exit;

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

/* 
$bodyCurrentPage = basename($_SERVER['PHP_SELF']);

include("../General Classes/classDataPair.php");

include("../General Classes/ClassBiddlecomUsers.php");
include("../General Classes/SECTION_FUNCTIONS.php");
include("../General Classes/LINE-ITEM_FUNCTIONS.php");
*/
include("Classes/ClassSnoopy.php");
// include("Classes/ClassLineItems.php");

include("ClassMySQLiConnection.php");
$conn=  new SnoopbConnection();
$objSnoopy = new Snoopy();
$objSnoopy->setDatabaseObject($conn);
$objSnoopy->get__MacAdresses();

$objSnoopy->get__wifi_ap_obs( $startTime,  $endTime );

$records = array();
$objRecords =  $objSnoopy->macs; 
//echo "Count of Macs is: ".count($objSnoopy->macs );
foreach($objRecords as $obj)	{
	array_push($records,
			array(	"mac"=>$obj->address, "detectCount"=>$obj->detectCount)
		);
}
	

foreach($records as $record)	{ 
	$mac = $record['mac'];
	$detectCount = $record['detectCount'];
//	echo "<br>Mac Address is: $mac. Detect Count is: $detectCount<br>";
}

echo  json_encode($records);