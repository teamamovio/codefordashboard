<?php

include("ClassWifiApObs.php");

class Mac
{
	var $id = 0;
	var $address = null;
	var $detectCount = 0;
	var $dataPointsActive = array();
	var $dataPointActiveKey = array(); //Warning, this hold null values if Point interval is not found
	
//named a method within the class Mac as Mac but we will live with it. 
	function Mac($strAddress)	{
		$this->address = $strAddress;
		//$this->detectCount++;
	//	echo "<br>Added $this->address to this MAC Object";
	}
//Not sure of the purpose of this method at this point. If this.adddress is equal to 	
	function isMacAddrThis($strAddress)	{
		if($this->address == $strAddress)
			return true;
		else 
			return false;
	}
//might have warnings with non explicit variable declaration. var $i = 0; looks to push 2 data points into the 
//dataPointsActive array. 
	function initDataPointsArray()	{
		for($i =0; $i < 2; $i++)	{
			array_push($this->dataPointsActive);
		}
	}
	
	function addDataPointActive($bDetectFound)	{
		array_push($this->dataPointsActive, $bDetectFound);
	}
	
	
}	

class TimeInterval	{
	var $startTime 	= null;
	var $endTime 	= null;
//Method defining start and end times	
	function TimeInterval($start, $interval)	{
		$this->startTime	= new DateTime($start);
		$this->endTime		= new DateTime($end);
	}
}

Class Snoopy	{ 
	var $connection 			= null;
	var $objDb					= null;	 
	
	var $objWifiApObsArray 		= array();
	var $macs					= array();
	
	var $timeStart 				= null;
	var $timeEnd				= null;		
	var $timeIntervalMinutes	= 1;
	var $timeDataPointsCount	= 5;
	var $timeSamples 			= array();
	
	//Empty method we should delete. 
	public function Snoopy()	{
		
		
	}
	
	function initDataSampling($interval, $numDataPoints)	{
		$this->timeIntervalMinutes = $interval;
		$this->timeDataPointsCount = $numDataPoints;
	}
	
	function get__MacAdresses($startTime, $endTime)	{
		$this->timeStart = $startTime;
		$this->timeEnd   = $endTime;
		$query = "Call GetMacAddresses('$startTime', '$endTime')";
		
		$records =  $this->objDb->getAssocResultsFromQuery($query);
		$numRecords = count($records);
		// 	echo "<br>Number of Records is: $numRecords";
		// 	echo " 1 Mac Address is: ".$records['mac'];
		foreach($records as $i => $record)	{
		//	echo "<br>Mac Address is: ".$record['mac'];
			$newMac = new Mac($record["mac"]);
			$dateTmFirstObs = new DateTime($this->timeStart);
			$dateTmLastObs	= new DateTime($this->timeEnd);
			$this->addInterval($dateTmLastObs);
			for($i = 0; $i < $this->timeDataPointsCount; $i++)	{		
				$bDetectFound = 0;
				$primKey = $this->blah($newMac, $dateTmFirstObs, $dateTmLastObs);
				if($primKey != null)
					$bDetectFound = 1;
			//	$mac->dataPointsActive[$i] = $bDetectFound;
			//	$mac->dataPointActiveKey[$i] = $primKey;
				$this->subInterval($dateTmFirstObs);
				$this->subInterval($dateTmLastObs);
			//	echo "<br>";
			}
			
			array_push($this->macs, $newMac);
		//	echo "<br> ********************************<br>";
		}
	}
	
	function addInterval($objObsDateTime)	{
		$inteveral = new DateInterval('PT' . $this->timeIntervalMinutes . 'M');
	
		$objObsDateTime->add($inteveral);
	}
	
	function subInterval($objObsDateTime)	{
		$inteveral = new DateInterval('PT' . $this->timeIntervalMinutes . 'M');
		$inteveral->invert = 1;
		$objObsDateTime->add($inteveral);
	}
	
	function setDataPointsActive()	{
		$timeSample = new TimeInterval($this->timeStart);
		array_push($this->timeSamples, $timeSample);
	}
	
	/*function fillMacsDetectArray()	{
		foreach($this->macs as $mac)	{
			for($i = 0; $i < 4; $i++)	{
				$bDetectFound = $this->blah($mac);
				$mac->dataPointsActive[$i] = $bDetectFound;
				echo "<br>";
			}
		}
	} */
	
	function blah($objMac, $dateTmFirstObs, $dateTmLastObs)	{
		
		
	
		$strFirstObs 	= $dateTmFirstObs->format('Y-m-d H:i:s');
		$strLastObs		= $dateTmLastObs->format('Y-m-d H:i:s');
		
		
		$query = "SELECT isTimeIntervalFound('$objMac->address', '$strFirstObs', '$strLastObs') AS 'KEY'";
	//	echo "<br>Query is: $query";
	//	$interval = date_diff($dateTmFirstObs, $dateTmLastObs, true);
		
	//	$minutes = $interval->i;
		
				 
		$records =  $this->objDb->getResult($query);
	//	print_r($records);
		$numRecords = count($records);
	//	echo "<br>Number of Records is: $numRecords";
		$bIntervalFound = 0;
		$recordKey = null;
		foreach($records as $i => $record)	{
		//		echo "<br>Primary Key is: ".$record['KEY'];
				if($record['KEY'] != null){
					$bIntervalFound = 1;
					$recordKey = $record['KEY'];
				}
			//$newMac = new Mac($record["mac"]);
			//array_push($this->macs, $newMac);
		}
		array_push($objMac->dataPointsActive, $bIntervalFound);
		array_push($objMac->dataPointActiveKey, $recordKey );
		// 	echo " 1 Mac Address is: ".$records['mac'];
		
	
	/*	for($i = 0; $i<$minutes; $i++)	{
			array_push($objMac->$dataPointsActive, $objMac);
		} */
			
	//	array_push($this->objWifiApObsArray, $objWifiApObs);
		return $bIntervalFound;
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
 