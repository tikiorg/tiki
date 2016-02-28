<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/* Task properties:
   user, taskId, title, description, date, status, priority, completed, percentage
*/
/**
 *
 */
class FaqLib extends TikiLib
{
    /**
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return mixed
     */
    function list_faqs($offset, $maxRecords, $sort_mode, $find)
	{
		$mid = '';
		if ( $find ) {
			$findesc = '%' . $find . '%';
			$mid = ' where (`title` like ? or `description` like ?)';
			$bindvars = array($findesc, $findesc);
		} else {
			 $bindvars = array();
		}

		$query = "select `faqId` from `tiki_faqs` $mid";
		$result = $this->fetchAll($query, $bindvars);
		$res = $ret = $retids = array();
		$n=0;

		//FIXME Perm:filter ?
		foreach ( $result as $res ) {
			$objperm = $this->get_perm_object($res['faqId'], 'faq', '', false);
			if ($objperm['tiki_p_view_faqs'] == 'y') {
				if (($maxRecords == -1) || (($n>=$offset) && ($n < ($offset + $maxRecords)))) {
					$retids[] = $res['faqId'];
					$n++;
				}
			}
		}

		if ($n > 0) {
			$query = "select * from `tiki_faqs` where faqId in (" . implode(',', $retids) . ") order by " . $this->convertSortMode($sort_mode);
			$result = $this->fetchAll($query);
			foreach ( $result as $res ) {
				$res['suggested'] = $this->getOne('select count(*) from `tiki_suggested_faq_questions` where `faqId`=?', array((int) $res['faqId']));
				$res['questions'] = $this->getOne('select count(*) from `tiki_faq_questions` where `faqId`=?', array((int) $res['faqId']));
				$ret[] = $res;
			}
		}

		$retval['data'] = $ret;
		$retval['cant'] = $n;
		return $retval;
	}

    /**
     * @param $faqId
     * @return bool
     */
    function get_faq($faqId)
	{
		$query = "select * from `tiki_faqs` where `faqId`=?";
		$result = $this->query($query, array((int)$faqId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

    /**
     * @param $faqId
     * @param $question
     * @param $answer
     * @param $user
     */
    function add_suggested_faq_question($faqId, $question, $answer, $user)
	{
		$question = strip_tags($question, '<a>');

		$answer = strip_tags($answer, '<a>');
		$query = "insert into `tiki_suggested_faq_questions`(`faqId`,`question`,`answer`,`user`,`created`)
			values(?,?,?,?,?)";
		$result = $this->query($query, array($faqId, $question, $answer, ($user===null) ? '' : $user, $this->now));
	}

    /**
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @param $faqId
     * @return array
     */
    function list_suggested_questions($offset, $maxRecords, $sort_mode, $find, $faqId)
	{
		$bindvars=array();
		if ($find || $faqId) {
			$mid = " where ";
			if ($find) {
				$findesc = '%' . $find . '%';
				$mid .= "(`question` like ? or `answer` like ?)";
				$bindvars[]=$findesc;
				$bindvars[]=$findesc;
				if ($faqId) {
					$mid .= " and ";
				}
			}
			if ($faqId) {
				$mid .= "`faqId`=?";
				$bindvars[] = $faqId;
			}
		} else {
			$mid = "";
		}

		$query = "select * from `tiki_suggested_faq_questions` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_suggested_faq_questions` $mid";
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
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return array
     */
    function list_all_faq_questions($offset, $maxRecords, $sort_mode, $find)
	{

		$bindvars=array();
		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`question` like ? or `answer` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}

		$query = "select * from `tiki_faq_questions` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_faq_questions` $mid";
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
     * @param $faqId
     * @return bool
     */
    function remove_faq($faqId)
	{
		$query = "delete from `tiki_faqs` where `faqId`=?";

		$result = $this->query($query, array($faqId));
		$query = "delete from `tiki_faq_questions` where `faqId`=?";
		$result = $this->query($query, array($faqId));
		// Remove comments and/or individual permissions for faqs
		$this->remove_object('faq', $faqId);
		return true;
	}

    /**
     * @param $questionId
     * @return bool
     */
    function remove_faq_question($questionId)
	{
		$faqId = $this->getOne('select `faqId` from `tiki_faq_questions` where `questionId`=?', array($questionId));
		$result = $this->query('delete from `tiki_faq_questions` where `questionId`=?', array($questionId));
		return true;
	}

    /**
     * @param $questionId
     * @return bool
     */
    function get_faq_question($questionId)
	{
		$query = "select * from `tiki_faq_questions` where `questionId`=?";

		$result = $this->query($query, array($questionId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

    /**
     * @param $faqId
     */
    function add_faq_hit($faqId)
	{
		global $prefs, $user;

		if (StatsLib::is_stats_hit()) {
			$query = "update `tiki_faqs` set `hits`=`hits`+1 where `faqId`=?";

			$result = $this->query($query, array($faqId));
		}
	}

    /**
     * @param $faqId
     * @param $questionId
     * @param $question
     * @param $answer
     * @return bool
     */
    function replace_faq_question($faqId, $questionId, $question, $answer)
	{
		// Check the name
		if ( $questionId ) {
			$query = 'update `tiki_faq_questions` set `question`=?,`answer`=? where `questionId`=?';
			$bindvars = array($question, $answer, (int) $questionId);
			$result = $this->query($query, $bindvars);
		} else {
			$query = 'delete from `tiki_faq_questions` where `faqId`=? and question=?';
			$result = $this->query($query, array((int) $faqId, $question), -1, -1, false);
			$query = 'insert into `tiki_faq_questions`(`faqId`,`question`,`answer`, `created`) values(?,?,?,?)';
			$result = $this->query($query, array((int) $faqId, $question, $answer, $this->now));
			$questionId = $this->getOne('select max(questionId) from `tiki_faq_questions` where `faqId`=?', $faqId);
		}

		require_once('lib/search/refresh-functions.php');
		refresh_index('faq_questions', $questionId);

		return true;
	}

    /**
     * @param $faqId
     * @param $title
     * @param $description
     * @param $canSuggest
     * @return mixed
     */
    function replace_faq($faqId, $title, $description, $canSuggest)
	{
		// Check the name
		if ( $faqId ) {
			$query = 'update `tiki_faqs` set `title`=?,`description`=? ,`canSuggest`=? where `faqId`=?';
			$result = $this->query($query, array($title, $description, $canSuggest, (int) $faqId));
		} else {
			$query = 'delete from `tiki_faqs` where `title`=?';
			$result = $this->query($query, array($title), -1, -1, false);
			$query = 'insert into `tiki_faqs`(`title`,`description`,`created`,`hits`,`canSuggest`) values(?,?,?,?,?)';
			$result = $this->query($query, array($title, $description, (int) $this->now, 0, $canSuggest));
			$faqId = $this->getOne('select max(`faqId`) from `tiki_faqs` where `title`=? and `created`=?', array($title, (int) $this->now));
		}

		require_once('lib/search/refresh-functions.php');
		refresh_index('faqs', $faqId);

		return $faqId;
	}

    /**
     * @param int $faqId
     * @param int $offset
     * @param $maxRecords
     * @param string $sort_mode
     * @param string $find
     * @return array
     */
    function list_faq_questions($faqId=0, $offset=0, $maxRecords=-1, $sort_mode='question_asc', $find='')
	{
		if (!empty($faqId)) {
			$mid = ' where `faqId`=? ';
			$bindvars=array((int)$faqId);
		} else {
			$mid = '';
			$bindvars = array();
		}
		if (!empty($find)) {
			$findesc = '%' . $find . '%';
			if (empty($mid)) $mid = ' where ';
			$mid .= ' and (`question` like ? or `answer` like ?)';
			$bindvars=array($findesc, $findesc);
		}

		$query = "select * from `tiki_faq_questions` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_faq_questions` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res['parsed'] = $this->parse_data($res['answer']);

			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

    /**
     * @param $sfqId
     */
    function remove_suggested_question($sfqId)
	{
		$query = "delete from `tiki_suggested_faq_questions` where `sfqId`=?";

		$result = $this->query($query, array((int) $sfqId));
	}

    /**
     * @param $sfqId
     */
    function approve_suggested_question($sfqId)
	{
		$info = $this->get_suggested_question($sfqId);

		$this->replace_faq_question($info["faqId"], 0, $info["question"], $info["answer"]);
		$this->remove_suggested_question($sfqId);
	}

    /**
     * @param $sfqId
     * @return bool
     */
    function get_suggested_question($sfqId)
	{
		$query = "select * from `tiki_suggested_faq_questions` where `sfqId`=?";
		$result = $this->query($query, array($sfqId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}
}
