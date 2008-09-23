<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class SurveyLib extends TikiLib 
{
	function SurveyLib($db) {
		parent::TikiLib($db);
	}

	function add_survey_hit($surveyId) {
		global $prefs, $user;

		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			$query = "update `tiki_surveys` set `taken`=`taken`+1, `lastTaken`=? where `surveyId`=?";
			$result = $this->query($query,array((int)$this->now,(int)$surveyId));
		}
	}

	function register_survey_text_option_vote($questionId, $value) {
		$cant = $this->getOne("select count(*) from `tiki_survey_question_options` where `qoption`=?",array($value));
		if ($cant) {
			$query = "update `tiki_survey_question_options` set `votes`=`votes`+1 where `questionId`=? and `qoption`=?";
		} else {
			$query = "insert into `tiki_survey_question_options`(`questionId`,`qoption`,`votes`) values(?,?,1)";
		}
		$result = $this->query($query, array((int)$questionId,$value));
	}

	function register_survey_rate_vote($questionId, $rate) {
		$query = "update `tiki_survey_questions` set `votes`=`votes`+1, `value`=`value` + ? where `questionId`=?";
		$result = $this->query($query, array((int)$rate,(int)$questionId));
		$query = "update `tiki_survey_questions` set `average`=`value`/`votes` where `questionId`=?";
		$result = $this->query($query, array((int)$questionId));
	}

	function register_survey_option_vote($questionId, $optionId) {
		$query = "update `tiki_survey_question_options` set `votes`=`votes`+1 where `questionId`=? and `optionId`=?";
		$result = $this->query($query, array((int)$questionId,(int)$optionId));
	}

	function clear_survey_stats($surveyId) {
		$query = "update `tiki_surveys` set `taken`=0 where `surveyId`=?";
		$result = $this->query($query, array((int)$surveyId));
		$query = "select * from `tiki_survey_questions` where `surveyId`=?";
		$result = $this->query($query, array((int)$surveyId));

		// Remove all the options for each question
		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$questionId = $res["questionId"];
			$query2 = "update `tiki_survey_question_options` set `votes`=0 where `questionId`=?";
			$result2 = $this->query($query2, array((int)$questionId));
		}

		$query = "update `tiki_survey_questions` set `average`=0, `value`=0, `votes`=0 where `surveyId`=?";
		$result = $this->query($query, array((int)$surveyId));
	}

	function replace_survey($surveyId, $name, $description, $status) {
		if ($surveyId) {
			$query = "update `tiki_surveys` set `name`=?, `description`=?, `status`=? where `surveyId`=?";
			$result = $this->query($query,array($name,$description,$status,(int)$surveyId));
		} else {
			$query = "insert into `tiki_surveys`(`name`,`description`,`status`,`created`,`taken`,`lastTaken`) values(?,?,?,?,0,?)";
			$result = $this->query($query,array($name,$description,$status,(int)$this->now,(int)$this->now));

			$queryid = "select max(`surveyId`) from `tiki_surveys` where `created`=?";
			$surveyId = $this->getOne($queryid,array((int)$this->now));
		}
		return $surveyId;
	}

	function replace_survey_question($questionId, $question, $type, $surveyId, $position, $options) {
		//$question = addslashes($question);

		//$options = addslashes($options);

		if ($questionId) {
			$query = "update `tiki_survey_questions` set `type`=?, `position`=?, `question`=?, `options`=? where `questionId`=? and `surveyId`=?";

			$result = $this->query($query, array($type,(int) $position,$question,$options,(int)$questionId,(int)$surveyId));
		} else {

			$query = "insert into `tiki_survey_questions`(`question`,`type`,`surveyId`,`position`,`votes`,`value`,`options`) values(?,?,?,?,0,0,?)";
			$result = $this->query($query,array($question,$type,(int)$surveyId,(int) $position,$options));

			$queryid = "select max(`questionId`) from `tiki_survey_questions` where `question`=? and `type`=?";
			$questionId = $this->getOne($queryid,array($question,$type));
		}

		if (!empty($options)) {
			$options = split(',', $options);
		} else {
			$options = array();
		}

		$query = "select `optionId`,`qoption` from `tiki_survey_question_options` where `questionId`=?";
		$result = $this->query($query,array((int)$questionId));
		$ret = array();

		while ($res = $result->fetchRow()) {
			if (!in_array($res["qoption"], $options)) {
				$query2 = "delete from `tiki_survey_question_options` where `questionId`=? and `optionId`=?";
				$result2 = $this->query($query2, array((int)$questionId,(int)$res["optionId"]));
			} else {
				$idx = array_search($res["qoption"], $options);
				unset ($options[$idx]);
			}
		}

		foreach ($options as $option) {
			$query = "insert into `tiki_survey_question_options` (`questionId`,`qoption`,`votes`) values(?,?,0)";
			$result = $this->query($query,array((int)$questionId,$option));
		}
		return $questionId;
	}

	function get_survey($surveyId) {
		$query = "select * from `tiki_surveys` where `surveyId`=?";
		$result = $this->query($query,array((int)$surveyId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function get_survey_question($questionId) {
		$query = "select * from `tiki_survey_questions` where `questionId`=?";
		$result = $this->query($query,array((int)$questionId));
		if (!$result->numRows()) return false;

		$res2 = $result->fetchRow();
		$query = "select * from `tiki_survey_question_options` where `questionId`=?";
		$result = $this->query($query,array((int)$questionId));
		$ret = array();
		$votes = 0;

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
			$votes += $res["votes"];
		}

		$res2["ovotes"] = $votes;
		$res2["qoptions"] = $ret;
		return $res2;
	}

	function list_survey_questions($surveyId, $offset, $maxRecords, $sort_mode, $find) {
		global $tikilib;
		$bindvars = array((int) $surveyId);
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where `surveyId`=? and (`question` like ?)";
			$bindvars[] = $findesc;
		} else {
			$mid = " where `surveyId`=?";
		}

		$query = "select * from `tiki_survey_questions` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_survey_questions` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$questionId = $res["questionId"];
			if (!empty($res['options']) &&  ($res["type"] == 'r' || $res["type"] == 's') )
				$res['explode'] = array_fill(1,$res['options'], " "); 

			// save user options
			$options = explode( ",", $res["options"] );
			
			$res["options"] = $this->getOne("select count(*) from `tiki_survey_question_options` where `questionId`=?",array((int)$res["questionId"]));
			$query2 = "select * from `tiki_survey_question_options` where `questionId`=? order by `optionId`";

			if ($res["type"] == 'r') {
				$maxwidth = 5;
			} else {
				$maxwidth = 10;
			}
			$res["width"] = $res["average"] * 200 / $maxwidth;
			$result2 = $this->query($query2,array((int)$questionId));
			$ret2 = array();
			$votes = 0;
			$total_votes = $this->getOne("select sum(`votes`) from `tiki_survey_question_options` where `questionId`=?",array((int)$questionId));
			
			// store user defined options indices
			$opts = array();
			for( $cpt = 0;  $cpt < count($options); $cpt++  ) {
				$opts[$options[$cpt]] = $cpt; 
			}

			while ($res2 = $result2->fetchRow()) {
				if ($total_votes) {
					$average = ($res2["votes"] / $total_votes)*100;
				} else {
					$average = 0;
				}

				$votes += $res2["votes"];
				$res2["average"] = $average;
				$res2["width"] = $average * 2;
				if ($res['type'] == 'x') 
					$res2['qoption'] = $tikilib->parse_data($res2['qoption']);
				
				// when question with multiple options
				// we MUST respect the user defined order
				if( in_array($res['type'], array('m', 'c'))  ) {
					$ret2[$opts[$res2['qoption']]] = $res2;
				}
				else {
					$ret2[] = $res2;
				}
			}

			$res["qoptions"] = $ret2;
			$res["ovotes"] = $votes;
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_all_questions($offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array();
		if ($find) {
			$mid = " where `question` like ?";
			$bindvars[] = '%' . $find . '%';
		} else {
			$mid = " ";
		}

		$query = "select * from `tiki_survey_questions` $mid order by ".$this->convert_sortmode("$sort_mode");
		$query_cant = "select count(*) from `tiki_survey_questions` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["options"] = $this->getOne("select count(*) from `tiki_survey_question_options` where `questionId`=?",array((int)$res["questionId"]));
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function remove_survey_question($questionId) {
		$query = "delete from `tiki_survey_questions` where `questionId`=?";
		$result = $this->query($query,array((int)$questionId));
		$query = "delete from `tiki_survey_question_options` where `questionId`=?";
		$result = $this->query($query,array((int)$questionId));
		return true;
	}

	function remove_survey($surveyId) {
		$query = "delete from `tiki_surveys` where `surveyId`=?";
		$result = $this->query($query,array((int)$surveyId));
		$query = "select * from `tiki_survey_questions` where `surveyId`=?";
		$result = $this->query($query,array((int)$surveyId));

		while ($res = $result->fetchRow()) {
			$questionId = $res["questionId"];
			$query2 = "delete from `tiki_survey_question_options` where `questionId`=?";
			$result2 = $this->query($query2,array((int)$questionId));
		}
		$query = "delete from `tiki_survey_questions` where `surveyId`=?";
		$result = $this->query($query,array((int)$surveyId));
		$this->remove_object('survey', $surveyId);
		return true;
	}
}
global $dbTiki;
$srvlib = new SurveyLib($dbTiki);

?>
