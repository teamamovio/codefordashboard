<!-- Connect to the MySQL database and validate connection, if the connection fails, throw error as to why -->

<?php
$servername = "localhost";
$username = "root";
$password = "testing123";
$database = "snoopy_db";

// Create connection, make sure we are connecting to snoopy_db
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "We have made a super leet connection";

?>