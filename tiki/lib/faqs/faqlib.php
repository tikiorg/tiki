<?php

/* Task properties:
   user, taskId, title, description, date, status, priority, completed, percentage
*/
class FaqLib extends TikiLib {
	function FaqLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to FAQLib constructor");
		}

		$this->db = $db;
	}

	function add_suggested_faq_question($faqId, $question, $answer, $user) {
		$question = addslashes(strip_tags($question, '<a>'));

		$answer = addslashes(strip_tags($answer, '<a>'));
		$now = date("U");
		$query = "insert into tiki_suggested_faq_questions(faqId,question,answer,user,created)
    values($faqId,'$question','$answer','$user',$now)";
		$result = $this->query($query);
	}

	function list_suggested_questions($offset, $maxRecords, $sort_mode, $find) {
		$sort_mode = str_replace("_", " ", $sort_mode);

		if ($find) {
			$findesc = $this->qstr('%' . $find . '%');

			$mid = " where (question like $findesc or answer like $findesc)";
		} else {
			$mid = "";
		}

		$query = "select * from tiki_suggested_faq_questions $mid order by $sort_mode limit $offset,$maxRecords";
		$query_cant = "select count(*) from tiki_suggested_faq_questions $mid";
		$result = $this->query($query);
		$cant = $this->getOne($query_cant);
		$ret = array();

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_all_faq_questions($offset, $maxRecords, $sort_mode, $find) {
		$sort_mode = str_replace("_", " ", $sort_mode);

		if ($find) {
			$findesc = $this->qstr('%' . $find . '%');

			$mid = " where (question like $findesc or answer like $findesc)";
		} else {
			$mid = "";
		}

		$query = "select * from tiki_faq_questions $mid order by $sort_mode limit $offset,$maxRecords";
		$query_cant = "select count(*) from tiki_faq_questions $mid";
		$result = $this->query($query);
		$cant = $this->getOne($query_cant);
		$ret = array();

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function remove_faq($faqId) {
		$query = "delete from tiki_faqs where faqId=$faqId";

		$result = $this->query($query);
		$query = "delete from tiki_faq_questions where faqId=$faqId";
		$result = $this->query($query);
		// Remove comments and/or individual permissions for faqs
		$this->remove_object('faq', $faqId);
		return true;
	}

	function remove_faq_question($questionId) {
		$faqId = $this->getOne("select faqId from tiki_faq_questions where questionId=$questionId");

		$query = "delete from tiki_faq_questions where questionId=$questionId";
		$result = $this->query($query);
		$query = "update tiki_faqs set questions=questions-1 where faqId=$faqId";
		$result = $this->query($query);
		return true;
	}

	function get_faq_question($questionId) {
		$query = "select * from tiki_faq_questions where questionId=$questionId";

		$result = $this->query($query);

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		return $res;
	}

	function add_faq_hit($faqId) {
		global $count_admin_pvs;

		global $user;

		if ($count_admin_pvs == 'y' || $user != 'admin') {
			$query = "update tiki_faqs set hits=hits+1 where faqId=$faqId";

			$result = $this->query($query);
		}
	}

	function replace_faq_question($faqId, $questionId, $question, $answer) {
		$question = addslashes($question);

		$answer = addslashes($answer);
		// Check the name
		if ($questionId) {
			$query = "update tiki_faq_questions set question='$question',answer='$answer' where questionId=$questionId";
		} else {
			$query = "update tiki_faqs set questions=questions+1 where faqId=$faqId";

			$result = $this->query($query);
			$query = "replace into tiki_faq_questions(faqId,question,answer)
                values($faqId,'$question','$answer')";
		}

		$result = $this->query($query);
		return true;
	}

	function replace_faq($faqId, $title, $description, $canSuggest) {
		$description = addslashes($description);

		$title = addslashes($title);
		// Check the name
		if ($faqId) {
			$query = "update tiki_faqs set title='$title',description='$description',canSuggest='$canSuggest' where faqId=$faqId";

			$result = $this->query($query);
		} else {
			$now = date("U");

			$query = "replace into tiki_faqs(title,description,created,hits,questions,canSuggest)
                values('$title','$description',$now,0,0,'$canSuggest')";
			$result = $this->query($query);
			$faqId = $this->getOne("select max(faqId) from tiki_faqs where title='$title' and created=$now");
		}

		return $faqId;
	}

	function list_faq_questions($faqId, $offset, $maxRecords, $sort_mode, $find) {
		$sort_mode = str_replace("_", " ", $sort_mode);

		if ($find) {
			$findesc = $this->qstr('%' . $find . '%');

			$mid = " where faqId=$faqId and (question like $findesc or answer like $findesc)";
		} else {
			$mid = " where faqId=$faqId ";
		}

		$query = "select * from tiki_faq_questions $mid order by $sort_mode limit $offset,$maxRecords";
		$query_cant = "select count(*) from tiki_faq_questions $mid";
		$result = $this->query($query);
		$cant = $this->getOne($query_cant);
		$ret = array();

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$res['parsed'] = $this->parse_data($res['answer']);

			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function remove_suggested_question($sfqId) {
		$query = "delete from tiki_suggested_faq_questions where sfqId=$sfqId";

		$result = $this->query($query);
	}

	function approve_suggested_question($sfqId) {
		$info = $this->get_suggested_question($sfqId);

		$this->replace_faq_question($info["faqId"], 0, $info["question"], $info["answer"]);
		$this->remove_suggested_question($sfqId);
	}

	function get_suggested_question($sfqId) {
		$query = "select * from tiki_suggested_faq_questions where sfqId=$sfqId";

		$result = $this->query($query);

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		return $res;
	}
}

$faqlib = new FaqLib($dbTiki);

?>