<?php

//One class Wifi Observations, holds all methods and members of the WifiApObs class, to be determined where this is used. 
// In PHP, a variable starts with the $ sign, followed by the name of the variable. Not jQuery!
//Not sure on $numOfBeacons, might want to talk about this a litte...
//Notes to self, -> is accessing a member a member of an object, could be a var, method, etc. So in our terms it would be myObject.myField, it is not 
//dereferncing a pointer like in C, I know this is a huge bitch. 

Class WifiApObs
{
	var $id	= 0;
	var $macAddress  		= null;
	var $firstObservation 	= null;
	var $firstObsDtObj		= null;
	var $lastObservation	= null;
	var $lastObsDtObj		= null;
	var $numOfBeacons		= -1;
	var $sunc				= 0;
	var $runID				= 0; 
	
	public function WifiApObs()	{
		
	}
	
	public function setRecordFromRow($row)	{
		$this->macAddress  		= $row['mac'];
	 	$this->firstObservation = $row['first_obs'];
	 	$this->lastObservation	= $row['last_obs'];
	 	$this->numOfBeacons		= $row['num_probes'];
	 	$this->sunc				= $row['sunc'];
	 	$this->runID			= $row['run_id']; 
	 	
	 	$this->id = $this->macAddress.$this->firstObservation;
	}	
	//assign this.macAddress to addr
	public function setMacAddress($addr)	{
		$this->macAddress = $addr;
	}	

	//return this.macAddress
	public function getMacAddress()	{
		return $this->macAddress;
	}
	
	
	public function setfirstObservation($strDateTime)	{
		$this->firstObservation	= $strDateTime;
	}	
	public function getfirstObservation()	{
		return $this->firstObservation;
	}
	
	
	public function setLastObservation($strDateTime)	{
		$this->lastObservation = $strDateTime;
	}	
	public function getLastObservation()	{
		return $this->lastObservation;
	}
	
	public function setNumBeacoms($nBeacons)	{
		$this->numOfBeacons = $nBeacons;
	}	
	public function getNumBeacoms()	{
		return $this->numOfBeacons;
	}
	
	public function setSunc($sunc)	{
		 $this->sunc	= $sunc;
	}	
	public function getSunc()	{
		return $this->sunc;
	}
	
	public function setRunID($id)	{
		 $this->runID	= $id;
	}
	
	public function getRunID()	{
		return$this->runID;
	}
}