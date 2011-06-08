<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class SurveyLib extends TikiLib
{
	function add_survey_hit($surveyId)
	{
		global $prefs, $user;

		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			$query = "update `tiki_surveys` set `taken`=`taken`+1, `lastTaken`=? where `surveyId`=?";
			$result = $this->query($query, array((int)$this->now, (int)$surveyId));
		}
	}

	function register_survey_text_option_vote($questionId, $value)
	{
		$bindvars = array((int)$questionId, $value);
		$cant = $this->getOne("select count(*) from `tiki_survey_question_options` where `questionId`=? and `qoption`=?", $bindvars);
		if ($cant) {
			$query = "update `tiki_survey_question_options` set `votes`=`votes`+1 where `questionId`=? and `qoption`=?";
		} else {
			$query = "insert into `tiki_survey_question_options`(`questionId`,`qoption`,`votes`) values(?,?,1)";
		}
		$result = $this->query($query, $bindvars);
	}

	function register_survey_rate_vote($questionId, $rate)
	{
		$query = "update `tiki_survey_questions` set `votes`=`votes`+1, `value`=`value` + ? where `questionId`=?";
		$result = $this->query($query, array((int)$rate,(int)$questionId));
		$query = "update `tiki_survey_questions` set `average`=`value`/`votes` where `questionId`=?";
		$result = $this->query($query, array((int)$questionId));
	}

	function register_survey_option_vote($questionId, $optionId)
	{
		$query = "update `tiki_survey_question_options` set `votes`=`votes`+1 where `questionId`=? and `optionId`=?";
		$result = $this->query($query, array((int)$questionId, (int)$optionId));
	}

	function clear_survey_stats($surveyId)
	{
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

		$query = 'delete from `tiki_user_votings` where `id`=?';
		$result = $this->query($query, array('survey'.(int)$surveyId));
	}

	function replace_survey($surveyId, $name, $description, $status)
	{
		if ($surveyId) {
			$query = "update `tiki_surveys` set `name`=?, `description`=?, `status`=? where `surveyId`=?";
			$result = $this->query($query, array($name, $description, $status,(int)$surveyId));
		} else {
			$query = "insert into `tiki_surveys`(`name`,`description`,`status`,`created`,`taken`,`lastTaken`) values(?,?,?,?,0,?)";
			$result = $this->query($query, array($name, $description, $status,(int)$this->now, (int)$this->now));

			$queryid = "select max(`surveyId`) from `tiki_surveys` where `created`=?";
			$surveyId = $this->getOne($queryid, array((int)$this->now));
		}
		return $surveyId;
	}

	function replace_survey_question($questionId, $question, $type
		, $surveyId, $position, $options, $mandatory = 'n'
		, $min_answers = 0, $max_answers = 0
	) {
		if ($mandatory != 'y')
			$mandatory = 'n';
		$min_answers = (int)$min_answers;
		$max_answers = (int)$max_answers;

		if ($questionId) {

			$query = "update `tiki_survey_questions` set `type`=?, `position`=?, `question`=?, `options`=?, `mandatory`=?, `min_answers`=?, `max_answers`=? where `questionId`=? and `surveyId`=?";
			$result = $this->query($query, array($type, (int)$position, $question, $options, $mandatory, (int)$min_answers, (int)$max_answers, (int)$questionId, (int)$surveyId));

		} else {

			$query = "insert into `tiki_survey_questions` (`question`,`type`,`surveyId`,`position`,`votes`,`value`,`options`,`mandatory`,`min_answers`,`max_answers`) values(?,?,?,?,0,0,?,?,?,?)";
			$result = $this->query($query, array($question, $type, (int)$surveyId, (int)$position, $options, $mandatory, (int)$min_answers, (int)$max_answers));

			$queryid = "select max(`questionId`) from `tiki_survey_questions` where `question`=? and `type`=?";
			$questionId = $this->getOne($queryid, array($question, $type));

		}

		if (!empty($options)) {
			$options = explode(',', $options);
		} else {
			$options = array();
		}

		$query = "select `optionId`,`qoption` from `tiki_survey_question_options` where `questionId`=?";
		$result = $this->query($query, array((int)$questionId));
		$ret = array();

		// Reset question options only if not a 'text' or 'filegal choice', because their options are dynamically generated
		if ( ! in_array($type, array('t', 'g')) ) {
			while ($res = $result->fetchRow()) {
				if (! in_array($res["qoption"], $options)) {
					$query2 = "delete from `tiki_survey_question_options` where `questionId`=? and `optionId`=?";
					$result2 = $this->query($query2, array((int)$questionId,(int)$res["optionId"]));
				} else {
					$idx = array_search($res["qoption"], $options);
					unset ($options[$idx]);
				}
			}
			foreach ($options as $option) {
				$query = "insert into `tiki_survey_question_options` (`questionId`,`qoption`,`votes`) values(?,?,0)";
				$result = $this->query($query, array((int)$questionId, $option));
			}
		}

		return $questionId;
	}

	function get_survey($surveyId)
	{
		$query = "select * from `tiki_surveys` where `surveyId`=?";
		$result = $this->query($query, array((int)$surveyId));
		if ( ! $result->numRows() )
			return false;
		$res = $result->fetchRow();
		return $res;
	}

	function get_survey_question($questionId)
	{
		$query = "select * from `tiki_survey_questions` where `questionId`=?";
		$result = $this->query($query, array((int)$questionId));
		if (!$result->numRows())
			return false;

		$res2 = $result->fetchRow();
		$query = "select * from `tiki_survey_question_options` where `questionId`=?";
		$result = $this->query($query, array((int)$questionId));
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

	function list_survey_questions($surveyId, $offset, $maxRecords, $sort_mode, $find)
	{
		global $tikilib;
		$filegallib = TikiLib::lib('filegal');
		$bindvars = array((int) $surveyId);
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where `surveyId`=? and (`question` like ?)";
			$bindvars[] = $findesc;
		} else {
			$mid = " where `surveyId`=?";
		}

		$query = "select * from `tiki_survey_questions` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_survey_questions` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {

			// save user options
			$options = explode(",", $res["options"]);

			$questionId = $res["questionId"];
			if ( ! empty($res['options']) ) {
				if (in_array($res['type'], array('g', 'x'))) {
					$res['explode'] = $options;
				} elseif (in_array($res['type'], array('r', 's')) ) {
					$res['explode'] = array_fill(1, $res['options'], ' ');
				}
			}
			
			$res["options"] = $this->getOne("select count(*) from `tiki_survey_question_options` where `questionId`=?", array((int)$res["questionId"]));
			$query2 = "select * from `tiki_survey_question_options` where `questionId`=? order by "
				. ($res['type'] == 'g' ? '`votes` desc' : '`optionId`');

			if ($res["type"] == 'r') {
				$maxwidth = 5;
			} else {
				$maxwidth = 10;
			}
			$res["width"] = $res["average"] * 200 / $maxwidth;
			$result2 = $this->query($query2, array((int)$questionId));
			$ret2 = array();
			$votes = 0;
			$total_votes = $this->getOne("select sum(`votes`) as sum from `tiki_survey_question_options` where `questionId`=?", array((int)$questionId));
			
			// store user defined options indices
			$opts = array();
			for ($cpt = 0, $cpecount_options = count($options);  $cpt < $cpecount_options; $cpt++) {
				$opts[$options[$cpt]] = $cpt; 
			}

			$ids = array();
			while ($res2 = $result2->fetchRow()) {

				if ($total_votes) {
					$average = ($res2["votes"] / $total_votes)*100;
				} else {
					$average = 0;
				}

				$votes += $res2["votes"];
				$res2["average"] = $average;
				$res2["width"] = $average * 2;
				if ($res['type'] == 'x') {
					$res2['qoption'] = $tikilib->parse_data($res2['qoption']);
				}
				
				// when question with multiple options
				// we MUST respect the user defined order
				if (in_array($res['type'], array('m', 'c'))) {
					$ret2[$opts[$res2['qoption']]] = $res2;
				} else {
					$ret2[] = $res2;
				}

				$ids[$res2['qoption']] = true;
			}
		
			// For a multiple choice from a file gallery, show all files in the stats results, even if there was no vote for those files
			if ($res['type'] == 'g' && $res['options'] > 0) {
				$files = $filegallib->get_files(0, -1, '', '', $options[0], false, false, false, true, false, false, false, false, '', false, false);
				foreach ($files['data'] as $f) {
					if ( ! isset($ids[$f['id']]) ) {
						$ret2[] = array(
							'qoption' => $f['id'],
							'votes' => 0,
							'average' => 0,
							'width' => 0
						);
					}
				}
				unset($files);
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

	function list_all_questions($offset, $maxRecords, $sort_mode, $find)
	{
		$bindvars = array();
		if ($find) {
			$mid = " where `question` like ?";
			$bindvars[] = '%' . $find . '%';
		} else {
			$mid = " ";
		}

		$query = "select * from `tiki_survey_questions` $mid order by ".$this->convertSortMode("$sort_mode");
		$query_cant = "select count(*) from `tiki_survey_questions` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["options"] = $this->getOne("select count(*) from `tiki_survey_question_options` where `questionId`=?", array((int)$res["questionId"]));
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function remove_survey_question($questionId)
	{
		$query = "delete from `tiki_survey_questions` where `questionId`=?";
		$result = $this->query($query, array((int)$questionId));
		$query = "delete from `tiki_survey_question_options` where `questionId`=?";
		$result = $this->query($query, array((int)$questionId));
		return true;
	}

	function remove_survey($surveyId)
	{
		$query = "delete from `tiki_surveys` where `surveyId`=?";
		$result = $this->query($query, array((int)$surveyId));
		$query = "select * from `tiki_survey_questions` where `surveyId`=?";
		$result = $this->query($query, array((int)$surveyId));

		while ($res = $result->fetchRow()) {
			$questionId = $res["questionId"];
			$query2 = "delete from `tiki_survey_question_options` where `questionId`=?";
			$result2 = $this->query($query2, array((int)$questionId));
		}
		$query = "delete from `tiki_survey_questions` where `surveyId`=?";
		$result = $this->query($query, array((int)$surveyId));
		$this->remove_object('survey', $surveyId);

		$query = 'delete from `tiki_user_votings` where `id`=?';
		$result = $this->query($query, array('survey'.(int)$surveyId));

		return true;
	}

	// Check mandatory fields and min/max number of answers and register vote/answers if ok
	function register_answers($surveyId, $questions, $answers, &$error_msg = null)
	{
		if ($surveyId <= 0 || empty($questions))
			return false;

		$errors = array();
		foreach ($questions as $question) {
			$key = 'question_'.$question['questionId'];
			$nb_answers = empty($answers[$key]) ? 0 : 1;
			$multiple_choice = in_array($question['type'], array('m', 'g'));
			if ($multiple_choice) {
				$nb_answers = is_array($answers[$key]) ? count($answers[$key]) : 0;
				if ($question['max_answers'] < 1)
					$question['max_answers'] = $nb_answers;
			}
			$q = empty($question['question']) ? '.' : ' "<b>'.$question['question'].'</b>".';
			if ($multiple_choice) {
				if ($question['mandatory'] == 'y')
					$question['min_answers'] = max(1, $question['min_answers']);
				
				if ($question['min_answers'] == $question['max_answers'] && $nb_answers != $question['min_answers']) {
					$errors[] = sprintf(tra('You have to make %d choice(s) for the question'), $question['min_answers']).$q;
				} elseif ($nb_answers < $question['min_answers']) {
					$errors[] = sprintf(tra('You have to make at least %d choice(s) for the question'), $question['min_answers']).$q;
				} elseif ($question['max_answers'] > 0 && $nb_answers > $question['max_answers']) {
					$errors[] = sprintf(tra('You have to make less than %d choice(s) for the question'), $question['max_answers']).$q;
				}
			} elseif ($question['mandatory'] == 'y' && $nb_answers == 0) {
				$errors[] = sprintf(tra('You have to choose at least %d choice(s) for the question'), 1).$q;
			}
		}
	
		if (count($errors) > 0) {
			if ($error_msg !== null) {
				$error_msg = implode('<br />', $errors);
			}
			return false;
		} else {
			foreach ($questions as $question) {
				$questionId = $question["questionId"];
	
				if (isset($answers["question_" . $questionId])) {
					if ($question["type"] == 'm') {
	
						// If we have a multiple question
						$ids = array_keys($answers["question_" . $questionId]);
	
						// Now for each of the options we increase the number of votes
						foreach ($ids as $optionId) {
							$this->register_survey_option_vote($questionId, $optionId);
						}
	
					} elseif ($question["type"] == 'g') {

						// If we have a multiple choice of file from a gallery
						$ids = $answers["question_" . $questionId];
	
						// Now for each of the options we increase the number of votes
						foreach ($ids as $optionId) {
							$this->register_survey_text_option_vote($questionId, $optionId);
						}
	
					} else {
						$value = $answers["question_" . $questionId];
	
						if ($question["type"] == 'r' || $question["type"] == 's') {
							$this->register_survey_rate_vote($questionId, $value);
						} elseif ($question["type"] == 't' || $question["type"] == 'x') {
							$this->register_survey_text_option_vote($questionId, $value);
						} else {
							$this->register_survey_option_vote($questionId, $value);
						}
					}
				}
			}
		}

		global $user;
		$this->register_user_vote($user, 'survey' . $surveyId, 0);

		return true;
	}
}
$srvlib = new SurveyLib;
