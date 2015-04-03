<?php
include("dbconfig.php");

//Begin the massive amount of querying that we need on our database

// Now that we have connected succesfully to the database let's try query from it and echo the results for now 

$result = mysqli_query($conn, "SELECT first_obs FROM wifi_client_obs ORDER BY ASC");

//Might be worth recording number of rows we will see for now though $num_rows = mysql_num_rows($result);
//Slap those results into a row array!
// $row = mysqli_fetch_array($result); this is a way to put the results into 
//So we are using the fetch_assoc method from the mysqli_query class to grab results and put them in a row
while ($row = $result->fetch_assoc()) {
        echo $row['first_obs']."<br>";
    }

//Let's query the mac column of the wifi_client_obs table, fetch all those rows and release them for processing!
$macQuery = mysqli_query($conn, "SELECT mac FROM wifi_client_obs");
$macRow = $macQuery->fetch_assoc();
mysqli_free_result($macQuery);

//Next query is to get the last_obs column from wifi_client_obs
$q2= mysqli_query($conn, "SELECT last_obs FROM wifi_client_obs");
while ($foo = $q2->fetch_assoc()) {
        echo $foo['last_obs']."<col>";
    }

//Next query is num_probes column from wifi_client_obs
$q3 = mysqli_query($conn, "SELECT num_probes from wifi_client_obs");
$numProbesRow = $q3->fetch_assoc();
mysqli_free_result($q3);

?>