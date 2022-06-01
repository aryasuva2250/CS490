<?php
include ("databaseConnection.php");
include ("db.php");
if(isset($_POST["username"]) && isset($_POST["passwd"])){
        $username = $_POST['username'];
        $password = $_POST['passwd'];
        if(!empty($username) && !empty($password)){
                $result = mysqli_query($conn, "SELECT * FROM `USER` WHERE username = '$username'");
                if($result){
                        $row = mysqli_fetch_assoc($result);
                        if($password == $row['passwd']){
                                $ves = array('type'=>$row['type'], "login"=>1);
                                $j = json_encode($ves);
                                echo $j;
                                return $j;
                        }
                        else{
                                $ves = array("login"=>0);
                                $j=json_encode($ves);
                                echo $j;
                                return $j;

                        }
                }
                else{
                        $ves = array("login"=>0);
                        $j=json_encode($ves);
                        echo $j;
                        return $j;
                }
        }
}
$conn->close();
?>

