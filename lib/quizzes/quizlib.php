<?php
 
class QuizLib extends TikiLib {
  
  function QuizLib($db) 
  {
    parent::TikiLib($db); 
  }
  
// Functions for Quizzes ////
  function get_user_quiz_result($userResultId)
  {
    $query = "select * from tiki_user_quizzes where userResultId=$userResultId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
     
  function list_quiz_question_stats($quizId)
  {
    $query = "select distinct(tqs.questionId) from tiki_quiz_stats tqs,tiki_quiz_questions tqq where tqs.questionId=tqq.questionId and tqs.quizId = $quizId order by position desc";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $question = $this->getOne("select question from tiki_quiz_questions where questionId=".$res["questionId"]);
      $total_votes = $this->getOne("select sum(votes) from tiki_quiz_stats where quizId=$quizId and questionId=".$res["questionId"]);
      $query2 = "select tqq.optionId,votes,optionText from tiki_quiz_stats tqq,tiki_quiz_question_options tqo where tqq.optionId=tqo.optionId and tqq.questionId=".$res["questionId"];
      $result2 = $this->query($query2);
      $options = Array();
      while($res = $result2->fetchRow(DB_FETCHMODE_ASSOC)) {
        $opt=Array();
        $opt["optionText"]=$res["optionText"];
        $opt["votes"]=$res["votes"];
        $opt["avg"]=$res["votes"]/$total_votes*100;
        $options[]=$opt;
      }
      
      $ques=Array();
      $ques["options"]=$options;
      $ques["question"]=$question;
      $ret[]=$ques;
    }
    return $ret;
  }
  
  function get_user_quiz_questions($userResultId)
  {
    $query = "select distinct(tqs.questionId) from tiki_user_answers tqs,tiki_quiz_questions tqq where tqs.questionId=tqq.questionId and tqs.userResultId = $userResultId order by position desc";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $question = $this->getOne("select question from tiki_quiz_questions where questionId=".$res["questionId"]);
      $query2 = "select tqq.optionId,tqo.points,optionText from tiki_user_answers tqq,tiki_quiz_question_options tqo where tqq.optionId=tqo.optionId and tqq.userResultId=$userResultId and tqq.questionId=".$res["questionId"];
      $result2 = $this->query($query2);
      $options = Array();
      while($res = $result2->fetchRow(DB_FETCHMODE_ASSOC)) {
        $opt=Array();
        $opt["optionText"]=$res["optionText"];
        $opt["points"]=$res["points"];
        $options[]=$opt;
      }
      
      $ques=Array();
      $ques["options"]=$options;
      $ques["question"]=$question;
      $ret[]=$ques;
    }
    return $ret;
  }
  
  function remove_quiz_stat($userResultId)
  {
    $query = "select quizId,user from tiki_user_quizzes where userResultId=$userResultId";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $user = $res["user"];
    $quizId = $res["quizId"];
    
    $query = "delete from tiki_user_taken_quizzes where user='$user' and quizId=$quizId";
    $result = $this->query($query);
    
    $query = "delete from tiki_user_quizzes where userResultId=$userResultId";
    $result = $this->query($query);
    $query = "delete from tiki_user_answers where userResultId=$userResultId";
    $result = $this->query($query);
  }
  
  function clear_quiz_stats($quizId)
  {
    
    $query = "delete from tiki_user_taken_quizzes where quizId=$quizId";
    $result = $this->query($query);
    
    $query = "delete from tiki_quiz_stats_sum where quizId=$quizId";
    $result = $this->query($query);
    
    $query = "delete from tiki_quiz_stats where quizId=$quizId";
    $result = $this->query($query);
    
    $query = "delete from tiki_user_quizzes where quizId=$quizId";
    $result = $this->query($query);
    
    $query = "delete from tiki_user_answers where quizId=$quizId";
    $result = $this->query($query);
  }
  
  
  function list_quiz_stats($quizId,$offset,$maxRecords,$sort_mode,$find)
  {
    $this->compute_quiz_stats();
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where quizId=$quizId";  
    } else {
      $mid="  where quizId=$quizId"; 
    }
    $query = "select * from tiki_user_quizzes $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_user_quizzes $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["avgavg"]=$res["points"]/$res["maxPoints"]*100;
      $hasDet = $this->getOne("select count(*) from tiki_user_answers where userResultId=".$res["userResultId"]);
      if($hasDet) {
        $res["hasDetails"]='y';
      } else {
        $res["hasDetails"]='n';
      }
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
    
  function register_user_quiz_answer($userResultId,$quizId,$questionId,$optionId)
  {
    $query = "insert into tiki_user_answers(userResultId,quizId,questionId,optionId)
    values($userResultId,$quizId,$questionId,$optionId)";
    $result = $this->query($query);
  }
  
  function register_quiz_stats($quizId,$user,$timeTaken,$points,$maxPoints,$resultId)
  {
    $now = date("U");
    $query = "insert into tiki_user_quizzes(user,quizId,timestamp,timeTaken,points,maxPoints,resultId)
    values('$user',$quizId,$now,$timeTaken,$points,$maxPoints,$resultId)";
    $result = $this->query($query);
    $queryId = $this->getOne("select max(userResultId) from tiki_user_quizzes where timestamp=$now and quizId=$quizId");
    return $queryId;
  }
  
  function register_quiz_answer($quizId,$questionId,$optionId)
  {
    $cant = $this->getOne("select count(*) from tiki_quiz_stats where quizId=$quizId and questionId=$questionId and optionId=$optionId");
    if($cant) {
      $query = "update tiki_quiz_stats set votes=votes+1 where quizId=$quizId and questionId=$questionId and optionId=$optionId";
    } else {
      $query = "insert into tiki_quiz_stats(quizId,questionId,optionId,votes)
      values($quizId,$questionId,$optionId,1)";
    }
    $result = $this->query($query);
    
    return true;
  }
  
  function calculate_quiz_result($quizId,$points)
  {
    $query = "select * from tiki_quiz_results where fromPoints<=$points and toPoints>=$points and quizId=$quizId";
    $result = $this->query($query);
    if(!$result->numRows()) return 0;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function user_has_taken_quiz($user,$quizId)
  {
    $cant = $this->getOne("select count(*) from tiki_user_taken_quizzes where user='$user' and quizId=$quizId");
    return $cant;
  }
  
  function user_takes_quiz($user,$quizId)
  {
    $query = "replace into tiki_user_taken_quizzes(user,quizId) values('$user',$quizId)";
    $result = $this->query($query);
  }
  
  function replace_quiz_result($resultId,$quizId,$fromPoints,$toPoints,$answer)
  {
    $answer = addslashes($answer);
    if($resultId) {
      // update an existing quiz
      $query = "update tiki_quiz_results set 
      fromPoints = $fromPoints,
      toPoints = $toPoints,
      quizId = $quizId,
      answer = '$answer'
      where resultId = $resultId";
    } else {
      // insert a new quiz
      $now = date("U");
      $query = "insert into tiki_quiz_results(quizId,fromPoints,toPoints,answer)
      values($quizId,$fromPoints,$toPoints,'$answer')";
      $queryid = "select max(resultId) from tiki_quiz_results where fromPoints=$fromPoints and toPoints=$toPoints and quizId=$quizId";
      $quizId = $this->getOne($queryid);  
    }
    $result = $this->query($query);
    return $quizId;
  }
  
  function get_quiz_result($resultId)
  {
    $query = "select * from tiki_quiz_results where resultId=$resultId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function remove_quiz_result($resultId)
  {
    $query = "delete from tiki_quiz_results where resultId=$resultId";
    $result = $this->query($query);
    return true;    
  }
  
  function list_quiz_results($quizId,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
    $mid=" where quizId=$quizId and (question like '%".$find."%'";  
    } else {
      $mid=" where quizId=$quizId "; 
    }
    $query = "select * from tiki_quiz_results $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_quiz_results $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function replace_quiz($quizId,$name,$description,$canRepeat,$storeResults,$questionsPerPage,$timeLimited,$timeLimit)
  {
    $name = addslashes($name);
    $description = addslashes($description);
    if($quizId) {
      // update an existing quiz
      $query = "update tiki_quizzes set 
      name = '$name',
      description = '$description',
      canRepeat = '$canRepeat',
      storeResults = '$storeResults',
      questionsPerPage = $questionsPerPage,
      timeLimited = '$timeLimited',
      timeLimit = $timeLimit
      where quizId = $quizId";
      $result = $this->query($query);
    } else {
      // insert a new quiz
      $now = date("U");
      $query = "insert into tiki_quizzes(name,description,canRepeat,storeResults,questionsPerPage,timeLimited,timeLimit,created,taken)
      values('$name','$description','$canRepeat','$storeResults',$questionsPerPage,'$timeLimited',$timeLimit,$now,0)";
      $result = $this->query($query);
      $queryid = "select max(quizId) from tiki_quizzes where created=$now";
      $quizId = $this->getOne($queryid);  
    }
    return $quizId;
  }

  function replace_quiz_question($questionId,$question,$type,$quizId,$position)
  {
    $question = addslashes($question);
    if($questionId) {
      // update an existing quiz
      $query = "update tiki_quiz_questions set 
      type='$type',
      position = $position,
      question = '$question'
      where questionId = $questionId and quizId=$quizId";
      $result = $this->query($query);
    } else {
      // insert a new quiz
      $now = date("U");
      $query = "insert into tiki_quiz_questions(question,type,quizId,position)
      values('$question','$type',$quizId,$position)";
      $result = $this->query($query);
      $queryid = "select max(questionId) from tiki_quiz_questions where question='$question' and type='$type'";
      $questionId = $this->getOne($queryid);
    }
    
    return $questionId;
  }

  function replace_question_option($optionId,$option,$points,$questionId)
  {
    $option = addslashes($option);
    // validating the points value
    if ((!is_numeric($points)) || ($points == "")) $points = 0;

    if($optionId) {
      // update an existing quiz
      $query = "update tiki_quiz_question_options set 
      points=$points,
      option = '$option'
      where optionId = $optionId and questionId=$questionId";
      $result = $this->query($query);
    } else {
      // insert a new quiz
      $now = date("U");
      $query = "insert into tiki_quiz_question_options(optionText,points,questionId)
      values('$option',$points,$questionId)";
      $result = $this->query($query);
      $queryid = "select max(optionId) from tiki_quiz_questions where optionText='$option' and questionId=$questionId";
      $optionId = $this->getOne($queryid);
    }
    
    return $optionId;
  }


  function get_quiz($quizId) 
  {
    $query = "select * from tiki_quizzes where quizId=$quizId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function get_quiz_question($questionId) 
  {
    $query = "select * from tiki_quiz_questions where questionId=$questionId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function get_quiz_question_option($optionId) 
  {
    $query = "select * from tiki_quiz_question_options where optionId=$optionId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  

  function list_quiz_questions($quizId,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
    $mid=" where quizId=$quizId and (question like '%".$find."%'";  
    } else {
      $mid=" where quizId=$quizId "; 
    }
    $query = "select * from tiki_quiz_questions $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_quiz_questions $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["options"]=$this->getOne("select count(*) from tiki_quiz_question_options where questionId=".$res["questionId"]);
      $res["maxPoints"]=$this->getOne("select max(points) from tiki_quiz_question_options where questionId=".$res["questionId"]);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function list_all_questions($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
    $mid=" where (question like '%".$find."%'";  
    } else {
      $mid=" "; 
    }
    $query = "select * from tiki_quiz_questions $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_quiz_questions $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["options"]=$this->getOne("select count(*) from tiki_quiz_question_options where questionId=".$res["questionId"]);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function list_quiz_question_options($questionId,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
    $mid=" where questionId=$questionId and (option '%".$find."%'";  
    } else {
      $mid=" where questionId=$questionId "; 
    }
    $query = "select * from tiki_quiz_question_options $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_quiz_question_options $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function remove_quiz_question($questionId)
  {
    $query = "delete from tiki_quiz_questions where questionId=$questionId";
    $result = $this->query($query);
    // Remove all the options for the question
    $query = "delete from tiki_quiz_question_options where questionId=$questionId";
    $result = $this->query($query);
    return true;    
  }
  
  function remove_quiz_question_option($optionId)
  {
    $query = "delete from tiki_quiz_question_options where optionId=$optionId";
    $result = $this->query($query);
    return true;    
  }

  function remove_quiz($quizId)
  {
    $query = "delete from tiki_quizzes where quizId=$quizId";
    $result = $this->query($query);
    $query = "select * from tiki_quiz_questions where quizId=$quizId";
    $result = $this->query($query);
    // Remove all the options for each question
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {    
      $questionId = $res["questionId"];
      $query2 = "delete from tiki_quiz_question_options where questionId=$questionId";
      $result2 = $this->query($query2);
    }
    // Remove all the questions
    $query = "delete from tiki_quiz_questions where quizId=$quizId";
    $result = $this->query($query);
    $query = "delete from tiki_quiz_results where quizId=$quizId";
    $result = $this->query($query);
    $query = "delete from tiki_quiz_stats where quizId=$quizId";
    $result = $this->query($query);
    $query = "delete from tiki_user_quizzes where quizId=$quizId";
    $result = $this->query($query);
    $query = "delete from tiki_user_answers where quizId=$quizId";
    $result = $this->query($query);
    $this->remove_object('quiz',$quizId);
    return true;    
  }
  
  // Function for Quizzes end ////
    
  
  
}

$quizlib= new QuizLib($dbTiki);
?>