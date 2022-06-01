<?php
include ("databaseConnection.php");
include ("db.php");
function P($result, $username, $password) {
        $st = 'invalid';
        while($row = $result->fetch_assoc()){
                 if ($user == $row['Username'] and password_verify($password, $row['Password'])){
                        $st = $row['Status'];
                        $c = True;
                }
        }
        return $st;
}
$s = file_get_contents("php://input");
$res = json_decode($s, true);
$username = "";
$password = "";
if(isset($res['username']) && isset($res['password'])){
        $username = $res['username'];
        $password = $res['password'];
}
$result = mysqli_query($conn, "SELECT * FROM `USER`");
$v = P($result, $username, $password);
if($v!=null){
        $ves = array('role'=>$v, 'login'=>true);
        $j = json_encode($ves);
        echo $j;
        return $j;
}
else{
        $ves = array("login"=>false);
        $j=json_encode($ves);
        echo $j;
        return $j;
}
$conn->close();
?>
