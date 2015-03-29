<?php


Class WifiApObs
{
	var $id	= 0;
	var $macAddress  		= null;
	var $firstObservation 	= null;
	var $lastObservation	= null;
	var $numOfBeacons		= -1;
	var $sunc				= 0;
	var $runID				= 0; 
	
	public function WifiApObs()	{
		
	}
	
	public function setRecordFromRow($row)	{
		$this->macAddress  		= $row['mac'];
	 	$this->firstObservation = $row['first_obs'];
	 	$this->lastObservation	= $row['last_obs'];
	 	$this->numOfBeacons		= $row['num_beacons'];
	 	$this->sunc				= $row['sunc'];
	 	$this->runID			= $row['run_id']; 
	 	
	 	$this->id = $this->macAddress.$this->firstObservation;
	}	
	
	public function setMacAddress($addr)	{
		$this->macAddress	= $addr;
	}	
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