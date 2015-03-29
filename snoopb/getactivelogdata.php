<?php

 date_default_timezone_set('America/Chicago');
 define('SQL_DATA_TYPE_INT', 0);
 define('SQL_DATA_TYPE_STR', 1);
 define('SQL_PARAM_IN', 1);
 define('SQL_PARAM_OUT', 2);



include("Classes/ClassSnoopy.php");

include("ClassMySQLiConnection.php");
$conn=  new SnoopbConnection();
$objSnoopy = new Snoopy();
$objSnoopy->setDatabaseObject($conn);



/*
foreach($records as $record)	{
	$mac 		= $record['mac'];
	$firstObs 	= $record['time1'];
	$secondObs 	= $record['time2'];
		echo "<br>Mac Address is $mac. First Observation was at $firstObs";
}
*/
;

$startTime =  $_POST['startTime']; 
$endTime 	= $_POST['endTime']; 

//$startTime =  '2015-01-22T23:41:00.000Z';
//$endTime 	= '2015-01-22T23:45:00.000Z';


//$startTime 	= '2015-01-21 01:47:00';
//$endTime	= '2015-01-21 01:49:00';

//$objSnoopy->getLogRecords($objSnoopy->fmtFromZuluTime($startTime), $objSnoopy->fmtFromZuluTime($endTime));



$local =  localtime(strtotime($startTime), true);

$year 	= $local['tm_year'] + 1900;
$month 	= $local['tm_mon'] + 1;
$day 	= $local['tm_mday'];
$hour 	= $local['tm_hour'] + 1;
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

$objSnoopy->initDataSampling(1, 60);
$objSnoopy->get__MacAdresses($startTime, $endTime);

$records = array();
$objMacs =  $objSnoopy->macs; 
//$objSnoopy->fillMacsDetectArray();
foreach($objMacs as $idx=> $objMac)	{
//	echo "<br> Mac $objMac->address Found Indexes:<br>";
//	print_r($objMac->dataPointsActive);
//	echo "<br>";

	$pointArray = array();
	$pointArray["mac"] = $objMac->address;
	for($i=0; $i < 60; $i++)	{		
		
		$pointArray["time".($i + 1)] = $objMac->dataPointsActive[$i];
		$pointArray["key_time".($i + 1)] = $objMac->dataPointActiveKey[$i];
		
	}
	array_push($records,$pointArray);
/*	array_push($records,
			array(	
					"mac"=>$objMac->address,
					"time1"=>$objMac->dataPointsActive[0],
					"key_time1"=>$objMac->dataPointActiveKey[0],
				
					"time2"=>$objMac->dataPointsActive[1],
					"key_time2"=>$objMac->dataPointActiveKey[1],
					
					"time3"=>$objMac->dataPointsActive[2],
					"key_time3"=>$objMac->dataPointActiveKey[2],
					
					"time4"=>$objMac->dataPointsActive[3],
					"key_time4"=>$objMac->dataPointActiveKey[3],
					
					"time5"=>$objMac->dataPointsActive[4],
					"key_time5"=>$objMac->dataPointActiveKey[4]
					
					) 
		); */
}
echo json_encode($records);
exit;	

foreach($records as $record)	{ 
	$mac = $record['mac'];
	$firstObs = $record['time1'];
//	echo "<br>Mac Address is $mac. First Observation was at $firstObs";
}

$objSnoopy->blah();
//echo "minutes is: ".$objSnoopy->blah();


$records = array();

$objRecords =  $objSnoopy->macs;
foreach($objRecords as $obj)	{
	print_r($obj->dataPointsActive);
	array_push($records,
			array(
					"mac"=>$obj->address,
					"time1"=>$obj->dataPointsActive[0],
					"time2"=>$obj->dataPointsActive[1],
					"time3"=>$obj->dataPointsActive[2],
					"time4"=>$obj->dataPointsActive[3])
	);
}

