<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 *
 */
class QuizLib extends TikiLib
{
    /**
     * @param $quizId
     * @return bool
     */
    public function get_quiz($quizId)
	{
		$query = "select * from `tiki_quizzes` where `quizId`=?";

		$result = $this->query($query, array((int) $quizId));

		if (!$result->numRows()) {
			return false;
		}

		$res = $result->fetchRow();
		return $res;
	}

	public function compute_quiz_stats()
	{
		$query = "select `quizId`  from `tiki_user_quizzes`";

		$result = $this->fetchAll($query, array());

		$quizStatsSum = $this->table('tiki_quiz_stats_sum');

		foreach ($result as $res) {
			$quizId = $res["quizId"];

			$quizName = $this->getOne("select `name`  from `tiki_quizzes` where `quizId`=?", array((int) $quizId));
			$timesTaken = $this->getOne("select count(*) from `tiki_user_quizzes` where `quizId`=?", array((int) $quizId));
			$avgpoints = $this->getOne("select avg(`points`) from `tiki_user_quizzes` where `quizId`=?", array((int) $quizId));
			$maxPoints = $this->getOne("select max(`maxPoints`) from `tiki_user_quizzes` where `quizId`=?", array((int) $quizId));
			$avgavg = ($maxPoints != 0) ? $avgpoints / $maxPoints * 100 : 0.0;
			$avgtime = $this->getOne("select avg(`timeTaken`) from `tiki_user_quizzes` where `quizId`=?", array((int) $quizId));

			$quizStatsSum->delete(array('quizId' => (int) $quizId,));
			$quizStatsSum->insert(
				array(
					'quizId' => (int) $quizId,
					'quizName' => $quizName,
					'timesTaken' => (int) $timesTaken,
					'avgpoints' => (float) $avgpoints,
					'avgtime' => $avgtime,
					'avgavg' => $avgavg,
				)
			);
		}
	}

    /**
     * @param $offset
     * @param $maxRecords
     * @param string $sort_mode
     * @param null $find
     * @return array
     */
    public function list_quizzes($offset, $maxRecords, $sort_mode = 'name_desc', $find = null)
	{

		$quizzes = $this->table('tiki_quizzes');
		$conditions = array();

		if ( ! empty($find) ) {
			$findesc = '%' . $find . '%';
			$conditions['search'] = $quizzes->expr('(`name` like ? or `description` like ?)', array($findesc, $findesc));
		}

		$result = $quizzes->fetchColumn('quizId', $conditions);
		$res = $ret = $retids = array();
		$n = 0;

		//FIXME Perm:filter ?
		foreach ($result as $res) {
			$objperm = Perms::get('quizzes', $res);

			if ( $objperm->take_quiz ) {
				if ( ($maxRecords == -1) || (($n >= $offset) && ($n < ($offset + $maxRecords))) ) {
					$retids[] = $res;
				}
				$n++;
			}
		}

		if ($n > 0) {
			$result = $quizzes->fetchAll(
				$quizzes->all(),
				array('quizId' => $quizzes->in($retids)),
				-1, -1, $quizzes->expr($this->convertSortMode($sort_mode))
			);

			$questions = $this->table('tiki_quiz_questions');
			$results = $this->table('tiki_quiz_results');

			foreach ($result as $res) {
				$res['questions'] = $questions->fetchCount(array('quizId' => (int) $res['quizId']));
				$res['results'] = $results->fetchCount(array('quizId' => (int) $res['quizId']));
				$ret[] = $res;
			}
		}

		return array(
			'data' => $ret,
			'cant' => $n,
		);
	}

    /**
     * @param $userResultId
     * @return bool
     */
    public function get_user_quiz_result($userResultId)
	{
		$query = "select * from `tiki_user_quizzes` where `userResultId`=?";

		$result = $this->query($query, array($userResultId));

		if (!$result->numRows()) {
			return false;
		}

		$res = $result->fetchRow();
		return $res;
	}

    /**
     * @param $quizId
     * @param int $offset
     * @param $maxRecords
     * @param string $sort_mode
     * @param string $find
     * @return array
     */
    public function list_quiz_question_stats($quizId, $offset = 0, $maxRecords = -1, $sort_mode = 'position_asc', $find = '')
	{

		$query = "select distinct(tqs.`questionId`)"
						. " from `tiki_quiz_stats` tqs,`tiki_quiz_questions` tqq"
						. " where tqs.`questionId`=tqq.`questionId` and tqs.`quizId` = ? order by "
						. $this->convertSortMode($sort_mode);

		$result = $this->query($query, array((int) $quizId));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$question = $this->getOne("select `question` from `tiki_quiz_questions` where `questionId`=?", array((int) $res["questionId"]));

			$total_votes = $this->getOne(
				"select sum(`votes`) from `tiki_quiz_stats` where `quizId`=? and `questionId`=?",
				array((int) $quizId, (int) $res["questionId"])
			);
			$query2 = "select tqq.`optionId`,`votes`,`optionText`"
								. " from `tiki_quiz_stats` tqq,`tiki_quiz_question_options` tqo"
								. " where tqq.`optionId`=tqo.`optionId` and tqq.`questionId`=?"
								;
			$result2 = $this->query($query2, array((int) $res["questionId"]));
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

    /**
     * @param $answerUploadId
     */
    public function download_answer($answerUploadId)
	{

		$query = "SELECT `filecontent`, `filetype`, `filename`, `filesize` FROM `tiki_user_answers_uploads` WHERE `answerUploadId`=?";

		$result = $this->query($query, array((int) $answerUploadId));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$data = $res['filecontent'];
			$name = $res['filename'];
			$type = $res['filetype'];
			$size = $res['filesize'];
		}

		$name = htmlspecialchars($name);

		header("Content-type: $type");
		header("Content-length: $size");
		header("Content-Disposition: attachment; filename=\"$name\"");
		header("Content-Description: PHP Generated Data");
		print $data;

	}


    /**
     * @param $userResultId
     * @return array
     */
    public function get_user_quiz_questions($userResultId)
	{
		$query = "select distinct(tqs.`questionId`) from `tiki_user_answers` tqs,`tiki_quiz_questions` tqq"
						. " where tqs.`questionId`=tqq.`questionId` and tqs.`userResultId` = ? order by `position` desc";

		$result = $this->query($query, array((int) $userResultId));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$question = $this->getOne("select `question` from `tiki_quiz_questions` where `questionId`=?", array((int) $res["questionId"]));

			$questionId = $res["questionId"];

			$query2 = "select tqq.`optionId`,tqo.`points`,`optionText`"
								. " from `tiki_user_answers` tqq,`tiki_quiz_question_options` tqo"
								.	" where tqq.`optionId`=tqo.`optionId` and tqq.`userResultId`=? and tqq.`questionId`=?";
			$result2 = $this->query($query2, array((int) $userResultId, (int) $questionId));
			$options = array();

			while ($res = $result2->fetchRow()) {
				$opt = array();

				$opt["optionText"] = $res["optionText"];
				$opt["points"] = $res["points"];

				$query3 = "select `answerUploadId`, `filename` from `tiki_user_answers_uploads` where `userResultId` = ? and `questionId` = ?";
				$result3 = $this->query($query3, array((int) $userResultId, (int) $questionId));

				while ($res2 = $result3->fetchRow()) {
					$opt["filename"] = $res2["filename"];
					$opt["answerUploadId"] = $res2["answerUploadId"];
				}

				$options[] = $opt;

			}


			$ques = array();
			$ques["options"] = $options;
			$ques["question"] = $question;
			$ret[] = $ques;

		}


		return $ret;
	}

    /**
     * @param $userResultId
     */
    public function remove_quiz_stat($userResultId)
	{
		$query = "select `quizId`,`user` from `tiki_user_quizzes` where `userResultId`=?";
		$bindvars=array((int) $userResultId);

		$result = $this->query($query, $bindvars);
		$res = $result->fetchRow();
		$user = $res["user"];
		$quizId = $res["quizId"];

		$query = "delete from `tiki_user_taken_quizzes` where `user`=? and `quizId`=?";
		$result = $this->query($query, array($user, (int) $quizId));

		$query = "delete from `tiki_user_quizzes` where `userResultId`=?";
		$result = $this->query($query, $bindvars);
		$query = "delete from `tiki_user_answers` where `userResultId`=?";
		$result = $this->query($query, $bindvars);
	}

    /**
     * @param $quizId
     */
    public function clear_quiz_stats($quizId)
	{
		$query = "delete from `tiki_user_taken_quizzes` where `quizId`=?";
		$bindvars=array((int) $quizId);

		$result = $this->query($query, $bindvars);

		$query = "delete from `tiki_quiz_stats_sum` where `quizId`=?";
		$result = $this->query($query, $bindvars);

		$query = "delete from `tiki_quiz_stats` where `quizId`=?";
		$result = $this->query($query, $bindvars);

		$query = "delete from `tiki_user_quizzes` where `quizId`=?";
		$result = $this->query($query, $bindvars);

		$query = "delete from `tiki_user_answers` where `quizId`=?";
		$result = $this->query($query, $bindvars);
	}

    /**
     * @param $quizId
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return array
     */
    public function list_quiz_stats($quizId, $offset, $maxRecords, $sort_mode, $find)
	{
		$this->compute_quiz_stats();

		$query = "select `passingperct` from `tiki_quizzes` where `quizId` = ?";
		$passingperct = $this->getOne($query, array((int) $quizId));

		if ($find) {
			//isnt that superflous? hmm.
			$findesc = '%' . $find . '%';
		}
		$mid = " where `quizId`=?";
		$bindvars=array((int) $quizId);

		$query = "select * from `tiki_user_quizzes` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_user_quizzes` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["avgavg"] = ($res["maxPoints"] != 0) ? $res["points"] / $res["maxPoints"] * 100 : 0.0;

			if (isset($passingperct) && $passingperct > 0) {
				$res['ispassing'] = ($res["avgavg"] >= $passingperct)?true:false;
			}

			$hasDet = $this->getOne(
				"select count(*) from `tiki_user_answers` where `userResultId`=?",
				array((int) $res["userResultId"])
			);
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

    /**
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return array
     */
    public function list_quiz_sum_stats($offset, $maxRecords, $sort_mode, $find)
	{
		$this->compute_quiz_stats();

		$stats = $this->table('tiki_quiz_stats_sum');
		$conditions = array();

		if ($find) {
			$conditions['quizName'] = $stats->like("%$find%");
		}

		return array(
			'data' => $stats->fetchAll($stats->all(), $conditions, $maxRecords, $offset, $stats->expr($this->convertSortMode($sort_mode))),
			'cant' => $stats->fetchCount($conditions),
		);
	}



	// Takes a given uploaded answer and inserts it into the DB. - burley
    /**
     * @param $userResultId
     * @param $questionId
     * @param $filename
     * @param $filetype
     * @param $filesize
     * @param $tmp_name
     */
    public function register_user_quiz_answer_upload($userResultId, $questionId, $filename, $filetype, $filesize,$tmp_name)
	{

		$data = fread(fopen($tmp_name, "r"), filesize($tmp_name));

		$query = "insert into `tiki_user_answers_uploads`"
							. "(`userResultId`,`questionId`,`filename`,`filetype`,`filesize`,`filecontent`)"
							.	" values(?,?,?,?,?,?)";
		$result = $this->query($query, array((int) $userResultId, (int) $questionId, $filename, $filetype, $filesize, $data));
	}


    /**
     * @param $userResultId
     * @param $quizId
     * @param $questionId
     * @param $optionId
     */
    public function register_user_quiz_answer($userResultId, $quizId, $questionId, $optionId)
	{
		$query = "insert into `tiki_user_answers`(`userResultId`,`quizId`,`questionId`,`optionId`) values(?,?,?,?)";
		$result = $this->query($query, array((int) $userResultId, (int) $quizId, (int) $questionId, (int) $optionId));
	}

    /**
     * @param $quizId
     * @param $user
     * @param $timeTaken
     * @param $points
     * @param $maxPoints
     * @param $resultId
     * @return mixed
     */
    public function register_quiz_stats($quizId, $user, $timeTaken, $points, $maxPoints, $resultId)
	{
		// Fix a bug if no result is indicated.
		if (!$resultId) {
			$resultId = 0;
		}

		$query = "insert into `tiki_user_quizzes`(`user`,`quizId`,`timestamp`,`timeTaken`,`points`,`maxPoints`,`resultId`) values(?,?,?,?,?,?,?)";
		$result = $this->query(
			$query,
			array(
				$user,
				$quizId,
				$this->now,
				$timeTaken,
				$points,
				$maxPoints,
				$resultId
			)
		);
		$queryId = $this->getOne(
			"select max(`userResultId`) from `tiki_user_quizzes` where `timestamp`=? and `quizId`=?",
			array($this->now, (int) $quizId)
		);
		return $queryId;
	}

    /**
     * @param $quizId
     * @param $questionId
     * @param $optionId
     * @return bool
     */
    public function register_quiz_answer($quizId, $questionId, $optionId)
	{
		$cant = $this->getOne(
			"select count(*) from `tiki_quiz_stats` where `quizId`=? and `questionId`=? and `optionId`=?",
			array((int) $quizId, (int) $questionId, (int) $optionId)
		);

		if ($cant) {
			$query = "update `tiki_quiz_stats` set `votes`=`votes`+1 where `quizId`=? and `questionId`=? and `optionId`=?";
			$bindvars=array((int) $quizId, (int) $questionId, (int) $optionId);
		} else {
			$query = "insert into `tiki_quiz_stats`(`quizId`,`questionId`,`optionId`,`votes`) values(?,?,?,?)";
			$bindvars=array((int) $quizId, (int) $questionId, (int) $optionId,1);
		}

		$result = $this->query($query, $bindvars);

		return true;
	}

    /**
     * @param $quizId
     * @param $points
     * @return int
     */
    public function calculate_quiz_result($quizId, $points)
	{
		$query = "select * from `tiki_quiz_results` where `fromPoints`<=? and `toPoints`>=? and `quizId`=?";

		$result = $this->query($query, array((int) $points, (int) $points, (int) $quizId));

		if (!$result->numRows()) {
			return 0;
		}

		$res = $result->fetchRow();
		return $res;
	}

    /**
     * @param $user
     * @param $quizId
     * @return mixed
     */
    public function user_has_taken_quiz($user, $quizId)
	{
		$cant = $this->getOne("select count(*) from `tiki_user_taken_quizzes` where `user`=? and `quizId`=?", array($user, (int) $quizId));

		return $cant;
	}

    /**
     * @param $user
     * @param $quizId
     */
    public function user_takes_quiz($user, $quizId)
	{
		$query = "delete from `tiki_user_taken_quizzes` where `user`=? and `quizId`=?";
		$bindvars=array($user,(int) $quizId);
		$result = $this->query($query, $bindvars, -1, -1, false);
		$query = "insert into `tiki_user_taken_quizzes`(`user`,`quizId`) values(?,?)";
		$result = $this->query($query, $bindvars);
	}

    /**
     * @param $resultId
     * @param $quizId
     * @param $fromPoints
     * @param $toPoints
     * @param $answer
     * @return mixed
     */
    public function replace_quiz_result($resultId, $quizId, $fromPoints, $toPoints, $answer)
	{
		if ($resultId) {
			// update an existing quiz
			$query = "update `tiki_quiz_results` set `fromPoints` = ?, `toPoints` = ?, `quizId` = ?, `answer` = ?  where `resultId` = ?";
			$bindvars=array((int) $fromPoints,(int) $toPoints, (int) $quizId, $answer, (int) $resultId);
			$result = $this->query($query, $bindvars);
		} else {
			// insert a new quiz

			$query = "insert into `tiki_quiz_results`(`quizId`,`fromPoints`,`toPoints`,`answer`) values(?,?,?,?)";
			$bindvars=array((int) $quizId, (int) $fromPoints, (int) $toPoints, $answer);
			$result = $this->query($query, $bindvars);
			$queryid = "select max(`resultId`) from `tiki_quiz_results` where `fromPoints`=? and `toPoints`=? and `quizId`=?";
			$quizId = $this->getOne($queryid, array((int) $fromPoints, (int) $toPoints, $quizId));
		}

		return $quizId;
	}

    /**
     * @param $resultId
     * @return bool
     */
    public function get_quiz_result($resultId)
	{
		$query = "select * from `tiki_quiz_results` where `resultId`=?";

		$result = $this->query($query, array((int) $resultId));

		if (!$result->numRows()) {
			return false;
		}

		$res = $result->fetchRow();
		return $res;
	}

    /**
     * @param $resultId
     * @return bool
     */
    public function remove_quiz_result($resultId)
	{
		$query = "delete from `tiki_quiz_results` where `resultId`=?";

		$result = $this->query($query, array($resultId));
		return true;
	}

    /**
     * @param $quizId
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return array
     */
    public function list_quiz_results($quizId, $offset, $maxRecords, $sort_mode, $find)
	{

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `quizId`=? and `answer` like ? ";
			$bindvars=array((int) $quizId, $findesc);
		} else {
			$mid = " where `quizId`=? ";
			$bindvars=array((int) $quizId);
		}

		$query = "select * from `tiki_quiz_results` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_quiz_results` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	// called by tiki-edit_quiz.php
    /**
     * @param $quizId
     * @param $name
     * @param $description
     * @param $canRepeat
     * @param $storeResults
     * @param $immediateFeedback
     * @param $showAnswers
     * @param $shuffleQuestions
     * @param $shuffleAnswers
     * @param $questionsPerPage
     * @param $timeLimited
     * @param $timeLimit
     * @param $publishDate
     * @param $expireDate
     * @param $passingperct
     * @return mixed
     */
    public function replace_quiz($quizId, $name, $description, $canRepeat, $storeResults
			, $immediateFeedback, $showAnswers,	$shuffleQuestions, $shuffleAnswers
			, $questionsPerPage, $timeLimited, $timeLimit, $publishDate, $expireDate
			, $passingperct
			)
	{
		if ($quizId) {
			// update an existing quiz
			$query = "update `tiki_quizzes` set `name` = ?, `description` = ?, `canRepeat` = ?, `storeResults` = ?,";
			$query.= "`immediateFeedback` = ?, `showAnswers` = ?,	`shuffleQuestions` = ?, `shuffleAnswers` = ?, ";
			$query.= "`publishDate` = ?, `expireDate` = ?, ";
			$query.= "`questionsPerPage` = ?, `timeLimited` = ?, `timeLimit` =?, `passingperct` = ?  where `quizId` = ?";
			$bindvars = array($name,
								$description,
								$canRepeat,
								$storeResults,
								$immediateFeedback,
								$showAnswers,
								$shuffleQuestions,
								$shuffleAnswers,
								$publishDate,
								$expireDate,
								(int) $questionsPerPage,
								$timeLimited,
								(int) $timeLimit,
								(int) $passingperct,
								(int) $quizId
			);

			$result = $this->query($query, $bindvars);
		} else {
			// insert a new quiz

			$query  = "insert into `tiki_quizzes`(`name`,`description`,`canRepeat`,`storeResults`,";
			$query .= "`immediateFeedback`, `showAnswers`,	`shuffleQuestions`, `shuffleAnswers`,";
			$query .= "`publishDate`, `expireDate`,";
			$query .= "`questionsPerPage`,`timeLimited`,`timeLimit`,`created`,`taken`,`passingperct`)";
			$query .= " values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$bindvars=array(
									$name,
									$description,
									$canRepeat,
									$storeResults,
									$immediateFeedback,
									$showAnswers,
									$shuffleQuestions,
									$shuffleAnswers,
									$publishDate,
									$expireDate,
									(int) $questionsPerPage,
									$timeLimited,
									(int) $timeLimit,
									(int) $this->now,
									0,
									(int) $passingperct
			);
			$result = $this->query($query, $bindvars);
			$queryid = "select max(`quizId`) from `tiki_quizzes` where `created`=?";
			$quizId = $this->getOne($queryid, array((int) $this->now));
		}

		return $quizId;
	}

    /**
     * @param $questionId
     * @param $question
     * @param $type
     * @param $quizId
     * @param $position
     * @return mixed
     */
    public function replace_quiz_question($questionId, $question, $type, $quizId, $position)
	{
		if ($questionId) {
			// update an existing quiz
			$query = "update `tiki_quiz_questions` set `type`=?, `position` = ?, `question` = ?  where `questionId` = ? and `quizId`=?";
			$bindvars = array($type,(int) $position, $question, (int) $questionId, (int) $quizId);
			$result = $this->query($query, $bindvars);
		} else {
			// insert a new quiz

			$query = "insert into `tiki_quiz_questions`(`question`,`type`,`quizId`,`position`) values(?,?,?,?)";
			$bindvars = array($question, $type, (int) $quizId, (int) $position);
			$result = $this->query($query, $bindvars);
			$queryid = "select max(`questionId`) from `tiki_quiz_questions` where `question` like ? and `type`=?";
			$questionId = $this->getOne($queryid, array(substr($question, 0, 200) . "%", $type));
		}
		return $questionId;
	}

    /**
     * @param $optionId
     * @param $option
     * @param $points
     * @param $questionId
     * @return mixed
     */
    public function replace_question_option($optionId, $option, $points, $questionId)
	{

		// validating the points value
		if ((!is_numeric($points)) || ($points == "")) {
			$points = 0;
		}
		if ($optionId) {
			$query = "update `tiki_quiz_question_options` set `points`=?, `optionText` = ?  where `optionId` = ? and `questionId`=?";
			$bindvars = array((int) $points, $option,(int) $optionId, (int) $questionId);
			$result = $this->query($query, $bindvars);
		} else {
			$query = "insert into `tiki_quiz_question_options`(`optionText`,`points`,`questionId`) values(?,?,?)";
			$result = $this->query($query, array($option, (int) $points, (int) $questionId));
			$queryid = "select max(`optionId`) from `tiki_quiz_question_options` where `optionText`=? and `questionId`=?";
			$optionId = $this->getOne($queryid, array($option, (int) $questionId));
		}

		return $optionId;
	}

    /**
     * @param $questionId
     * @return bool
     */
    public function get_quiz_question($questionId)
	{
		$query = "select * from `tiki_quiz_questions` where `questionId`=?";
		$result = $this->query($query, array((int) $questionId));
		if (!$result->numRows()) {
			return false;
		}
		$res = $result->fetchRow();
		return $res;
	}

    /**
     * @param $optionId
     * @return bool
     */
    public function get_quiz_question_option($optionId)
	{
		$query = "select * from `tiki_quiz_question_options` where `optionId`=?";
		$result = $this->query($query, array((int) $optionId));
		if (!$result->numRows()) {
			return false;
		}
		$res = $result->fetchRow();
		return $res;
	}

    /**
     * @param $quizId
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return array
     */
    public function list_quiz_questions($quizId, $offset, $maxRecords, $sort_mode, $find)
	{
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where `quizId`=? and `question` like ? ";
			$bindvars=array((int) $quizId, $findesc);
		} else {
			$mid = " where `quizId`=? ";
			$bindvars=array((int) $quizId);
		}

		$query = "select * from `tiki_quiz_questions` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_quiz_questions` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["options"] = $this->getOne("select count(*) from `tiki_quiz_question_options` where `questionId`=?", array((int) $res["questionId"]));
			$res["maxPoints"] = $this->getOne("select max(`points`) from `tiki_quiz_question_options` where `questionId`=?", array((int) $res["questionId"]));
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

    /**
     * @param $offset
     * @param $maxRecords
     * @param string $sort_mode
     * @param $find
     * @return array
     */
    public function list_all_questions($offset, $maxRecords, $sort_mode="position_desc", $find)
	{
		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `question` like ? ";
			$bindvars = array($findesc);
		} else {
			$mid = " ";
			$bindvars = array();
		}

		$query = "select * from `tiki_quiz_questions` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_quiz_questions` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["options"]
				= $this->getOne("select count(*) from `tiki_quiz_question_options` where `questionId`=?", array((int) $res["questionId"]));

			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

    /**
     * @param $questionId
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return array
     */
    public function list_quiz_question_options($questionId, $offset, $maxRecords, $sort_mode, $find)
	{
		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `questionId`=? and `optionText` like ? ";
			$bindvars=array((int) $questionId,$findesc);
		} else {
			$mid = " where `questionId`=? ";
			$bindvars=array((int) $questionId);
		}

		$query = "select * from `tiki_quiz_question_options` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_quiz_question_options` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

    /**
     * @param $questionId
     * @return bool
     */
    public function remove_quiz_question($questionId)
	{
		$query = "delete from `tiki_quiz_questions` where `questionId`=?";

		$result = $this->query($query, array((int) $questionId));
		// Remove all the options for the question
		$query = "delete from `tiki_quiz_question_options` where `questionId`=?";
		$result = $this->query($query, array((int) $questionId));
		return true;
	}

    /**
     * @param $optionId
     * @return bool
     */
    public function remove_quiz_question_option($optionId)
	{
		$query = "delete from `tiki_quiz_question_options` where `optionId`=?";

		$result = $this->query($query, array((int) $optionId));
		return true;
	}

    /**
     * @param $quizId
     * @return bool
     */
    public function remove_quiz($quizId)
	{
		$query = "delete from `tiki_quizzes` where `quizId`=?";

		$result = $this->query($query, array((int) $quizId));
		$query = "select * from `tiki_quiz_questions` where `quizId`=?";
		$result = $this->query($query, array((int) $quizId));

		// Remove all the options for each question
		while ($res = $result->fetchRow()) {
			$questionId = $res["questionId"];

			$query2 = "delete from `tiki_quiz_question_options` where `questionId`=?";
			$result2 = $this->query($query2, array((int) $questionId));
		}

		// Remove all the questions
		$query = "delete from `tiki_quiz_questions` where `quizId`=?";
		$result = $this->query($query, array((int) $quizId));
		$query = "delete from `tiki_quiz_results` where `quizId`=?";
		$result = $this->query($query, array((int) $quizId));
		$query = "delete from `tiki_quiz_stats` where `quizId`=?";
		$result = $this->query($query, array((int) $quizId));
		$query = "delete from `tiki_user_quizzes` where `quizId`=?";
		$result = $this->query($query, array((int) $quizId));
		$query = "delete from `tiki_user_answers` where `quizId`=?";
		$result = $this->query($query, array((int) $quizId));
		$this->remove_object('quiz', $quizId);
		return true;
	}

    /**
     * @param $id
     * @return Quiz
     */
    public function quiz_fetch($id)
	{
		if ($id == 0) {
			$quiz = new Quiz;
		} else {
			echo __FILE__ . " line: " . __LINE__ . " : Need to fetch a quiz from the database" . "<br />";
		}
		return $quiz;
	}

	// $quiz is a quiz object
    /**
     * @param $quiz
     * @return mixed
     */
    public function quiz_store($quiz)
	{
		echo __FILE__ . " line: " . __LINE__ . ": in quizlib->quiz_store<br />";
		echo "Store stuff in the dbFields array.<br />";
		foreach ($quiz->dbFields as $f) {
		}
		die;
		if ($quizId) {
			// update an existing quiz
			$query = "update `tiki_quizzes` set `name` = ?, `description` = ?, `canRepeat` = ?, `storeResults` = ?,";
			$query.= "`immediateFeedback` = ?, `showAnswers` = ?,	`shuffleQuestions` = ?, `shuffleAnswers` = ?, ";
			$query.= "`publishDate` = ?, `expireDate` = ?, ";
			$query.= "`questionsPerPage` = ?, `timeLimited` = ?, `timeLimit` =?  where `quizId` = ?";
			$bindvars = array(
							$name,
							$description,
							$canRepeat,
							$storeResults,
							$immediateFeedback,
							$showAnswers,
							$shuffleQuestions,
							$shuffleAnswers,
							$publishDate,
							$expireDate,
							(int) $questionsPerPage,
							$timeLimited,
							(int) $timeLimit,
							(int) $quizId
			);

			$result = $this->query($query, $bindvars);
		} else {
			// insert a new quiz

			$query = "insert into `tiki_quizzes`(`name`,`description`,`canRepeat`,`storeResults`,";
			$query.= "`immediateFeedback`, `showAnswers`,	`shuffleQuestions`, `shuffleAnswers`,";
			$query.= "`publishDate`, `expireDate`,";
			$query.="`questionsPerPage`,`timeLimited`,`timeLimit`,`created`,`taken`) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$bindvars = array($name,
								$description,
								$canRepeat,
								$storeResults,
								$immediateFeedback,
								$showAnswers,
								$shuffleQuestions,
								$shuffleAnswers,
								$publishDate,
								$expireDate,
								(int) $questionsPerPage,
								$timeLimited,
								(int) $timeLimit,
								(int) $this->now,
								0
			);
			$result = $this->query($query, $bindvars);
			$queryid = "select max(`quizId`) from `tiki_quizzes` where `created`=?";
			$quizId = $this->getOne($queryid, array((int) $this->now));
		}

		return $quizId;
	}

    /**
     * @return string
     */
    public function get_upload_dir()
	{
		return "quiz_uploads/";
	}
}

// Find the next non-blank or return -1
/**
 * @param $text
 * @return int
 */
function NextText($text)
{
	$found = -1;
	for ($i = 0, $icount_text = count($text); $i < $icount_text; $i++) {
		if (strlen($text[$i]) > 0) {
			$found = $i;
			break;
		}
	}
	return $found;
}

// Find the next blank or retrun the last element
/**
 * @param $text
 * @return int
 */
function NextBlank($text)
{
	$found = 0;
	for ($i = 0, $icount_text = count($text); $i < $icount_text; $i++) {
		$found = $i;
		if ($text[$i] == "") {
			break;
		}
	}
	return $found;
}

/**
 * @param $s
 */
function quizlib_error_exit($s)
{
	$smarty = TikiLib::lib('smarty');
	$smarty->assign('msg', $s);

	$smarty->display("error.tpl");
	die;
}

// Called by tiki-edit_quiz_questions.php
// Convert a block of text into an array of question objects.
/**
 * @param $text
 * @return array
 */
function TextToQuestions($text)
{
	$text = preg_replace("/\r\n/", "\n", $text);
	$text = preg_replace("/\n\r/", "\n", $text);
	$text = preg_replace("/\r/", "\n", $text);
	$text = preg_replace("/\t/", " ", $text);
	$text = preg_replace("/[ ]+/", " ", $text);

	$text = preg_split("/\n/", $text);

	if ($text[count($text)-1] != "") {
		$text[] = "";
	}

	for ($i = 0, $icount_text = count($text); $i < $icount_text; $i++) {
		$text[$i] = trim($text[$i]);
		if ($text[$i] and !ctype_print($text[$i])) {
			quizlib_error_exit(
				"lib/quizzes/quizlib.php line "
				. __LINE__
				. ": Your text has invalid character(s) near line $i where it says:\n  $text[$i]"
			);
		}
	}

	$questions = Array();

	while (NextText($text) != -1) {
		$text = array_slice($text, NextText($text));
		$lines = array_slice($text, 0, NextBlank($text));
		$text = array_slice($text, NextBlank($text));
		if (count($lines) > 0) {
			$question = new HW_QuizQuestionMultipleChoice($lines);
			array_push($questions, $question);
		}
	}
	return $questions;
}

// An abstract class
/**
 *
 */
class HW_QuizQuestion
{
	public $question;

    /**
     * @param $lines
     */
    public function from_text($lines)
	{
		// Set the question according to an array of text lines.
	}
	public function getQuestion()
	{
		return $this->question;
	}
	public function to_text()
	{
		// Export the question to an array of text lines.
	}
	public function getAnswerCount()
	{
		// How many possible answers (i.e. choices in a multiple-choice)
	}
}

// A multiple-choice quiz question
// e.g.
//   $question = "What is your favorite color?";
//   $choices = Array(Array('text'=>"Red",  'correct'=>1),
//                    Array('text'=>"Blue", 'correct'=>1),
//                    Array('text'=>"Green",'correct'=>1));
//   Any of the answers are correct in this example.
/**
 *
 */
class HW_QuizQuestionMultipleChoice extends HW_QuizQuestion
{
	public $choices  = Array();

    /**
     * @param $lines
     */
    public function __construct($lines)
	{
		$this->from_text($lines);
	}

	// Import from text array
	// $lines is in array of text items.
	//   The 0th line is the question.
	//   The rest of the lines are answers.
	//   Correct answers start with a "*"
    /**
     * @param $lines
     */
    public function from_text($lines)
	{
		$this->question = $lines[0];
		$this->choices  = Array();
		$lines = array_slice($lines, 1);
		foreach ($lines as $line) {
			if (preg_match("/^\*\s*(.*)/", $line, $match)) {
				// Ignore spaces after the "*"
				$a = Array('text'=>$match[1],'correct'=>1);
			} else {
				$a = Array('text'=>$line,'correct'=>0);
			}
			array_push($this->choices, $a);
		}
	}

	// Export the question to an array of text lines.
    /**
     * @param bool $show_answer
     * @return array
     */
    public function to_text($show_answer = false)
	{
		$lines = Array();
		array_push($lines, $this->question);
		foreach ($this->choices as $choice) {
			if ($show_answer && $choice['correct']) {
				array_push($lines, "*" . $choice['text']);
			} else {
				array_push($lines, " " . $choice['text']);
			}
		}
		return $lines;
	}

    /**
     * @return int
     */
    public function getChoiceCount()
	{
		return count($this->choices);
	}

    /**
     * @param $i
     * @return mixed
     */
    public function getChoice($i)
	{
		return $this->choices[$i]['text'];
	}

    /**
     * @param $i
     * @return mixed
     */
    public function getCorrect($i)
	{
		return $this->choices[$i]['correct'];
	}

	public function dump()
	{
		echo "question = \"" . $this->question . "\"\n";
		echo "choices =\n";
		foreach ($this->choices as $choice) {
			if ($choice['correct']) {
				echo "*";
			} else {
				echo " ";
			}
			echo $choice['text'] . "\n";
		}
	}
}

// A Yes-No quiz question
// e.g.
//   $question = "Do you wiki?";
//   $answer   = -1 (unknown), 0 (no), 1 (yes)
/**
 *
 */
class HW_QuizQuestionYesNo extends HW_QuizQuestion
{
	public $question;
	public $answer  = -1;

    /**
     * @param $lines
     */
    public function __construct($lines)
	{
		$this->from_text($lines);
	}

	// Import from text array
	// $lines is in array of text items.
	//   The 0th line is the question.
	//   The first line is the answer.
    /**
     * @param $lines
     */
    public function from_text($lines)
	{
		$this->question = $lines[0];
		if (preg_match("/^\s*[Yy][Ee][Ss]\s*$/", $lines[1])) {
			// Ignore spaces and case
			$this->answer = 1;
		} elseif (preg_match("/^\s*[Nn][Oo]\s*$/", $lines[1])) {
			// Ignore spaces and case
			$this->answer = 0;
		} else {
			$this->answer = -1;
		}
	}

    /**
     * @param bool $show_answer
     * @return array
     */
    public function to_text($show_answer = false)
	{
		// Export the question to an array of text lines.
		$lines = Array();
		array_push($lines, $this->question);
		if ($this->answer == 1) {
			array_push($lines, " Yes");
		} elseif ($this->answer == 0) {
			array_push($lines, " No");
		} else {
			array_push($lines, " Unknown");
		}
		return $lines;
	}

	public function dump()
	{
		echo "question = \"" . $this->question . "\"\n";
		echo "answer = $this->answer\n";
	}
}

/**
 *
 */
class Quiz
{
	public $id;
	public $bDeleted;
	public $timestamp;
	public $nAuthor;
	public $bOnline;
	public $nTaken;
	public $sName;
	public $sDescription;
	public $datePub;
	public $dateExp;
	public $bRandomQuestions;
	public $nRandomQuestions;
	public $bShuffleQuestions;
	public $bShuffleAnswers;
	public $bLimitQuestionsPerPage;
	public $nLimitQuestionsPerPage;
	public $bTimeLimited;
	public $nTimeLimit;
	public $bMultiSession;
	public $bCanRepeat;
	public $nCanRepeat;
	public $sGradingMethod;
	public $sShowScore;
	public $sShowCorrectAnswers;
	public $sPublishStats;
	public $bAdditionalQuestions;
	public $bForum;
	public $sForum;
	public $sPrologue;
	public $sData;
	public $sEpilogue;
	public $dbFields;

	public function __construct()
	{
		global $user;
		$userlib = TikiLib::lib('user');
		$this->dbFields = array(
				"id",
				"bDeleted",
				"timestamp",
				"nAuthor",
				"bOnline",
				"nTaken",
				"sName",
				"sDescription",
				"datePub",
				"dateExp",
				"bRandomQuestions",
				"nRandomQuestions",
				"bShuffleQuestions",
				"bShuffleAnswers",
				"bLimitQuestionsPerPage",
				"nLimitQuestionsPerPage",
				"bTimeLimited",
				"nTimeLimit",
				"bMultiSession",
				"bCanRepeat",
				"nCanRepeat",
				"sGradingMethod",
				"sShowScore",
				"sShowCorrectAnswers",
				"sPublishStats",
				"bAdditionalQuestions",
				"bForum",
				"sForum",
				"sPrologue",
				"sData",
				"sEpilogue"
		);
		$this->id = 0;
		$this->bDeleted = 0;
		$this->timestamp = $this->now;
		$this->nAuthor = $userlib->get_user_id($user);
		$this->sAuthor = $user;
		$this->bOnline = 'n';
		$this->nTaken = 'n';
		$this->sName = "";
		$this->sDescription = "";
		$this->datePub = $this->now;
		$this->dateExp = TikiLib::make_time(0, 0, 0, 1, 1, TikiLib::date_format("%Y")+10);
		$this->bRandomQuestions = "y";
		$this->nRandomQuestions = 10;
		$this->nShuffleQuestions = "y";
		$this->bShuffleAnswers = "y";
		$this->bLimitQuestionsPerPage = "y";
		$this->nLimitQuestionsPerPage = 1;
		$this->bTimeLimited = "n";
		$this->nTimeLimit = "1";
		$this->bMultiSession = "n";
		$this->bCanRepeat = "y";
		$this->nCanRepeat = "unlimited";
		$this->sGradingMethod = "machine";
		$this->sShowScore = "immediately";
		$this->sShowCorrectAnswers = "immediately";
		$this->sPublishStats = "immediately";
		$this->bAdditionalQuestions = "n";

		$this->forum = "n";
		$this->forumName = "";
		$this->prologue = "";
		$this->epilogue = "";
	}

	// dump as html text
    /**
     * @return array
     */
    public function show_html()
	{
		$userlib = TikiLib::lib('user');
		$lines = array();
		$lines[] = "id = " . $this->id . "<br />";
		$lines[] = "deleted = " . $this->deleted . "<br />";
		$authorInfo = $userlib->get_userid_info($this->author);
		$lines[] = "author id = " . $this->author . "; author login = " . $authorInfo["login"] . "<br />";
		$lines[] = "version = " . $this->version . "<br />";
		$lines[] = "timestamp = " . $this->date_format("%a, %e %b %Y %H:%M:%S %O", $this->timestamp) . "<br />";
		$lines[] = "online = " . $this->online . "<br />";
		$lines[] = "studentAttempts = " . $this->studentAttempts . "<br />";
		$lines[] = "name = " . $this->name . "<br />";
		$lines[] = "description = " . $this->description . "<br />";
		$lines[] = "datePub = " . $this->date_format("%a, %e %b %Y %H:%M:%S %O", $this->datePub) . "<br />";
		$lines[] = "dateExp = " . $this->date_format("%a, %e %b %Y %H:%M:%S %O", $this->dateExp) . "<br />";
		$lines[] = "nQuestion = " . $this->nQuestion."<br />";
		$lines[] = "nQuestions = " . $this->nQuestions . "<br />";
		$lines[] = "shuffleQuestions = " . $this->shuffleQuestions . "<br />";
		$lines[] = "shuffleAnswers = " . $this->shuffleAnswers . "<br />";
		$lines[] = "limitDisplay = " . $this->limitDisplay . "<br />";
		$lines[] = "questionsPerPage = " . $this->questionsPerPage . "<br />";
		$lines[] = "timeLimited = " . $this->timeLimited . "<br />";
		$lines[] = "timeLimit = " . $this->timeLimit . "<br />";
		$lines[] = "multiSession = " . $this->multiSession . "<br />";
		$lines[] = "canRepeat = " . $this->canRepeat . "<br />";
		$lines[] = "repetitions = " . $this->repetitions . "<br />";
		$lines[] = "gradingMethod = " . $this->gradingMethod . "<br />";
		$lines[] = "showScore = " . $this->showScore . "<br />";
		$lines[] = "showCorrectAnswers = " . $this->showCorrectAnswers . "<br />";
		$lines[] = "publishStats = " . $this->publishStats . "<br />";
		$lines[] = "additionalQuestions = " . $this->additionalQuestions . "<br />";
		$lines[] = "forum = " . $this->forum . "<br />";
		$lines[] = "forumName = " . $this->forumName . "<br />";
		$lines[] = "data = " . $this->data . "<br />";
		return $lines;
	}

	// Use any data in the array to replace the instance data.
    /**
     * @param $data
     */
    public function data_load($data)
	{
		foreach ($this as $key => $val) {
			if (isset($data[$key]) && ($data[$key] != $val)) {
				$this->$key = $data[$key];
			}
		}
	}

    /**
     * @param $quiz
     */
    public function compare($quiz)
	{

	}

	public function getAnswerCount()
	{
		// How many possible answers (i.e. choices in a multiple-choice)
	}
}
