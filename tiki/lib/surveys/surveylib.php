<?php
 
class SurveyLib extends TikiLib {
    
  function SurveyLib($db) 
  {
    if(!$db) {
      die("Invalid db object passed to UsersLib constructor");  
    }
    $this->db = $db;  
  }
  
/* Surveys */
  function add_survey_hit($surveyId)
  {
    $now=date("U"); 	 
    $query = "update tiki_surveys set taken=taken+1, lastTaken=$now where surveyId=$surveyId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);	
  }
  
  function register_survey_text_option_vote($questionId,$value)
  {
    $value=addslashes($value);  	 
    $cant = $this->db->getOne("select count(*) from tiki_survey_question_options where qoption='$value'");	
    if($cant) {
      $query = "update tiki_survey_question_options set votes=votes+1 where questionId=$questionId and	qoption='$value'";
    } else {
      $query = "insert into tiki_survey_question_options(questionId,qoption,votes)
                values($questionId,'$value',1)";
                	
    }
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
  }
  
  function register_survey_rate_vote($questionId,$rate)
  {
    $query = "update tiki_survey_questions set votes=votes+1, value=value+$rate where questionId=$questionId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $query = "update tiki_survey_questions set average=value/votes where questionId=$questionId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    	
  }
  
  function register_survey_option_vote($questionId,$optionId)
  {
    
    $query = "update tiki_survey_question_options set votes=votes+1 where questionId=$questionId and optionId=$optionId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
  }
  
  function clear_survey_stats($surveyId)
  {
    $query = "update tiki_surveys set taken=0 where surveyId=$surveyId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $query = "select * from tiki_survey_questions where surveyId=$surveyId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    // Remove all the options for each question
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {    
      $questionId = $res["questionId"];
      $query2 = "update tiki_survey_question_options set average=0, votes=0 where questionId=$questionId";
      $result2 = $this->db->query($query2);
      if(DB::isError($result2)) $this->sql_error($query2, $result2);
    }
    $query = "update tiki_survey_questions set value=0,votes=0 where surveyId=$surveyId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
  }
  
  function replace_survey($surveyId,$name,$description,$status)
  {
    $name = addslashes($name);
    $description = addslashes($description);
    if($surveyId) {
      // update an existing quiz
      $query = "update tiki_surveys set 
      name = '$name',
      description = '$description',
      status = '$status'
      where surveyId = $surveyId";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query, $result);
    } else {
      // insert a new quiz
      $now = date("U");
      $query = "insert into tiki_surveys(name,description,status,created,taken,lastTaken)
      values('$name','$description','$status',$now,0,$now)";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query, $result);
      $queryid = "select max(surveyId) from tiki_surveys where created=$now";
      $quizId = $this->db->getOne($queryid);  
    }
    return $surveyId;
  }

  function replace_survey_question($questionId,$question,$type,$surveyId,$position,$options)
  {
    $question = addslashes($question);
    $options = addslashes($options);
    if($questionId) {
      // update an existing quiz
      $query = "update tiki_survey_questions set 
      type='$type',
      position = $position,
      question = '$question',
      options = '$options'
      where questionId = $questionId and surveyId=$surveyId";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query, $result);
    } else {
      // insert a new question
      $now = date("U");
      $query = "insert into tiki_survey_questions(question,type,surveyId,position,votes,value,options)
      values('$question','$type',$surveyId,$position,0,0,'$options')";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query, $result);
      $queryid = "select max(questionId) from tiki_survey_questions where question='$question' and type='$type'";
      $questionId = $this->db->getOne($queryid);
    }
    // Now process the question options
    if(!empty($options)) {
      $options = split(',',$options);
    } else {
      $options=Array();
    }
    $query = "select optionId,qoption from tiki_survey_question_options where questionId=$questionId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $ret=Array();
    
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      if(!in_array($res["qoption"],$options)) {
        $query2 = "delete from tiki_survey_question_options where questionId=$questionId and optionId='".$res["optionId"]."'";
        $result2 = $this->db->query($query2);
        if(DB::isError($result2)) $this->sql_error($query2, $result2);
      } else {
        // Since it is in the array removeit from the array
        $idx = array_search($res["qoption"],$options);
        unset($options[$idx]);
      }
    }
    foreach($options as $option) {
      $query = "insert into tiki_survey_question_options (questionId,qoption,votes)
      values($questionId,'$option',0)";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query, $result);
    }
    return $questionId;
  }

  function get_survey($surveyId) 
  {
    $query = "select * from tiki_surveys where surveyId=$surveyId";
    $result = $this->db->query($query);
    if(!$result->numRows()) return false;
    if(DB::isError($result)) $this->sql_error($query, $result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function get_survey_question($questionId) 
  {
    $query = "select * from tiki_survey_questions where questionId=$questionId";
    $result = $this->db->query($query);
    if(!$result->numRows()) return false;
    if(DB::isError($result)) $this->sql_error($query, $result);
    $res2 = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $query = "select * from tiki_survey_question_options where questionId=$questionId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $ret = Array();
    $votes = 0;
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) { 
      $ret[]=$res;
      $votes += $res["votes"];
    }
    $res2["ovotes"]=$votes;
    $res2["qoptions"]=$ret;
    return $res2;
  }
   
  

  function list_survey_questions($surveyId,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
    $mid=" where surveyId=$surveyId and (question like '%".$find."%'";  
    } else {
      $mid=" where surveyId=$surveyId "; 
    }
    $query = "select * from tiki_survey_questions $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_survey_questions $mid";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $questionId=$res["questionId"];
      $res["options"]=$this->db->getOne("select count(*) from tiki_survey_question_options where questionId=".$res["questionId"]);
      $query2 = "select * from tiki_survey_question_options where questionId=$questionId";
      if($res["type"]=='r') {
        $maxwidth=5;
      } else {
        $maxwidth=10;
      }
      $res["width"]=$res["average"]*200 / $maxwidth;
      $result2 = $this->db->query($query2);
      if(DB::isError($result2)) $this->sql_error($query2, $result2);
      $ret2 = Array();
      $votes=0;
      $total_votes = $this->db->getOne("select sum(votes) from tiki_survey_question_options where questionId=$questionId");
      while($res2 = $result2->fetchRow(DB_FETCHMODE_ASSOC)) { 
        if($total_votes) {
          $average = $res2["votes"]/$total_votes;	
        } else {
          $average = 0;	
        }
        $votes += $res2["votes"];
        $res2["average"]=$average;
        $res2["width"]=$average*200;
        $ret2[]=$res2;
      }
      $res["qoptions"]=$ret2;
      $res["ovotes"]=$votes;
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
    $query = "select * from tiki_survey_questions $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_survey_questions $mid";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["options"]=$this->db->getOne("select count(*) from tiki_survey_question_options where questionId=".$res["questionId"]);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  
  function remove_survey_question($questionId)
  {
    $query = "delete from tiki_survey_questions where questionId=$questionId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    // Remove all the options for the question
    $query = "delete from tiki_survey_question_options where questionId=$questionId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    return true;    
  }
    
  function remove_survey($surveyId)
  {
    $query = "delete from tiki_surveys where surveyId=$surveyId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $query = "select * from tiki_survey_questions where surveyId=$surveyId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    // Remove all the options for each question
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {    
      $questionId = $res["questionId"];
      $query2 = "delete from tiki_survey_question_options where questionId=$questionId";
      $result2 = $this->db->query($query2);
      if(DB::isError($result2)) $this->sql_error($query2, $result2);
    }
    // Remove all the questions
    $query = "delete from tiki_survey_questions where surveyId=$surveyId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $this->tikilib->remove_object('survey',$surveyId);
    return true;    
  }
  /* Surveys */
  
}

$srvlib= new SurveyLib($dbTiki);
?>