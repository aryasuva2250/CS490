<?php
include ("databaseConnection.php");
include ("db.php");

if(isset($_POST['page']) && $_POST['page'] == "makeExam"){
        $num_questions = $_POST['numQuestions'];
        $name = $_POST['name'];
        $s = "INSERT INTO EXAMS (num_questions, name) VALUES ('$num_questions', '$name')";
        $conn->query($s);
        $exam_id = $conn->insert_id;
        $res = json_decode($_POST['questions']);
        error_log(var_export($res));

        if($res){
                $questions_data = $_POST['questions'];
                //$send = "INSERT INTO `Exam_Questions` (`exam_id`, `question_id`, `max_points`) VALUES ";
                //foreach($questions as $question){ //=> $q){
                        //$send = "INSERT INTO Exam_Questions (
                        //foreach($question as $question_id => $max_points){
                          //      $send .= "({$exam_id}, {$question_id}, {$max_points}),";
                        //}
                //}
                $send = "";
                foreach($res as $question_id => $max_points){
                                $send = "INSERT INTO `EXAM_QUESTIONS` (`exam_id`, `question_id`, `max_points`) VALUES ($exam_id, $question_id, $max_points);";
                                $conn->query($send);
                }
        }
}


//}

//take exam
if(isset($_POST['testId']) && isset($_POST['page']) && $_POST['page'] == "examQuestions"){
        /*
        $examId = (int)$_POST['testId'];
        $res = $conn->query("SELECT * FROM EXAMS WHERE `id`=`$examId`");
        $getExamQuestions = " SELECT `EXAMS_QUESTIONS`.`id` AS `examQuestion_id`, `QUESTION`.`question` AS `quest`, `EXAM_QUESTIONS`.`max_points` AS `questionMaxScore` FROM `QUESTION` JOIN `EXAM_QUESTIONS` ON `EXAM_QUESTIONS`.`question_id`=`QUESTION`.`id` JOIN `EXAMS` ON `EXAMS`.`id`=`EXAM_QUESTIONS`.`exam_id` WHERE `EXAMS`.`id`={$examId}";
        $result = mysqli_query($conn, $getExamQuestions);
        $j = json_encode($result);
        echo $j;
        return $j;
        */
        $send = array();
        $examId = (int)$_POST['testId'];
        $res = $conn->query("SELECT * FROM EXAMS WHERE id='".$examId."';");
        $res = $res->fetch_assoc();
        $send["numQuestions"] = $res["num_questions"];
        $send["examName"] = $res["name"];

        $getExamQuestions = "SELECT * FROM EXAM_QUESTIONS LEFT JOIN QUESTION on QUESTION.id=EXAM_QUESTIONS.question_id WHERE exam_id='".$examId."'";

        $result = $conn->query($getExamQuestions);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $send["questions"][$row["question_id"]] = array("question"=>$row["question"], "points"=>$row["max_points"]);
            }
        }
        $j = json_encode($send);
        echo $j;
        return $j;

}

//get exams for student page
if(isset($_POST['page']) && $_POST['page'] == "listExams"){
//if(isset($_POST['get_exams'])){
        $get_exams = "SELECT * FROM EXAMS";
        //$result = mysqli_query($conn, $get_exams);
        $result = $conn->query($get_exams);
        $a = [];
        while($r = $result->fetch_assoc()){
                //$a[] = array('id'=>$r['id'], 'name'=>$r['name']);
                $a[$r['id']] = $r['name'];
        }
        $j = json_encode($a);
        echo $j;
        return $j;
}
if(isset($_POST['page']) && $_POST['page'] == "submitExam"){
        $examId = $_POST['testId'];
        $arr = json_decode($_POST['sub'], true);
        error_log(var_export($arr));
        $send = "";
        foreach($arr as $question_id => $answer){
                $send = "INSERT INTO `RESULTS` (`test_id`, `question_id`, `answer`) VALUES ($examId, $question_id, \"$answer\");";
                error_log($send);
                $conn->query($send);
        }
}
?>
