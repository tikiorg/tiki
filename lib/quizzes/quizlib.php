<?php

class QuizLib extends TikiLib {
	function QuizLib($db) {
		parent::TikiLib($db);
	}

	// Functions for Quizzes ////
	function get_user_quiz_result($userResultId) {
		$query = "select * from `tiki_user_quizzes` where `userResultId`=?";

		$result = $this->query($query,array($userResultId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function list_quiz_question_stats($quizId) {
		$query = "select distinct(tqs.`questionId`) from `tiki_quiz_stats` tqs,`tiki_quiz_questions` tqq where tqs.`questionId`=tqq.`questionId` and tqs.`quizId` = ? order by `position` desc";

		$result = $this->query($query,array($quizId));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$question = $this->getOne("select `question` from `tiki_quiz_questions` where `questionId`=?",array($res["questionId"]));

			$total_votes
				= $this->getOne("select sum(`votes`) from `tiki_quiz_stats` where `quizId`=? and `questionId`=?",array($quizId, $res["questionId"]));
			$query2 = "select tqq.`optionId`,`votes`,`optionText` from `tiki_quiz_stats` tqq,`tiki_quiz_question_options` tqo where tqq.`optionId`=tqo.`optionId` and tqq.`questionId`=?";
			$result2 = $this->query($query2,array($res["questionId"]));
			$options = array();

			while ($res = $result2->fetchRow()) {
				$opt = array();

				$opt["optionText"] = $res["optionText"];
				$opt["votes"] = $res["votes"];
				$opt["avg"] = $res["votes"] / $total_votes * 100;
				$options[] = $opt;
			}

			$ques = array();
			$ques["options"] = $options;
			$ques["question"] = $question;
			$ret[] = $ques;
		}

		return $ret;
	}

	function get_user_quiz_questions($userResultId) {
		$query = "select distinct(tqs.`questionId`) from `tiki_user_answers` tqs,`tiki_quiz_questions` tqq where tqs.`questionId`=tqq.`questionId` and tqs.`userResultId` = ? order by `position` desc";

		$result = $this->query($query,array($userResultId));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$question = $this->getOne("select `question` from `tiki_quiz_questions` where `questionId`=?",array($res["questionId"]));

			$query2 = "select tqq.`optionId`,tqo.`points`,`optionText` from `tiki_user_answers` tqq,`tiki_quiz_question_options` tqo where tqq.`optionId`=tqo.`optionId` and tqq.`userResultId`=? and tqq.`questionId`=?";
			$result2 = $this->query($query2,array($userResultId,$res["questionId"]));
			$options = array();

			while ($res = $result2->fetchRow()) {
				$opt = array();

				$opt["optionText"] = $res["optionText"];
				$opt["points"] = $res["points"];
				$options[] = $opt;
			}

			$ques = array();
			$ques["options"] = $options;
			$ques["question"] = $question;
			$ret[] = $ques;
		}

		return $ret;
	}

	function remove_quiz_stat($userResultId) {
		$query = "select `quizId`,`user` from `tiki_user_quizzes` where `userResultId`=?";
		$bindvars=array($userResultId);

		$result = $this->query($query,$bindvars);
		$res = $result->fetchRow();
		$user = $res["user"];
		$quizId = $res["quizId"];

		$query = "delete from `tiki_user_taken_quizzes` where `user`=? and `quizId`=?";
		$result = $this->query($query,array($user,$quizId));

		$query = "delete from `tiki_user_quizzes` where `userResultId`=?";
		$result = $this->query($query,$bindvars);
		$query = "delete from `tiki_user_answers` where `userResultId`=?";
		$result = $this->query($query,$bindvars);
	}

	function clear_quiz_stats($quizId) {
		$query = "delete from `tiki_user_taken_quizzes` where `quizId`=?";
		$bindvars=array($quizId);

		$result = $this->query($query,$bindvars);

		$query = "delete from `tiki_quiz_stats_sum` where `quizId`=?";
		$result = $this->query($query,$bindvars);

		$query = "delete from `tiki_quiz_stats` where `quizId`=?";
		$result = $this->query($query,$bindvars);

		$query = "delete from `tiki_user_quizzes` where `quizId`=?";
		$result = $this->query($query,$bindvars);

		$query = "delete from `tiki_user_answers` where `quizId`=?";
		$result = $this->query($query,$bindvars);
	}
	function list_quiz_stats($quizId, $offset, $maxRecords, $sort_mode, $find) {
		$this->compute_quiz_stats();

		if ($find) {
			//isnt that superflous? hmm.
			$findesc = '%' . $find . '%';
			
			$mid = " where `quizId`=?";
			$bindvars=array($quizId);
		} else {
			$mid = " where `quizId`=?";
			$bindvars=array($quizId);
		}

		$query = "select * from `tiki_user_quizzes` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_user_quizzes` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["avgavg"] = ($res["maxPoints"] != 0) ? $res["points"] / $res["maxPoints"] * 100 : 0.0;

			$hasDet = $this->getOne("select count(*) from `tiki_user_answers` where `userResultId`=?",array($res["userResultId"]));

			if ($hasDet) {
				$res["hasDetails"] = 'y';
			} else {
				$res["hasDetails"] = 'n';
			}

			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function register_user_quiz_answer($userResultId, $quizId, $questionId, $optionId) {
		$query = "insert into `tiki_user_answers`(`userResultId`,`quizId`,`questionId`,`optionId`)
    values(?,?,?,?)";

		$result = $this->query($queryi,array($userResultId,$quizId,$questionId,$optionId));
	}

	function register_quiz_stats($quizId, $user, $timeTaken, $points, $maxPoints, $resultId) {
		$now = date("U");

		// Fix a bug if no result is indicated.
		if (!$resultId)
			$resultId = 0;

		$query = "insert into `tiki_user_quizzes`(`user`,`quizId`,`timestamp`,`timeTaken`,`points`,`maxPoints`,`resultId`)
    values(?,?,?,?,?,?,?)";
		$result = $this->query($query,array($user,$quizId,$now,$timeTaken,$points,$maxPoints,$resultId));
		$queryId = $this->getOne("select max(`userResultId`) from `tiki_user_quizzes` where `timestamp`=? and `quizId`=?",array($now,$quizId));
		return $queryId;
	}

	function register_quiz_answer($quizId, $questionId, $optionId) {
		$cant = $this->getOne(
			"select count(*) from `tiki_quiz_stats` where `quizId`=? and `questionId`=? and `optionId`=?",array($quizId,$questionId,$optionId));

		if ($cant) {
			$query
				= "update `tiki_quiz_stats` set `votes`=`votes`+1 where `quizId`=? and `questionId`=? and `optionId`=?";
			$bindvars=array($quizId,$questionId,$optionId);
		} else {
			$query = "insert into `tiki_quiz_stats`(`quizId`,`questionId`,`optionId`,`votes`)
      				values(?,?,?,?)";
			$bindvars=array($quizId,$questionId,$optionId,1);
		}

		$result = $this->query($query,$bindvars);

		return true;
	}

	function calculate_quiz_result($quizId, $points) {
		$query = "select * from `tiki_quiz_results` where `fromPoints`<=? and `toPoints`>=? and `quizId`=?";

		$result = $this->query($query,array($points,$points,$quizId));

		if (!$result->numRows())
			return 0;

		$res = $result->fetchRow();
		return $res;
	}

	function user_has_taken_quiz($user, $quizId) {
		$cant = $this->getOne("select count(*) from `tiki_user_taken_quizzes` where `user`=? and `quizId`=?",array($user,$quizId));

		return $cant;
	}

	function user_takes_quiz($user, $quizId) {
		$query = "delete from `tiki_user_taken_quizzes` where `user`=? and `quizId`=?";
		$bindvars=array($user,$quizId,-1,-1,false);
		$result = $this->query($query,$bindvars);
		$query = "insert into `tiki_user_taken_quizzes`(`user`,`quizId`) values(?,?)";
		$result = $this->query($query,$bindvars);
	}

	function replace_quiz_result($resultId, $quizId, $fromPoints, $toPoints, $answer) {

		if ($resultId) {
			// update an existing quiz
			$query = "update `tiki_quiz_results` set 
      `fromPoints` = ?,
      `toPoints` = ?,
      `quizId` = ?,
      `answer` = ?
      where `resultId` = ?";
      			$bindvars=array($fromPoints,$toPoints,$quizId,$answer,$resultId);
			$result = $this->query($query,$bindvars);
		} else {
			// insert a new quiz
			$now = date("U");

			$query = "insert into `tiki_quiz_results`(`quizId`,`fromPoints`,`toPoints`,`answer`)
      				values(?,?,?,?)";
			$bindvars=array($quizId,$fromPoints,$toPoints,$answer);
			$result = $this->query($query,$bindvars);
			$queryid = "select max(`resultId`) from `tiki_quiz_results` where `fromPoints`=? and `toPoints`=? and `quizId`=?";
			$quizId = $this->getOne($queryid,array($fromPoints,$toPoints,$quizId));
		}

		return $quizId;
	}

	function get_quiz_result($resultId) {
		$query = "select * from `tiki_quiz_results` where `resultId`=?";

		$result = $this->query($query,array($resultId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function remove_quiz_result($resultId) {
		$query = "delete from `tiki_quiz_results` where `resultId`=?";

		$result = $this->query($query,array($resultId));
		return true;
	}

	function list_quiz_results($quizId, $offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `quizId`=? and `question` like ? ";
			$bindvars=array($quizId,$findesc);
		} else {
			$mid = " where `quizId`=? ";
			$bindvars=array($quizId);
		}

		$query = "select * from `tiki_quiz_results` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_quiz_results` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function replace_quiz($quizId, $name, $description, $canRepeat, $storeResults, $questionsPerPage, $timeLimited, $timeLimit) {
		if ($quizId) {
			// update an existing quiz
			$query = "update `tiki_quizzes` set 
      `name` = ?,
      `description` = ?,
      `canRepeat` = ?,
      `storeResults` = ?,
      `questionsPerPage` = ?,
      `timeLimited` = ?,
      `timeLimit` =? 
      where `quizId` = ?";
			$bindvars=array($name,$description,$canRepeat,$storeResults,$questionsPerPage,$timeLimited,$timeLimit,$quizId);
			$result = $this->query($query,$bindvars);
		} else {
			// insert a new quiz
			$now = date("U");

			$query = "insert into `tiki_quizzes`(`name`,`description`,`canRepeat`,`storeResults`,`questionsPerPage`,`timeLimited`,`timeLimit`,`created`,`taken`)
      values(?,?,?,?,?,?,?,?,?)";
      			$bindvars=array($name,$description,$canRepeat,$storeResults,(int) $questionsPerPage,$timeLimited,(int) $timeLimit,(int) $now,0);
			$result = $this->query($query,$bindvars);
			$queryid = "select max(`quizId`) from `tiki_quizzes` where `created`=?";
			$quizId = $this->getOne($queryid,array((int) $now));
		}

		return $quizId;
	}

	function replace_quiz_question($questionId, $question, $type, $quizId, $position) {
		if ($questionId) {
			// update an existing quiz
			$query = "update `tiki_quiz_questions` set 
      `type`=?,
      `position` = ?,
      `question` = ?
      where `questionId` = ? and `quizId`=?";

			$bindvars=array($type,$position,$question,$questionId,$quizId);
			$result = $this->query($query,$bindvars);
		} else {
			// insert a new quiz
			$now = date("U");

			$query = "insert into `tiki_quiz_questions`(`question`,`type`,`quizId`,`position`)
      values(?,?,?,?)";
      			$bindvars=array($question,$type,$quizId,$position);
			$result = $this->query($query,$bindvars);
			$queryid = "select max(`questionId`) from `tiki_quiz_questions` where `question`=? and type=?";
			$questionId = $this->getOne($queryid,array($question,$type));
		}

		return $questionId;
	}

	function replace_question_option($optionId, $option, $points, $questionId) {

		// validating the points value
		if ((!is_numeric($points)) || ($points == ""))
			$points = 0;

		if ($optionId) {
			// update an existing quiz
			$query = "update `tiki_quiz_question_options` set 
      `points`=?,
      `optionText` = ?
      where `optionId` = ? and `questionId`=?";
      			$bindvars=array($points,$option,$optionId,$questionId);

			$result = $this->query($query,$bindvars);
		} else {
			// insert a new quiz
			$now = date("U");

			$query = "insert into `tiki_quiz_question_options`(`optionText`,`points`,`questionId`)
      values(?,?,?)";
			$result = $this->query($query,array($option,$points,$questionId));
			$queryid = "select max(`optionId`) from `tiki_quiz_question_options` where `optionText`=? and `questionId`=?";
			$optionId = $this->getOne($queryid,array($option,$questionId));
		}

		return $optionId;
	}

	function get_quiz_question($questionId) {
		$query = "select * from `tiki_quiz_questions` where `questionId`=?";

		$result = $this->query($query,array($questionId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function get_quiz_question_option($optionId) {
		$query = "select * from `tiki_quiz_question_options` where `optionId`=?";

		$result = $this->query($query,array($optionId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function list_quiz_questions($quizId, $offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `quizId`=? and `question` like ? ";
			$bindvars=array((int) $quizId,$findesc);
		} else {
			$mid = " where `quizId`=? ";
			$bindvars=array((int) $quizId);
		}

		$query = "select * from `tiki_quiz_questions` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_quiz_questions` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["options"]
				= $this->getOne("select count(*) from `tiki_quiz_question_options` where `questionId`=?",array($res["questionId"]));

			$res["maxPoints"]
				= $this->getOne("select max(`points`) from `tiki_quiz_question_options` where `questionId`=?",array($res["questionId"]));
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_all_questions($offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `question` like ? ";
			$bindvars=array($findesc);
		} else {
			$mid = " ";
			$bindvars=array();
		}

		$query = "select * from `tiki_quiz_questions` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_quiz_questions` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["options"]
				= $this->getOne("select count(*) from `tiki_quiz_question_options` where `questionId`=?",array($res["questionId"]));

			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_quiz_question_options($questionId, $offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `questionId`=? and `option` ? ";
			$bindvars=array($questionId,$findesc);
		} else {
			$mid = " where `questionId`=? ";
			$bindvars=array($questionId);
		}

		$query = "select * from `tiki_quiz_question_options` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_quiz_question_options` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function remove_quiz_question($questionId) {
		$query = "delete from `tiki_quiz_questions` where `questionId`=?";

		$result = $this->query($query,array($questionId));
		// Remove all the options for the question
		$query = "delete from `tiki_quiz_question_options` where `questionId`=?";
		$result = $this->query($query,array($questionId));
		return true;
	}

	function remove_quiz_question_option($optionId) {
		$query = "delete from `tiki_quiz_question_options` where `optionId`=?";

		$result = $this->query($query,array($optionId));
		return true;
	}

	function remove_quiz($quizId) {
		$query = "delete from `tiki_quizzes` where `quizId`=?";

		$result = $this->query($query,array($quizId));
		$query = "select * from `tiki_quiz_questions` where `quizId`=?";
		$result = $this->query($query,array($quizId));

		// Remove all the options for each question
		while ($res = $result->fetchRow()) {
			$questionId = $res["questionId"];

			$query2 = "delete from `tiki_quiz_question_options` where `questionId`=?";
			$result2 = $this->query($query2,array($questionId));
		}

		// Remove all the questions
		$query = "delete from `tiki_quiz_questions` where `quizId`=?";
		$result = $this->query($query,array($quizId));
		$query = "delete from `tiki_quiz_results` where `quizId`=?";
		$result = $this->query($query,array($quizId));
		$query = "delete from `tiki_quiz_stats` where `quizId`=?";
		$result = $this->query($query,array($quizId));
		$query = "delete from `tiki_user_quizzes` where `quizId`=?";
		$result = $this->query($query,array($quizId));
		$query = "delete from `tiki_user_answers` where `quizId`=?";
		$result = $this->query($query,array($quizId));
		$this->remove_object('quiz', $quizId);
		return true;
	}

// Function for Quizzes end ////
}

$quizlib = new QuizLib($dbTiki);

?>
