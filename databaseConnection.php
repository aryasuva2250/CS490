<?php
include("config.php");
$conn = mysqli_connect($databaseHost, $databaseUser, databasePassword, $databaseName);
if(!$conn){
        die("Failed connection ", mysqli_connect_error());
}
?>
