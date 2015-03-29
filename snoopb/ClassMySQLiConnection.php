<?php

Class MySQLiConnection 
{
    var $connection;
    var $paramTypes = array('i', 's');
     
    function MySQLiConnection()  {
        // $this->serverName = $_SERVER['SERVER_NAME']; 
        if($_SERVER['HTTP_HOST'] != 'phoenix')	{
        	$this->serverName = "localhost";
        	$this->userName = 'root';
        	$this->userPassword = 'testing123';
        }
        else
        	$this->serverName = 'phoenix';
      //  echo "<br>$this->connectionName IS RUNNING ON SERVER ".$this->serverName;        
        $this->ConnectToMySQLiDatabase();    
    }
    

    //General connection to the MySQL db

    function ConnectToMySQLiDatabase()	{        

    	$this->connection = new mysqli($this->serverName ,$this->userName, $this->userPassword, $this->database);      
       
        // Check connection
        if ($this->connection->connect_error) {
       		die("<br>Connection failed: " . $this->connection->connect_error);
        }
        else
       		; // echo "<br>Connected successfully";
       
       if (!$this->connection)
          {
            echo "<br>Could not Connect to $connectionInfo[Database] for User  $connectionInfo[UID]<br>";
            die('Could not connect: ' . print_r(sqlsrv_errors()));
          }
       else   {           
           
           /* Retrieve and display the results of the query. */
          
           $logon_name = "Development";
           if($logon_name == null || $logon_name == "") {
                echo "<br>Logon Name is not valid";            
           }
               
        }  	 
    }
    
        
    function getResult($query)  {     // DOES NOT CREATE A PREPARED STATEMENT
    	$result = $this->connection->query($query);
    	 
    	if($result)   {
    		; // Do Nothing
    	}
    	else    {
    		$errMessage = $this->getErrors();
    		echo "<br>**** BAD RESULT FROM QUERY.  QUERY WAS $query. Error is: ".$errMessage;
    	}
    	return $result;
    }
// int array_push ( array &$array , mixed $value1 [, mixed $... ] )
    public function getAssocResultsFromQuery($query)  {         
        $result = $this->getResult($query); 
        /* For MysQLnd Driver Only      
   		$assocResult = $result->fetch_all(MYSQLI_ASSOC);
   		*/

        //Create new array named assocRecords, while the result is still fetching, assign to variable row. Then push row 
        // into the array assocRecords
        $assocRecords = array();
        while ($row = $result->fetch_assoc())	{
        	array_push($assocRecords, $row);
        }
       $this->connection->next_result();
       // print_r( $assocRecords);
      	//echo "ddd ".$assocResult['mac'];
    	
    	return  $assocRecords;    	 
    }   

    
        
    public function getStatementWithParams($query, $params)  { 
  //	echo "<br> Upside down query is $query<br>";
   		$values = array();
   		$valueTypes = array();
   		$typeString ='';
    	$stmt = $this->connection->prepare($query);
    //	echo "<br>Params are: ".print_r($params);
    	if($stmt)   {
	    	foreach($params as $parameter) {  
	    		
	  //      	echo "<br>***************PARAMETER*********************************<br>";
	    	//	print_r($parameter);
	    		$valType	= $parameter[0];
	    		$val 		= $parameter[1];
	    		$valInOut	= $parameter[2];
	    		$typeString .= $this->paramTypes[$valType];
	    		array_push($values, $val);
	    		array_push($valueTypes, $valType);
	    	/*	if($valInOut == SQL_PARAM_OUT)	{
	    			echo "<br>XXXX THIS IS A PARAM OUT";	    		
	    		} */
	 //   		echo "<br>**********************************************************<br>";
	    	}
	    	$numParams = count($values);
	    	
	    	switch ($numParams)	{
	    		case 1: $stmt->bind_param($typeString, $values[0]); break;
	    		case 2: // echo "<br>Along Comes Mary Type string is ***$typeString*** - Values are $values[0] and X $values[1]X <br>"; 
	    				$stmt->bind_param($typeString, $values[0], $values[1] );
	    				//$stmt->bind_param('is', $values[0], $values[1] );
	    				// echo "<br>Fight the power";
	    				break;
	    		case 8; $stmt->bind_param($typeString,  $values[0], $values[1], $values[2], $values[3],
	    												$values[4], $values[5], $values[6], $values[7] );		
	    		default:; break; // Do Nothing
	    	}
	    	
	    	//$stmt->bind_param($this->paramTypes[$valType], $val);
    		if(!$stmt->execute())	{
    			$errMessage = $this->getErrors();
    			echo "<br>****  line 119 BAD EXECUTE STATEMENT   QUERY WAS $query. Error is: ".$errMessage;
    			exit;
    		}
        		
        }
        else    {
        	
        	echo "<br>There was an Error Preparing the Statement<br>".$this->connection->error;
        	exit;
        }
        
        return $stmt;        
    }  

    
    
    
    public function getFunctionResultFromStatement($stmt)  {
    	//$result = $stmt->get_result();
    	//$assocResult = $result->fetch_all(MYSQLI_ASSOC);
    	//print_r($assocResult);
    	//exit;
    	$stmt->bind_result($x);
    	$stmt->fetch();
    //	echo "<br>Value val X is: $x<br>"; 
      	
    	return $x;
    }

    public function closeStatement($stmt)	{
    	//mysqli_stmt_store_result($stmt);
  		$stmt->store_result();
    	//$stmt->close();
    	
    }    
     
    private function getErrors()	{
    	$errMsg = null;
    	if($this->connection->connect_errno) {
    		$errMsg = $mysqli->connect_error;
    	}
    	return $errMsg;
    }
        
    function CloseConnection(){
            sqlsrv_close($this->connection);
        } 

        
       /* function execStoredProcedure($query)  {
        	$params = array();
        	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
        	$stmt = sqlsrv_query( $this->connection, $query, $params, $options);
        	if($stmt)   {
        		return $stmt;
        	}
        	else
        		echo "<br>**** BAD STATMENT:  QUERY WAS $query ";
        		return $stmt;
        } */
        
        public function  getRowFromStatement($stmt)	{
         
        }    
    
   	
}	

Class SnoopbConnection extends MySQLiConnection
{
	var $userName = 'biddlec1_snoop';
	var $userPassword = 'bgxi3wp3@';

	var $database = 'snoopy_db';
	var $connectionName = 'Snoop Snoop';
	
	public function RouterBoutConnection() {
		$this->MySQLiConnection();
	}
}



Class MySQLiConnectionServerName extends MySQLiConnection
//Same as class above except ServerName in constructor
{     
 
    function ItmConnectionServerName($serverNm)  {
    	$this->RouterBoutConnection();
        $this->serverName = $serverNm;
        $this->ConnectToRouterBout();    
    }
     
    
}   




  