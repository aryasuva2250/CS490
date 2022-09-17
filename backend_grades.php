<?php
include ("databaseConnection.php");
include ("db.php");

//autograde
if(isset($_POST['page']) && $_POST['page'] == "agGetQInfo"){
        $testId = $_POST["testId"];
        $q = "SELECT `EXAMS`.`num_questions`, `EXAM_QUESTIONS`.`question_id` FROM `EXAMS` JOIN `EXAM_QUESTIONS` ON `EXAMS`.`id`=`EXAM_QUESTIONS`.`exam_id` WHERE `EXAMS`.`id`='".$testId."';";
        $results = mysqli_query($conn, $q);
        $send = array();
        $temp = array();
        foreach($results as $row){
                $send['numQuestions']=$row['num_questions'];
                array_push($temp, $row['question_id']);
        }
        $send['question_ids']=$temp;
        $j = json_encode($send);
        echo $j;
        return $j;
}

if(isset($_POST['page']) && $_POST['page'] == "agGetQuestion"){
        $testId = $_POST['testId'];
        $question_id = $_POST['qId'];
        $queryMaxPoints=$conn->query("SELECT `max_points` FROM `EXAM_QUESTIONS` WHERE `exam_id` ='".$testId."' AND `question_id` ='".$question_id."'");
        $q1 = $queryMaxPoints->fetch_assoc();
        //$q1=mysqli_query($conn, $queryMaxPoints);
        $queryNumCases = $conn->query("SELECT `numTestCases` FROM `QUESTION` WHERE `id` ='".$question_id."'");
        $q2 = $queryNumCases->fetch_assoc();
        $queryConstraints = $conn->query("SELECT `constraints` FROM `QUESTION` WHERE `id` ='".$question_id."'");
        $q4 = $queryConstraints->fetch_assoc();
        //$q2=mysqli_query($conn, $queryNumCases);
        $queryAnswer = $conn->query("SELECT `answer` FROM `RESULTS` WHERE `test_id` ='".$testId."' AND `question_id` ='".$question_id."'");
        $q3 = $queryAnswer->fetch_assoc();
        //$q3=mysqli_query($conn, $queryAnswer);
        $queryTestCases = "SELECT * FROM TEST_CASES WHERE question_id ='".$question_id."'";
        $send = array();
        $send = array("qPoints"=>$q1['max_points'], "numCases"=>$q2['numTestCases'], "constraints"=>$q4['constraints'], "studentResponse"=>$q3['answer']);
        $result = $conn->query($queryTestCases);
        foreach($result as $row){
                $send[$row["testCaseName"]]=array("runStat"=>$row["runStat"], "expected"=>$row["expected"]);
                //array_push($send, $s);
        }
        $j = json_encode($send);
        echo $j;
        return $j;
}

//student grade
if(isset($_POST['page']) && $_POST['page'] == "agSendGrade"){
        $testId = $_POST['testId'];
        $question_id = $_POST['qId'];
        $res = $_POST['grade'];
        //error_log($res);
        $send = "UPDATE `RESULTS` SET `grade` = '$res', `instructorGrade` = '$res' WHERE `test_id`='".$testId."' AND `question_id`='".$question_id."'";
        //$send = "UPDATE `RESULTS` SET `grade` = '$res' AND `instructorGrade` = '$res' WHERE `test_id`='$testId' AND `question_id`='$question_id'";
        $h = $conn->query($send);
        error_log($send);
        if($h){
                echo "success";
        }
        else {
                echo "nope";
        }
}

//instructor updates grade for student

if(isset($_POST['page']) && $_POST['page'] == "updateGrade"){
        $testId = $_POST['testId'];
        $res = json_decode($_POST['grades'], true);
        foreach($res as $key=>$value){
                $grade = $res[$key]['grade'];
                $comment = $res[$key]['comment'];
                $send = "UPDATE `RESULTS` SET `instructorGrade` = '$grade', `comment` = '$comment' WHERE `question_id` ='".$key."' AND `test_id`='".$testId."'";
                //error_log($send);
                $conn->query($send);
        }
        error_log($send);
        echo json_encode(array("success"));
}

if(isset($_POST['page']) && $_POST['page'] == "teacherScore"){
        $testId = $_POST['testId'];
        $q = "SELECT `question_id`, `instructorGrade` FROM `RESULTS` WHERE `test_id` ='".$testId."'";
        $result = $conn->query($q);
        $send["grade"] = array();
        foreach($result as $row){
                //$send[$row["question_id"]["instructorGrade"]] = array("question_id"=>$row["question_id"], "instructorGrade"=>$row["instructorGrade"]);
                //$send[$row["question_id"]] = $row["instructorGrade"];
                $send["grade"][$row["question_id"]]=$row["instructorGrade"];
                $send[$row["question_id"]] = array();
                $q2 = "SELECT `testCaseName`, `runStat`, `expected` FROM `TEST_CASES` WHERE `question_id`='".$row["question_id"]."'";
                $res = $conn->query($q2);
                error_log($q2);
                foreach($res as $r){
                        $send[$row["question_id"]][$r["testCaseName"]] = array($r["runStat"], $r["expected"]);
                }
        }
        $j = json_encode($send);
        echo $j;
        return $j;
}

if(isset($_POST['page']) && $_POST['page'] == "getStudentScore"){
         $testId = $_POST['testId'];
        $q = "SELECT `question_id`, `instructorGrade`, `grade`, `comment` FROM `RESULTS` WHERE `test_id` ='".$testId."'";
        $result = $conn->query($q);
        $send["grade"] = array();
        foreach($result as $row){
                $send["grade"][$row["question_id"]]=array("auto"=>$row["grade"], "teacher"=>$row["instructorGrade"], "comment"=>$row["comment"]);
                $send[$row["question_id"]] = array();
                $q2 = "SELECT `testCaseName`, `runStat`, `expected` FROM `TEST_CASES` WHERE `question_id`='".$row["question_id"]."'";
                $res = $conn->query($q2);
                error_log($q2);
                foreach($res as $r){
                        $send[$row["question_id"]][$r["testCaseName"]] = array($r["runStat"], $r["expected"]);
                }
        }
        $j = json_encode($send);
        echo $j;
        return $j;

}

$conn->close();
?>
