<?php
include ("databaseConnection.php");
include ("db.php");
$requestFromMiddle=isset($_POST['request']);
//function getQuestion($studID, $testID, $qNum)

if((!empty($_POST['studID'])) && (!empty($_POST['testID'])) && (!empty($_POST['qNum']))){
        $stmt = "SELECT id FROM USER";
        $stmt1 = "SELECT id FROM EXAM";
        $stmt2 = "SELECT testCase1, testCase2 FROM QUESTION";
        //$stmt3 = array("studID=>"$stmt, "testID=>"$stmt1, "qNum=>"$stmt2);
        $j = json_encode($stmt);
        echo $j;
        return $j;
}

//function getQuestionNum($testID)
if((!empty($_POST['qNum']))){
        $st = "SELECT id FROM EXAM";
        $st2 = "SELECT testCase1, testCase2 FROM QUESTION";
        //$st3 = array("testID=>"$stmt1, "qNum=>"$stmt2);
        $j = json_encode($st);
        echo $j;
        return $j;
}

//$requestFromMiddle = $_POST['requestFromMiddle'];
//question
if(isset($_POST['question']) && isset($_POST['diff']) && isset($_POST['type']) && isset($_POST['constraint']) && isset($_POST['numTestCases'])){ //&& isset($_POST['runStat']) && isset($_POST['expected'])){ //add constraints
        $question = $_POST['question'];
        //error_log($question);
        $difficulty = $_POST['diff'];
        $topic = $_POST['type'];
        $constraint = $_POST['constraint'];
        $numCases = $_POST['numTestCases'];
        //$runStat = $_POST['runStat'];
        //expected = $_POST['expected'];
        //$s = "INSERT INTO  QUESTION (question, difficulty, topic, testCase1, testCase2, testCase1Output, testCase2Output) VALUES ('$question', '$difficulty', '$topic', '$testCase1', '$testCase2', '$testCase1Output', '$testCase2Output')";
        $s = "INSERT INTO QUESTION (question, difficulty, topic, constraints, numTestCases) VALUES ('$question', '$difficulty', '$topic', '$constraint', '$numCases')";
        $h = mysqli_query($conn, $s);
        $question_id = $conn->insert_id;
        //$testcase = "UPDATE QUESTION SET `numTestCases` = $numCases";
        //$t = mysqli_query($conn, $testcase);
        for($i = 1; $i<=$numCases; $i++){
                $case = "testCase" . $i;
                $expected = "ts" . $i . "_expected";
                $runStat = $_POST[$case];
                $expectedTestCase = $_POST[$expected];
                $send = "INSERT INTO `TEST_CASES` (`question_id`, `testCaseName`, `runStat`, `expected`) VALUES ('$question_id', '$case', '$runStat', '$expectedTestCase');";
                $t = $conn->query($send);
        }
        if($h AND $t){
                echo "Question created successfully";
        }
        else{
                echo "Question not created successfully";
        }
}

if(isset($_POST['diff']) && isset($_POST['type'])){
        //$s = mysqli_query($conn, "SELECT id, question, difficulty, topic FROM `QUESTION`");
        //$r = mysqli_fetch_all($s, MYSQLI_ASSOC);
        //$r = $s->fetch_assoc();
        //$sending = json_encode($r);
        $difficulty = $_POST['diff'];
        $topic = $_POST['type'];
        $s = "SELECT * FROM `QUESTION` WHERE difficulty ='".$difficulty."' AND topic ='".$topic."'";
        //$s = "SELECT * FROM `QUESTION` WHERE difficulty = '$difficulty' AND topic = '$topic'";
        $result = mysqli_query($conn, $s);

        foreach($result as $row){
                //$send = array();
                //if($result){
                //$send = array("id"=>$row['id'], "question"=>$row['question'], "difficulty"=>$row['difficulty'], "type"=>$row['topic']);
                $send[$row['id']] = array("question"=>$row['question'], "diff"=>$row['difficulty'], "type"=>$row['topic']);
                //}
                //else{
                //      $send["query"] = 0;
                //}
        }
        $sending = json_encode($send);
        echo $sending;
        return $sending;
}

//make exam
/*
if(isset($_POST['title'])){
        $title = $_POST['title'];
        $iD = $_POST['id'];
        foreach($iD as $i){
                foreach($score as $s){
                        $sq = "INSERT INTO EXAMS (EXAMS.id, EXAMS.title) SELECT QUESTION.question FROM QUESTION WHERE QUESTION.id == '$id'";
                        $h = mysqli_query($conn, $sq);
                        if($h){
                        echo "Exam created successfully";
                        }
                        else{
                                echo "Exam not created successfully";
                        }
                }
        }
}
*/
/*
if(isset($_POST['title'])){
        $title = $_POST['title'];
        $q = array();
        foreach($title as $qid => $score){
                $q[$qid] = $score;
        }
        $iExam = "INSERT INTO `EXAMS` (`title`) VALUES ($title)";
        $exam_id = $conn->insert_id;
        $exam_questionsI = "INSERT INTO `EXAM_QUESTIONS` (`exam_id`, `question_id`, `max_points`) VALUES ";
        foreach($q as $qs){
                foreach($qs as $questionId => $max_points){
                        $exam_questionsI .= "({$exam_id}, {$question_id}, {$max_points}),";
                }
        }
        $exam_questionsI = substr($exam_questionsI, 0, strlen($exam_questionsI) - 1) . ";";
        $conn->query($exam_questionsI);

}
*/

//student results
if($requestFromMiddle == "studentResults"){
        //send information to middle end to get autograded
        $st = "SELECT EXAM.id, EXAM.question, EXAM.score, EXAM.answer, QUESTION.testCase1, QUESTION.testCase2 FROM EXAM INNER JOIN QUESTION ON QUESTION.question = EXAM.question";
        $send = mysqli_query($conn, $st);
        $autogradeSend = mysqli_fetch_all($send, MYSQLI_ASSOC);
        return json_encode($autogradeSend);
        //get info back from middle end to store in results table
        //if(isset(json_decode($_POST["grade"]))){
                $ht = json_decode($_POST["grade"]);
        if(isset($ht)){
                $d = json_decode($_POST["grade"]);
                foreach($de as $d){
                        $score = $_POST["grade"];
                        $resultScore = "INSERT INTO RESULT (score) VALUES ('$score')";
                        //$question = "INSERT INTO RESULT (RESULT.examQuestion) SELECT EXAM.question FROM EXAM";
                        $res = mysqli_query($conn, $resultScore);
                        //$res2 = mysqli_query($conn, $question);
                        $r = mysqli_fetch_all($res, MYSQLI_ASSOC);
                        //$e = mysqli_fetch_all($res2, MYSQLI_ASSOC);
                        return json_encode($r);
                        //$f = json_encode($e);
                        //echo $f;

                }
        }
        $question = "INSERT INTO RESULT (RESULT.examQuestion) SELECT EXAM.question FROM EXAM";
        $res2 = mysqli_query($conn, $question);
        $e = mysqli_fetch_all($res2, MYSQLI_ASSOC);
        $f = json_encode($e);
        echo $f;
}

//teacher comments - first time
if(isset($_POST["comments"])){
        $comment = $_POST["comment"];
        $c = "INSERT INTO RESULT (comments) VALUES ('$comment')";
}
//updating comments
if(isset($_POST["updatedComments"])){
                $updatedComments = $_POST["updatedComments"];
                $com = "UPDATE RESULT set comments = '$updatedComments'";
}

$conn->close();
?>
