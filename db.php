<?php
include("config.php");
$conn = mysqli_connect($databaseHost, $databaseUser, databasePassword, $databaseName);
if(!$conn){
        die("Failed connection ", mysqli_connect_error());
}
?>
afsconnect1-66 CS490 >: cat db.php
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
function getDB(){
    global $db;
    if(!isset($db)) {
        try{
            require_once('config.php');
            $connection_string = "mysql:host=$databaseHost;dbname=$databaseName;charset=utf8mb4";
            $db = new PDO($connection_string, $databaseUser, $databasePassword);
        }
    catch(Exception $e){
            var_export($e);
            $db = null;
        }
    }
    return $db;
}
?>
