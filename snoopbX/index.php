 <?php	
 /*if (extension_loaded('mysqlnd'))
 	echo 'extension mysqlnd is loaded'; // WORKED
 
 if (extension_loaded('mysqli'))
 	echo 'extension mysqli is loaded'; // WORKED
 exit;
 */
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
// include("../itm/ClassVisualConnection.php");
/* ******************************************* */
 // disabling 12/22/2014 include("../check_privileges.php");
/* ******************************************* */




/*
include("../class_get_browser_type.php");
$objBrowser = new Browser();
$browserName = $objBrowser->getBrowser();
*/

/*
$conn=  new SnoopbConnection();
$objSnoopy = new Snoopy();
$objSnoopy->setDatabaseObject($conn);
*/

$bContinue = true;
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html >
<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		
		<title>Snoop Monitor</title>
		<script src="../jquery-1.11.1.js">	</script>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script src="http://code.highcharts.com/highcharts.js"></script>			
		<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
		 <link rel="stylesheet" href="/resources/demos/style.css">
		
		<script>
			<?php 	if($_SERVER['HTTP_HOST'] != 'phoenix')	{ ?>
					$.getScript("snoopscript.js");
			<?php 	}
				else { ?>
					$.getScript("snoopscript.js");
			<?php 	}				
				?>	 
		</script>
	
		
		
</head>
    
   
    
<body style = "text-align:center">
	
<div style = "height: 900px; width:1024px; text-align:center; margin: 0 auto; background-color:yellow">	
	
	<button id = "get_x_axis" >Get MACS</button>	
	<button id = "addSeries" >Add series</button>
	<button id = "completeChart">Finish</button>
	Current Time:<input id ="currentTime" style = "width: 400px">
	Loading%:<input id ="loadPercent" style = "width: 50px">
	Data Points Found:<input id="dataPointFound" style = "width:80px">


 


	<div id="container" style="float:left; width:100%; height:auto; margin: 0 auto; background-color:transparent">
	</div>	
	
	<div style = "height:400px; background-color:green; margin-top:450px; overflow-y:scroll">
		 <table id="movingLog" width="99%" border="1" style = "height:auto">
			  <caption>
			    MAC Findings
			  </caption>
			  <tr>
			  	<th scope="col" style ="width:40px">TBD</th>
			    <th scope="col" style ="width:40px">MAC</th>
			    <th scope="col" style ="width:120px">1st Observation</th>
			   <?php /*  <th scope="col" style ="width:40px">Last Observation;</th>
			    <th scope="col" style ="width:40px">#Beacons</th>
			    <th scope="col" style ="width:40px">sunc</th>
			    <th scope="col" style ="width:40px">runI;</th> */ ?>
			  </tr>
			<?php  /* <tr>
			  	<?php foreach($objSnoopy->objWifiApObsArray as $obj)	{ ?>
			  	<td><?php echo $obj->macAddress ?></td>
			  	<td><?php echo $obj->firstObservation ?></td>
			  	<td><?php echo $obj->lastObservation ?></td>
			  	<td><?php echo $obj->numOfBeacons ?></td>
			  	<td><?php echo $obj->sunc ?></td>
			  	<td><?php echo $obj->runID ?></td>
			  </tr> 
			  <?php } */ ?>
	</table> 
	</div>      
	        
</div>
</body>

