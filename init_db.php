<?php
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once ('db.php');
$count = 0;
try{
    foreach(glob(__DIR__ . "/*.sql") as $filename){
        $sql[$filename] = file_get_contents($filename);
    }
    if(isset($sql) && $sql && count($sql) > 0){
        ksort($sql);
        echo "<br><pre>" . var_export($sql, true) . "</pre><br>";
        $db = getDB();
        $stmt = $db->prepare("show tables");
        $stmt->execute();
        $count++;
        $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $t = [];
        foreach($tables as $row){
            foreach($row as $key => $value) {
                array_push($t, $value);
            }
        }
        foreach($sql as $key => $value){
            echo "<br>Running: " . $key;
            $lines = explode("(", $value, 2);
            if(count($lines) > 0){
                $line = $lines[0];
                $line = preg_replace('!\s+!', ' ', $line);
                $line = str_ireplace("create table", "", $line);
                $line = str_ireplace("if not exists", "", $line);
                $line = str_ireplace("`","",$line);
                $line = trim($line);
                if (in_array($line, $t)){
                    echo "<br>Blocked from running, table found in 'show tables' results.<br>";
                    continue;
                }
            }
            $stmt = $db->prepare($value);
            $result = $stmt->execute();
            $count++;
            $error = $stmt->errorInfo();
            if($error && $error[0] !== '00000'){
                echo "<br>Error:<pre>" . var_export($error,true) . "</pre><br>";
            }
            echo "<br>$key result: " . ($result>0?"Success":"Fail") . "<br>";
        }
        echo "<br> Init complete, used approximately $count db calls.<br>";
    }
    else{
        echo "Didn't find any files, please check the directory/directory contents/permissions";
    }
    $db = null;
}
catch(Exception $e){
    echo $e->getMessage();
    exit("Something went wrong");
}
?>
