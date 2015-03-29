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
  
$bodyCurrentPage = basename($_SERVER['PHP_SELF']);

include("../General Classes/classDataPair.php");
include("../General Classes/classHTML_HEADERS.php");
include("../General Classes/ClassBiddlecomUsers.php");
include("../General Classes/SECTION_FUNCTIONS.php");
include("../General Classes/LINE-ITEM_FUNCTIONS.php");

include("Classes/ClassSnoopy.php");
// include("Classes/ClassLineItems.php");

include("ClassMySQLiConnection.php");
$conn=  new SnoopbConnection();
$objSnoopy = new Snoopy();
$objSnoopy->setDatabaseObject($conn);

$objSnoopy->get__MacAdresses();

$records = array();
$objRecords =  $objSnoopy->macs; 
foreach($objRecords as $obj)	{

	array_push($records,
			array(	"macAddress"=>$obj->address	)
		);
}
	

foreach($records as $record)	{ 
	$mac = $record['macAddress'];
//	echo "<br>Mac Address is $mac.<br>";
}

echo json_encode($records);