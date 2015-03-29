<?php


include("ClassWifiApObs.php");

class Mac
{
	var $id = 0;
	var $address = null;
	var $detectCount = 0;
	
	
	function Mac($strAddress)	{
		$this->address = $strAddress;
		//$this->detectCount++;
	//	echo "<br>Added $this->address to this MAC Object";
	}
	
	function isMacAddrThis($strAddress)	{
		if($this->address == $strAddress)
			return true;
		else 
			return false;
	}
	
}	


Class Snoopy	{ 
	var $connection 	= null;
	var $objDb			= null;	 
	
	var $objWifiApObsArray = array();
	var $macs				= array();
	
	var $timeSamples 	= array();
	public function Snoopy()	{
		
		
	}
	
	function setDatabaseObject($dbObject) {
        $this->objDb = $dbObject;
       // $this->get__wifi_ap_obs();
    }
    
/* MySQLi Prepared Statement  Version
    function get__MacAdresses()	{
    	$query = "Call GetMacAddresses()";
    	$params = array();
   		
    	$stmt = $this->objDb->getStatementWithParams($query, $params);
    	$stmt->bind_result($col1);
    	// $records = $this->objDb->getAssocResultsFromStatement($stmt);
    	//$numRecords = count($records);
    
  
    	while($stmt->fetch())	{   	
    		$newMac = new Mac($col1);    		
    		array_push($this->macs, $newMac);   	
    	}
    }
 */    
    
    function fmtFromZuluTime($zuluStr)	{
    	$zulu 	= localtime(strtotime($zuluStr), true);
    	
    	$year 	= $zulu['tm_year'] + 1900;
    	$month 	= $zulu['tm_mon'] + 1;
    	$day 	= $zulu['tm_mday'];
    	$hour 	= $zulu['tm_hour'];
    	$min	= $zulu['tm_min'];
    	$sec	= $zulu['tm_sec'];
    	
    	$strTime = "$year-$month-$day $hour:$min:$sec";
    	$dateTime  = new DateTime($strTime);
    	$fmtStrTime =  $dateTime->format('Y-m-d H:i:s');
    	
    	return $fmtStrTime;
    }

    function get__MacAdresses()	{
    	$query = "Call GetMacAddresses()";    		
   		
   		$records =  $this->objDb->getAssocResultsFromQuery($query);
   		$numRecords = count($records);
  // 	echo "<br>Number of Records is: $numRecords";
  // 	echo " 1 Mac Address is: ".$records['mac'];
    	foreach($records as $i => $record)	{
    	//	echo "<br>Mac Address is: ".$record['mac'];
    		$newMac = new Mac($record["mac"]);
    		array_push($this->macs, $newMac);
    	}
    }
  function getLogRecords($startTime, $endTime)	{
    	$query = "Call GetWifi_ap_obs('$startTime', '$endTime')";
    	 
    	$records =  $this->objDb->getAssocResultsFromQuery($query);
    	$numRecords = count($records);
    	// 	echo "<br>Number of Records is: $numRecords";
    	// 	echo " 1 Mac Address is: ".$records['mac'];
    	 foreach($records as $i => $record)	{
		
			$objWifiApObs = new WifiApObs();
			$objWifiApObs->setRecordFromRow($record);
		/*	For MySQLi Prepared Statements 
			$objWifiApObs->setMacAddress($col1);
			$objWifiApObs->setfirstObservation($col2);
			$objWifiApObs->setLastObservation($col3);
			$objWifiApObs->setNumBeacoms($col4);
			$objWifiApObs->setSunc($col5);
			$objWifiApObs->setRunID($col6); */
			
			array_push($this->objWifiApObsArray, $objWifiApObs);
    	}
    } 
    
    function get__wifi_ap_obs($startTime, $endTime)	{
    	$query = "Call GetWifi_ap_obs('$startTime', '$endTime')";
		 
	//	 echo "<br>Start Time is: $starTime - End Time is: $endTime";
		/* MSQLI Preparation - Don't forget to add Statement Fetch Loop
		$params = array(
				array(SQL_DATA_TYPE_STR, $starTime, SQL_PARAM_IN),
				array(SQL_DATA_TYPE_STR, $endTime, SQL_PARAM_IN)
				);		
		$stmt = $this->objDb->getStatementWithParams($query, $params);	
		$stmt->bind_result($col1, $col2, $col3, $col4, $col5, $col6);
		*/
    	
		$records = $this->objDb->getAssocResultsFromQuery($query);
		$numRecords = count($records);
		
		//  echo "<br>Num of Records is $numRecords<br>";
		 foreach($records as $i => $record)	{
		
			$objWifiApObs = new WifiApObs();
			$objWifiApObs->setRecordFromRow($record);
		/*	For MySQLi Prepared Statements 
			$objWifiApObs->setMacAddress($col1);
			$objWifiApObs->setfirstObservation($col2);
			$objWifiApObs->setLastObservation($col3);
			$objWifiApObs->setNumBeacoms($col4);
			$objWifiApObs->setSunc($col5);
			$objWifiApObs->setRunID($col6); */
			
			array_push($this->objWifiApObsArray, $objWifiApObs);
			 
			foreach($this->macs as $i=> $mac)	{
				//echo "<br>Obj Mac is $mac->address - Db Mac is $col1";
				if($mac->isMacAddrThis($objWifiApObs->getMacAddress()))	{					
					$mac->detectCount++;	
				}						
			} //echo "<br><br>";
			//echo "<br><br>";
		}
			//echo "<br>Class Objects are<br><br>".var_dump(get_object_vars($objWifiApObs));
    }
}
 