<?php

include_once ('lib/diff.php');

require_once ('lib/pear/Date.php');
include_once ('lib/pear/HTTP/Request.php');
require_once ('lib/tikidate.php');
require_once ('lib/tikidblib.php');

// This class is included by all the Tiki php scripts, so it's important
// to keep the class as small as possible to improve performance.
// What goes in this class:
// * generic functions that MANY scripts must use
// * shared functions (marked as /*shared*/) are functions that are
//   called from Tiki modules.
class TikiLib extends TikiDB {
    var $db; // The ADODB db object used to access the database

    var $buffer;
    var $flag;
    var $parser;
    var $pre_handlers = array();
    var $pos_handlers = array();

    var $num_queries = 0;

    // Constructor receiving a PEAR::Db database object.
    function TikiLib($db) {
	if (!$db) {
	    die ("Invalid db object passed to TikiLib constructor");
	}

	$this->db = $db;
    }


/*shared*/
function httprequest($url, $reqmethod = HTTP_REQUEST_METHOD_GET) {
	  global $use_proxy,$proxy_host,$proxy_port;
		// test url :
		if (!preg_match("/^[-_a-zA-Z0-9:\/\.\?&;=\+]*$/",$url)) return false;
	  // rewrite url if sloppy # added a case for https urls
	  if ( (substr($url,0,7) <> "http://") and
	       (substr($url,0,8) <> "https://")
	     ) {
			$url = "http://" . $url;
	  }
	  // (cdx) params for HTTP_Request.
	  // The timeout may be defined by a DEFINE("HTTP_TIMEOUT",5) in some file...
	  $aSettingsRequest=array("method"=>$reqmethod,"timeout"=>5);
	
	  if (substr_count($url, "/") < 3) {
			$url .= "/";
	  }
	  // Proxy settings
	  if ($use_proxy == 'y') {
			$aSettingsRequest["proxy_host"]=$proxy_host;
			$aSettingsRequest["proxy_port"]=$proxy_port;
	  }
	  $req = &new HTTP_Request($url, $aSettingsRequest);
	  // (cdx) return false when can't connect
	  // I prefer throw a PEAR_Error. You decide ;)
	  if (PEAR::isError($oError=$req->sendRequest())) {
			return false;
	  } 
	  $data = $req->getResponseBody();
	  return $data;
}

/*shared*/
function get_dsn_by_name($name) {
    return $this->getOne("select `dsn`  from `tiki_dsn` where `name`='$name'");
}

/* convert data to iso-8601 format */
function iso_8601 ($timestamp) { 
	$main_date = date("Y-m-d\TH:i:s", $timestamp); 
	
	$tz = date("O", $timestamp); 
	$tz = substr_replace ($tz, ':', 3, 0); 
	
	$return = $main_date . $tz; 
	
	return $return; 
}

/*shared*/
function check_rules($user, $section) {
    // Admin is never banned
    if ($user == 'admin')
	return false;

    $ips = explode('.', $_SERVER["REMOTE_ADDR"]);
    $now = date("U");
    $query = "select tb.`message`,tb.`user`,tb.`ip1`,tb.`ip2`,tb.`ip3`,tb.`ip4`,tb.`mode` from `tiki_banning` tb, `tiki_banning_sections` tbs where tbs.`banId`=tb.`banId` and tbs.`section`=? and ( (tb.`use_dates` = ?) or (tb.`date_from` <= ? and tb.`date_to` >= ?))";
    $result = $this->query($query,array($section,'n',(int)$now,(int)$now));

    while ($res = $result->fetchRow()) {
	if (!$res['message']) {
	    $res['message'] = tra('You are banned from'). ':' . $section;
	}

	if ($user && $res['mode'] == 'user') {
	    // check user
	    $pattern = '/' . $res['user'] . '/';

	    if (preg_match($pattern, $user)) {
		return $res['message'];
	    }
	} else {
	    // check ip
	    if (count($ips) == 4) {
		if (($ips[0] == $res['ip1'] || $res['ip1'] == '*') && ($ips[1] == $res['ip2'] || $res['ip2'] == '*')
			&& ($ips[2] == $res['ip3'] || $res['ip3'] == '*') && ($ips[3] == $res['ip4'] || $res['ip4'] == '*')) {
		    return $res['message'];
		}
	    }
	}
    }

    return false;
}

/*shared*/
function replace_note($user, $noteId, $name, $data) {
    $now = date("U");
    $size = strlen($data);

    if ($noteId) {
	$query = "update `tiki_user_notes` set `name` = ?, `data` = ?, `size` = ?, `lastModif` = ?  where `user`=? and `noteId`=?";
	$this->query($query,array($name,$data,(int)$size,(int)$now,$user,(int)$noteId));
	return $noteId;
    } else {
	$query = "insert into `tiki_user_notes`(`user`,`noteId`,`name`,`data`,`created`,`lastModif`,`size`) values(?,?,?,?,?,?,?)";
	$this->query($query,array($user,(int)$noteId,$name,$data,(int)$now,(int)$now,(int)$size));
	$noteId = $this->getOne( "select max(`noteId`) from `tiki_user_notes` where `user`=? and `name`=? and `created`=?",array($user,$name,(int)$now));
	return $noteId;
    }
}

/*shared*/
function add_user_watch($user, $event, $object, $type, $title, $url) {
    global $userlib;

    $hash = md5(uniqid('.'));
    $email = $userlib->get_user_email($user);
    $query = "delete from `tiki_user_watches` where `user`=? and `event`=? and `object`=?";
    $this->query($query,array($user,$event,$object));
    $query = "insert into `tiki_user_watches`(`user`,`event`,`object`,`email`,`hash`,`type`,`title`,`url`) ";
    $query.= "values(?,?,?,?,?,?,?,?)";
    $this->query($query,array($user,$event,$object,$email,$hash,$type,$title,$url));
    return true;
}

/*shared*/
function remove_user_watch_by_hash($hash) {
    $query = "delete from `tiki_user_watches` where `hash`=?";
    $this->query($query,array($hash));
}

/*shared*/
function remove_user_watch($user, $event, $object) {
    $query = "delete from `tiki_user_watches` where `user`=? and `event`=? and `object`=?";
    $this->query($query,array($user,$event,$object));
}

/*shared*/
function get_user_watches($user, $event = '') {
    $mid = '';
    $bindvars=array($user);
    if ($event) {
	$mid = " and `event`=? ";
	$bindvars[]=$event;
    }

    $query = "select * from `tiki_user_watches` where `user`=? $mid";
    $result = $this->query($query,$bindvars);
    $ret = array();

    while ($res = $result->fetchRow()) {
	$ret[] = $res;
    }
    return $ret;
}

/*shared*/
function get_watches_events() {
    $query = "select distinct `event` from `tiki_user_watches`";
    $result = $this->query($query,array());
    $ret = array();
    while ($res = $result->fetchRow()) {
	$ret[] = $res['event'];
    }
    return $ret;
}

/*shared*/
function get_user_event_watches($user, $event, $object) {
    $query = "select * from `tiki_user_watches` where `user`=? and `event`=? and `object`=?";
    $result = $this->query($query,array($user,$event,$object));
    if (!$result->numRows()) return false;
    $res = $result->fetchRow();
    return $res;
}

/*shared*/
function get_event_watches($event, $object) {
    $ret = array();

    $query = "select * from `tiki_user_watches` where `event`=? and `object`=?";
    $result = $this->query($query,array($event,$object));

    if (!$result->numRows())
	return $ret;

    while ($res = $result->fetchRow()) {
	$ret[] = $res;
    }

    return $ret;
}

/*shared*/
function replace_task($user, $taskId, $title, $description, $date, $status, $priority, $completed, $percentage) {
    if ($taskId) {
	$query = "update `tiki_user_tasks` set `title` = ?, `description` = ?, `date` = ?, `status` = ?, `priority` = ?, ";
	$query.= "`percentage` = ?, `completed` = ?  where `user`=? and `taskId`=?";
	$this->query($query,array($title,$description,$date,$status,$priority,$percentage,$completed,$user,$taskId));
	return $taskId;
    } else {
	$query = "insert into `tiki_user_tasks`(`user`,`taskId`,`title`,`description`,`date`,`status`,`priority`,`completed`,`percentage`) ";
	$query.= " values(?,?,?,?,?,?,?,?,?)";

	$this->query($query,array($user,$taskId,$title,$description,$date,$status,$priority,$completed,$percentage));
	$taskId = $this->getOne( "select  max(`taskId`) from `tiki_user_tasks` where `user`=? and `title`=? and `date`=?",array($user,$title,$date));
	return $taskId;
    }
}

/*shared*/
function complete_task($user, $taskId) {
    $now = date("U");
    $query = "update `tiki_user_tasks` set `completed`=?, `status`='c', `percentage`=100 where `user`=? and `taskId`=?";
    $this->query($query,array((int)$now,$user,(int)$taskId));
}

/*shared*/
function remove_task($user, $taskId) {
    $query = "delete from `tiki_user_tasks` where `user`=? and `taskId`=?";
    $this->query($query,array($user,(int)$taskId));
}

/*shared*/
function list_tasks($user, $offset, $maxRecords, $sort_mode, $find, $use_date, $pdate) {
    $now = date("U");
    $bindvars=array($user);
    if ($use_date == 'y') {
	$prio = " and date<=? ";
	$bindvars2=$pdate;
    } else {
	$prio = '';
    }

    if ($find) {
	$findesc = '%' . $find . '%';

	$mid = " and (`title` like $findesc or `description` like $findesc)";
	$bindvars[]=$findesc;
	$bindvars[]=$findesc;
    } else {
	$mid = "" ;
    }

    $mid.=$prio;
    if(isset($bindvars2)) $bindvars[]=$bindvars2;

    $query = "select * from `tiki_user_tasks` where `user`=? $mid order by ".$this->convert_sortmode($sort_mode).",`taskId` desc";
    $query_cant = "select count(*) from `tiki_user_tasks` where `user`=? $mid";
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

/*shared*/
function dir_stats() {
    $aux = array();
    $aux["valid"] = $this->db->getOne("select count(*) from `tiki_directory_sites` where `isValid`=?",array('y'));
    $aux["invalid"] = $this->db->getOne("select count(*) from `tiki_directory_sites` where `isValid`=?",array('n'));
    $aux["categs"] = $this->db->getOne("select count(*) from `tiki_directory_categories`",array());
    $aux["searches"] = $this->db->getOne("select sum(`hits`) from `tiki_directory_search`",array());
    $aux["visits"] = $this->db->getOne("select sum(`hits`) from `tiki_directory_sites`",array());
    return $aux;
}

/*shared*/
function dir_list_all_valid_sites2($offset, $maxRecords, $sort_mode, $find) {

    if ($find) {
	$mid = " where `isValid`=? and (`name` like ? or `description` like ?)";
	$bindvars=array('y','%'.$find.'%','%'.$find.'%');
    } else {
	$mid = " where `isValid`=? ";
	$bindvars=array('y');
    }

    $query = "select * from `tiki_directory_sites` $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_directory_sites` $mid";
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

/*shared*/
function get_directory($categId) {
    $query = "select * from `tiki_directory_categories` where `categId`=?";
    $result = $this->query($query,array($categId));
    if (!$result->numRows()) return false;
    $res = $result->fetchRow();
    return $res;
}

/*shared*/
function user_unread_messages($user) {
    $cant = $this->getOne("select count(*) from `messu_messages` where `user`=? and `isRead`=?",array($user,'n'));
    return $cant;
}

/*shared*/
function get_online_users() {
    $query = "select `user` ,`timestamp` from `tiki_sessions` where `user`<>?";
    $result = $this->query($query,array(''));
    $ret = array();
    while ($res = $result->fetchRow()) {
	$res['user_information'] = $this->get_user_preference($res['user'], 'user_information', 'public');
	$ret[] = $res;
    }
    return $ret;
}

/*shared*/
function get_user_items($user) {
    $items = array();

    $query = "select ttf.`trackerId`, tti.`itemId` from `tiki_tracker_fields` ttf, `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif";
    $query .= " where ttf.`fieldId`=ttif.`fieldId` and ttif.`itemId`=tti.`itemId` and `type`=? and tti.`status`=? and `value`=?";
    $result = $this->query($query,array('u','o',$user));
    $ret = array();

    while ($res = $result->fetchRow()) {
	$itemId = $res["itemId"];

	$trackerId = $res["trackerId"];
	// Now get the isMain field for this tracker
	$fieldId = $this->getOne("select `fieldId`  from `tiki_tracker_fields` ttf where `isMain`=? and `trackerId`=?",array('y',(int)$trackerId));
	// Now get the field value
	$value = $this->getOne("select `value`  from `tiki_tracker_item_fields` where `fieldId`=? and `itemId`=?",array((int)$fieldId,(int)$itemId));
	$tracker = $this->getOne("select `name`  from `tiki_trackers` where `trackerId`=?",array((int)$trackerId));
	$aux["trackerId"] = $trackerId;
	$aux["itemId"] = $itemId;
	$aux["value"] = $value;
	$aux["name"] = $tracker;

	if (!in_array($itemId, $items)) {
	    $ret[] = $aux;
	    $items[] = $itemId;
	}
    }

    $groups = $this->get_user_groups($user);

    foreach ($groups as $group) {
	$query = "select ttf.`trackerId`, tti.`itemId` from `tiki_tracker_fields` ttf, `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif ";
	$query .= " where ttf.`fieldId`=ttif.`fieldId` and ttif.`itemId`=tti.`itemId` and `type`=? and tti.`status`=? and value=?";
	$result = $this->query($query,array('g','o',$group));

	while ($res = $result->fetchRow()) {
	    $itemId = $res["itemId"];

	    $trackerId = $res["trackerId"];
	    // Now get the isMain field for this tracker
	    $fieldId = $this->getOne("select `fieldId`  from `tiki_tracker_fields` ttf where `isMain`=? and `trackerId`=?",array('y',(int)$trackerId));
	    // Now get the field value
	    $value = $this->getOne("select `value`  from `tiki_tracker_item_fields` where `fieldId`=? and `itemId`=?",array((int)$fieldId,(int)$itemId));
	    $tracker = $this->getOne("select `name`  from `tiki_trackers` where `trackerId`=?",array((int)$trackerId));
	    $aux["trackerId"] = $trackerId;
	    $aux["itemId"] = $itemId;
	    $aux["value"] = $value;
	    $aux["name"] = $tracker;

	    if (!in_array($itemId, $items)) {
		$ret[] = $aux;
		$items[] = $itemId;
	    }
	}
    }

    return $ret;
}

/*shared*/
function get_actual_content($contentId) {
    $data = '';

    $now = date("U");
    $query = "select max(`publishDate`) from `tiki_programmed_content` where `contentId`=? and `publishDate`<=?";
    $res = $this->getOne($query,array((int)$contentId,$now));

    if (!$res)
	return '';

    $query = "select `data`  from `tiki_programmed_content` where `contentId`=? and `publishDate`=?";
    $data = $this->getOne($query,array((int)$contentId,$res));
    return $data;
}

/*shared*/
function get_quiz($quizId) {
    $query = "select * from `tiki_quizzes` where `quizId`=?";

    $result = $this->query($query,array((int) $quizId));

    if (!$result->numRows())
	return false;

    $res = $result->fetchRow();
    return $res;
}


function compute_quiz_stats() {
    $query = "select `quizId`  from `tiki_user_quizzes`";

    $result = $this->query($query,array());

    while ($res = $result->fetchRow()) {
	$quizId = $res["quizId"];

	$quizName = $this->getOne("select `name`  from `tiki_quizzes` where `quizId`=?",array((int)$quizId));
	$timesTaken = $this->getOne("select count(*) from `tiki_user_quizzes` where `quizId`=?",array((int)$quizId));
	$avgpoints = $this->getOne("select avg(`points`) from `tiki_user_quizzes` where `quizId`=?",array((int)$quizId));
	$maxPoints = $this->getOne("select max(`maxPoints`) from `tiki_user_quizzes` where `quizId`=?",array((int)$quizId));
	$avgavg = ($maxPoints != 0) ? $avgpoints / $maxPoints * 100 : 0.0;
	$avgtime = $this->getOne("select avg(`timeTaken`) from `tiki_user_quizzes` where `quizId`=?",array((int)$quizId));
	$querydel = "delete from `tiki_quiz_stats_sum` where `quizId`=?";
	$resultdel = $this->query($querydel,array((int)$quizId),-1,-1,false);
	$query2 = "insert into `tiki_quiz_stats_sum`(`quizId`,`quizName`,`timesTaken`,`avgpoints`,`avgtime`,`avgavg`)
	    values(?,?,?,?,?,?)";
	$result2 = $this->query($query2,array((int)$quizId,$quizName,(int)$timesTaken,(float)$avgpoints,$avgtime,$avgavg));
    }
}


/*shared*/
function list_quizzes($offset, $maxRecords, $sort_mode, $find) {

    if ($find) {
	$findesc = '%' . $find . '%';

	$mid = " where (`name` like $findesc or `description` like $findesc)";
	$bindvars = array($findesc,$findesc);
    } else {
	$mid = " ";
	$bindvars=array();
    }

    $query = "select * from `tiki_quizzes` $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_quizzes` $mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = array();

    while ($res = $result->fetchRow()) {
	$res["questions"] = $this->getOne("select count(*) from `tiki_quiz_questions` where `quizId`=?",array((int) $res["quizId"]));

	$res["results"] = $this->getOne("select count(*) from `tiki_quiz_results` where `quizId`=?",array((int) $res["quizId"]));
	$ret[] = $res;
    }

    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
}

/*shared*/
function list_quiz_sum_stats($offset, $maxRecords, $sort_mode, $find) {
    $this->compute_quiz_stats();

    if ($find) {
	$findesc = '%' . $find . '%';

	$mid = "  `quizName` like ? ";
	$bindvars=array($findesc);
    } else {
	$mid = "  ";
	$bindvars=array();
    }

    $query = "select * from `tiki_quiz_stats_sum` $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_quiz_stats_sum` $mid";
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

function get_tracker($trackerId) {
		$query = "select * from `tiki_trackers` where `trackerId`=?";

		$result = $this->query($query,array((int) $trackerId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}
/*shared*/


function list_trackers($offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`name` like ? or `description` like ?)";
			$bindvars=array($findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		$query = "select * from `tiki_trackers` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_trackers` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			// Tracker fields are automatically counted when adding/removing fields to trackers
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

/*shared*/
function list_surveys($offset, $maxRecords, $sort_mode, $find) {

    if ($find) {
	$findesc = '%' . $find . '%';

	$mid = " where (`name` like ? or `description` like ?)";
	$bindvars=array($findesc,$findesc);
    } else {
	$mid = " ";
	$bindvars=array();
    }

    $query = "select * from `tiki_surveys` $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_surveys` $mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = array();

    while ($res = $result->fetchRow()) {
	$res["questions"] = $this->getOne("select count(*) from `tiki_survey_questions` where `surveyId`=?",array((int) $res["surveyId"]));

	$ret[] = $res;
    }

    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
}

/*shared*/
function list_tracker_items($trackerId, $offset, $maxRecords, $sort_mode, $fields, $status = '') {
    $filters = array();

    if ($fields) {
	for ($i = 0; $i < count($fields["data"]); $i++) {
	    $fieldId = $fields["data"][$i]["fieldId"];

	    $type = $fields["data"][$i]["type"];
	    $value = $fields["data"][$i]["value"];
	    $aux["value"] = $value;
	    $aux["type"] = $type;
	    $filters[$fieldId] = $aux;
	}
    }

    $mid = " where `trackerId`=? ";
    $bindvars = array((int)$trackerId);

    if ($status) {
	$mid .= " and `status`=? ";
	$bindvars[] = $status;
    }

    $query = "select * from `tiki_tracker_items` $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_tracker_items` $mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant, $bindvars);
    $ret = array();

    while ($res = $result->fetchRow()) {
	$fields = array();

	$itid = $res["itemId"];
	$query2 = "select ttif.`fieldId`,`name`,`value`,`type`,`isTblVisible`,`isMain` from `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf ";
	$query2.= " where ttif.`fieldId`=ttf.`fieldId` and `itemId`=? order by ".$this->convert_sortmode("fieldId_asc");
	$result2 = $this->query($query2,array((int) $res["itemId"]));
	$pass = true;

	while ($res2 = $result2->fetchRow()) {
	    // Check if the field is visible!
	    $fieldId = $res2["fieldId"];

	    if (count($filters) > 0) {
		if ($filters["$fieldId"]["value"]) {
		    if ($filters["$fieldId"]["type"] == 'a' || $filters["$fieldId"]["type"] == 't') {
			if (!strstr($res2["value"], $filters["$fieldId"]["value"]))
			    $pass = false;
		    } else {
			if ($res2["value"] != $filters["$fieldId"]["value"])
			    $pass = false;
		    }
		}
	    }

	    $fields[] = $res2;
	}

	$res["field_values"] = $fields;
	$res["comments"] = $this->getOne("select count(*) from `tiki_tracker_item_comments` where `itemId`=?",array((int)$itid));

	if ($pass)
	    $ret[] = $res;
    }

    //$ret=$this->sort_items_by_condition($ret,$sort_mode);
    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
}

/*shared*/
// \todo remove all hardcoded html in get_user_avatar()
    function get_user_avatar($user, $float = "") {
	if (empty($user))
	    return '';

	if (!$this->user_exists($user)) {
	    return '';
	}

	$type = $this->getOne("select `avatarType`  from `users_users` where `login`=?",array($user));
	$libname = $this->getOne("select `avatarLibName`  from `users_users` where `login`=?",array($user));
	$ret = '';
	$style = '';

	if (strcasecmp($float, "left") == 0) {
	    $style = "style='float:left;margin-right:5px;'";
	} else if (strcasecmp($float, "right") == 0) {
	    $style = "style='float:right;margin-left:5px;'";
	}

	switch ($type) {
	    case 'n':
		$ret = '';
		break;

	    case 'l':
		$ret = "<img border='0' width='45' height='45' src='" . $libname . "' " . $style . " alt=\"$user\"/>";
		break;

	    case 'u':
		$ret = "<img border='0' width='45' height='45' src='tiki-show_user_avatar.php?user=$user' " . $style . " alt=\"$user\"/>";
		break;
	}

	return $ret;
    }

/*shared*/
function get_forum_sections() {
    $query = "select distinct `section` from `tiki_forums` where `section`<>?";
    $result = $this->query($query,array(''));
    $ret = array();

    while ($res = $result->fetchRow()) {
	$ret[] = $res["section"];
    }

    return $ret;
}

/* Referer stats */
/*shared*/
function register_referer($referer) {
    $now = date("U");
    $cant = $this->getOne("select count(*) from `tiki_referer_stats` where `referer`=?",array($referer));

    if ($cant) {
	$query = "update `tiki_referer_stats` set `hits`=`hits`+1,`last`=? where `referer`=?";
    } else {
	$query = "insert into `tiki_referer_stats`(`last`,`referer`,`hits`) values(?,?,1)";
    }

    $result = $this->query($query,array((int)$now,$referer));
}

// File attachments functions for the wiki ////
/*shared*/
function add_wiki_attachment_hit($id) {
    global $count_admin_pvs, $user;
    if ($count_admin_pvs == 'y' || $user != 'admin') {
	$query = "update `tiki_wiki_attachments` set `downloads`=`downloads`+1 where `attId`=?";
	$result = $this->query($query,array((int)$id));
    }
    return true;
}

/*shared*/
function get_wiki_attachment($attId) {
    $query = "select * from `tiki_wiki_attachments` where `attId`=?";
    $result = $this->query($query,array((int)$attId));
    if (!$result->numRows()) return false;
    $res = $result->fetchRow();
    return $res;
}

/*shared*/
function get_random_image($galleryId = -1) {
    $whgal = "";
    $bindvars = array();
    if (((int)$galleryId) != -1) {
	$whgal = " where `galleryId`=? ";
	$bindvars[] = (int) $galleryId;
    }

    $query = "select count(*) from `tiki_images` $whgal";
    $cant = $this->getOne($query,$bindvars);
    $ret = array();

    if ($cant) {
	$pick = rand(0, $cant - 1);

	$query = "select `imageId` ,`galleryId`,`name` from `tiki_images` $whgal";
	$result = $this->query($query,$bindvars,1,$pick);
	$res = $result->fetchRow();
	$ret["galleryId"] = $res["galleryId"];
	$ret["imageId"] = $res["imageId"];
	$ret["name"] = $res["name"];
	$query = "select `name`  from `tiki_galleries` where `galleryId` = ?";
	$ret["gallery"] = $this->getOne($query,array((int)$res["galleryId"]));
    } else {
	$ret["galleryId"] = 0;

	$ret["imageId"] = 0;
	$ret["name"] = tra("No image yet, sorry.");
    }

    return ($ret);
}

/*shared*/
function get_gallery($id) {
    $query = "select * from `tiki_galleries` where `galleryId`=?";
    $result = $this->query($query,array((int) $id));
    $res = $result->fetchRow();
    return $res;
}

// Last visit module ////
/*shared*/
function get_news_from_last_visit($user) {
    if (!$user) return false;

    $last = $this->getOne("select `lastLogin`  from `users_users` where `login`=?",array($user));
    $ret = array();

    if (!$last) {
	$last = time();
    }
    $ret["lastVisit"] = $last;
    $ret["images"] = $this->getOne("select count(*) from `tiki_images` where `created`>?",array((int)$last));
    $ret["pages"] = $this->getOne("select count(*) from `tiki_pages` where `lastModif`>?",array((int)$last));
    $ret["files"] = $this->getOne("select count(*) from `tiki_files` where `created`>?",array((int)$last));
    $ret["comments"] = $this->getOne("select count(*) from `tiki_comments` where `commentDate`>?",array((int)$last));
    $ret["users"] = $this->getOne("select count(*) from `users_users` where `registrationDate`>?",array((int)$last));
    return $ret;
}

// Templates ////
/*shared*/
function list_templates($section, $offset, $maxRecords, $sort_mode, $find) {
    $bindvars = array($section);
    if ($find) {
	$findesc = '%'.$find.'%';
	$mid = " and (`content` like ?)";
	$bindvars[] = $findesc;
    } else {
	$mid = "";
    }

    $query = "select `name` ,`created`,tcts.`templateId` from `tiki_content_templates` tct, `tiki_content_templates_sections` tcts ";
    $query.= " where tcts.`templateId`=tct.`templateId` and `section`=? $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_content_templates` tct, `tiki_content_templates_sections` tcts ";
    $query_cant.= "where tcts.`templateId`=tct.`templateId` and `section`=? $mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = array();

    while ($res = $result->fetchRow()) {
	$query2 = "select `section`  from `tiki_content_templates_sections` where `templateId`=?";

	$result2 = $this->query($query2,array((int)$res["templateId"]));
	$sections = array();

	while ($res2 = $result2->fetchRow()) {
	    $sections[] = $res2["section"];
	}

	$res["sections"] = $sections;
	$ret[] = $res;
    }

    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
}

/*shared*/
function get_template($templateId) {
    $query = "select * from `tiki_content_templates` where `templateId`=?";
    $result = $this->query($query,array((int)$templateId));
    if (!$result->numRows()) return false;
    $res = $result->fetchRow();
    return $res;
}
// templates ////

/*shared*/
function list_games($offset, $maxRecords, $sort_mode, $find) {
    $bindvars = array();
    if ($find) {
	$findesc = '%'.$find.'%';
	$mid = " where (`gameName` like ?)";
	$bindvars[] = $findesc;
    } else {
	$mid = "";
    }

    $query = "select * from `tiki_games` $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_games` $mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = array();

    while ($res = $result->fetchRow()) {
	$parts = explode('.', $res["gameName"]);

	$res["thumbName"] = $parts[0];
	$ret[] = $res;
    }

    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
}

/*shared*/
function pick_cookie() {
    $cant = $this->getOne("select count(*) from `tiki_cookies`",array());
    if (!$cant) return '';

    $bid = rand(0, $cant - 1);
    //$cookie = $this->getOne("select `cookie`  from `tiki_cookies` limit $bid,1"); getOne seems not to work with limit
    $result = $this->query("select `cookie`  from `tiki_cookies`",array(),1,$bid);
    if ($res = $result->fetchRow()) {
	$cookie = str_replace("\n", "", $res['cookie']);
	return '<i>"' . $cookie . '"</i>';
    }
    else
	return "";
}

// Stats ////
/*shared*/
function add_pageview() {
    $dayzero = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
    $cant = $this->getOne("select count(*) from `tiki_pageviews` where `day`=?",array((int)$dayzero));

    if ($cant) {
	$query = "update `tiki_pageviews` set `pageviews`=`pageviews`+1 where `day`=?";
    } else {
	$query = "insert into `tiki_pageviews`(`day`,`pageviews`) values(?,1)";
    }
    $result = $this->query($query,array((int)$dayzero));
}

function get_pv_chart_data($days) {
    $now = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
    $dfrom = 0;
    if ($days != 0) $dfrom = $now - ($days * 24 * 60 * 60);

    $query = "select `day`, `pageviews` from `tiki_pageviews` where `day`<=? and `day`>=?";
    $result = $this->query($query,array((int)$now,(int)$dfrom));
    $ret = array();
    $n = ceil($result->numRows() / 10);
    $i = 0;

    while ($res = $result->fetchRow()) {
	if ($i % $n == 0) {
	    $data = array(
		    date("j M", $res["day"]),
		    $res["pageviews"]
		    );
	} else {
	    $data = array(
		    "",
		    $res["pageviews"]
		    );
	}

	$i++;
	$ret[] = $data;
    }

    return $ret;
}

function get_usage_chart_data() {
    $this->compute_quiz_stats();
    $data[] = array( "wiki",   $this->getOne("select sum(`hits`) from `tiki_pages`",array()));
    $data[] = array( "img-g",  $this->getOne("select sum(`hits`) from `tiki_galleries`",array()));
    $data[] = array( "file-g", $this->getOne("select sum(`hits`) from `tiki_file_galleries`",array()));
    $data[] = array( "faqs",   $this->getOne("select sum(`hits`) from `tiki_faqs`",array()));
    $data[] = array( "quizzes",$this->getOne("select sum(`timesTaken`) from `tiki_quiz_stats_sum`",array()));
    $data[] = array( "arts",   $this->getOne("select sum(`reads`) from `tiki_articles`",array()));
    $data[] = array( "blogs",  $this->getOne("select sum(`hits`) from `tiki_blogs`",array()));
    $data[] = array( "forums", $this->getOne("select sum(`hits`) from `tiki_forums`",array()));
    $data[] = array( "games",  $this->getOne("select sum(`hits`) from `tiki_games`",array()));
    return $data;
}

// User assigned modules ////
/*shared*/
function get_user_id($user) {
    $id = $this->getOne("select `userId` from `users_users` where `login`=?", array($user),-1,-1,false);
    return $id;
}

/*shared*/
function get_user_groups($user) {
    $userid = $this->get_user_id($user);
    $query = "select `groupName`  from `users_usergroups` where `userId`=?";
    $result=$this->query($query,array((int) $userid));
    $ret = array();
    while ($res = $result->fetchRow()) {
	$ret[] = $res["groupName"];
    }
    $ret[] = "Anonymous";
    return $ret;
}

// Functions for FAQs ////
/*shared*/
function list_faqs($offset, $maxRecords, $sort_mode, $find) {

    if ($find) {
	$findesc = '%' . $find . '%';
	$mid = " where (`title` like ? or `description` like ?)";
	$bindvars=array($findesc,$findesc);
    } else {
	$mid = "";
	$bindvars=array();
    }
    $query = "select * from `tiki_faqs` $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_faqs` $mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = array();

    while ($res = $result->fetchRow()) {
	$res["suggested"] = $this->getOne("select count(*) from `tiki_suggested_faq_questions` where `faqId`=?",array((int) $res["faqId"]));

	$ret[] = $res;
    }

    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
}

/*shared */
function get_faq($faqId) {
    $query = "select * from `tiki_faqs` where `faqId`=?";
    $result = $this->query($query,array((int)$faqId));
    if (!$result->numRows()) return false;
    $res = $result->fetchRow();
    return $res;
}
// End Faqs ////

/*shared*/
function genPass() {
    $vocales = "aeiouAEIOU";
    $consonantes = "bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ0123456789_";
    $r = '';
    for ($i = 0; $i < 8; $i++) {
	if ($i % 2) {
	    $r .= $vocales{rand(0, strlen($vocales) - 1)};
	} else {
	    $r .= $consonantes{rand(0, strlen($consonantes) - 1)};
	}
    }
    return $r;
}

// This function calculates the pageRanks for the tiki_pages
// it can be used to compute the most relevant pages
// according to the number of links they have
// this can be a very interesting ranking for the Wiki
// More about this on version 1.3 when we add the pageRank
// column to tiki_pages
function pageRank($loops = 16) {
    $query = "select `pageName`  from `tiki_pages`";
    $result = $this->query($query,array());
    $ret = array();

    while ($res = $result->fetchRow()) {
	$ret[] = $res["pageName"];
    }

    // Now calculate the loop
    $pages = array();

    foreach ($ret as $page) {
	$val = 1 / count($ret);

	$pages[$page] = $val;
	// Fixed query.  -rlpowell
	$query = "update `tiki_pages` set `pageRank`=? where `pageName`= ?";
	$result = $this->query($query, array((int)$val, $page) );
    }

    for ($i = 0; $i < $loops; $i++) {
	foreach ($pages as $pagename => $rank) {
	    // Get all the pages linking to this one
	    // Fixed query.  -rlpowell
	    $query = "select `fromPage`  from `tiki_links` where `toPage` = ?";
	    $result = $this->query($query, array( $pagename ) );
	    $sum = 0;

	    while ($res = $result->fetchRow()) {
		$linking = $res["fromPage"];

		if (isset($pages[$linking])) {
		    // Fixed query.  -rlpowell
		    $q2 = "select count(*) from `tiki_links` where `fromPage`= ?";
		    $cant = $this->getOne($q2, array($linking) );
		    if ($cant == 0) $cant = 1;
		    $sum += $pages[$linking] / $cant;
		}
	    }

	    $val = (1 - 0.85) + 0.85 * $sum;
	    $pages[$pagename] = $val;
	    // Fixed query.  -rlpowell
	    $query = "update `tiki_pages` set `pageRank`=? where `pageName`=?";
	    $result = $this->query($query, array((int)$val, $pagename) );

	    // Update
	}
    }

    arsort ($pages);
    return $pages;
}

// Spellchecking routine
// Parameters:
// what: what to spell check (a text)
// where: where to replace (maybe the same text)
// language: language to use
// element: element where the text is going to be replaced (a textarea or similar)
/*shared*/
// \todo replace the hardcoded html by smarty template
function spellcheckreplace($what, $where, $language, $element) {
    global $smarty;

    $trl = '';
    $words = preg_split("/\s/", $what);

    foreach ($words as $word) {
	if (preg_match("/^[A-Z]?[a-z]+$/", $word) && strlen($word) > 1) {
	    $result = $this->spellcheckword($word, $language);

	    if (count($result) > 0) {
		// Replace the word with a warning color in the edit_data
		// Prepare the replacement
		$sugs = $result[$word];

		$first = 1;
		$repl = '';

		$popup_text = '';

		//foreach($sugs as $sug=>$lev) {
		//  if($first) {
		//	$repl.=' <span style="color:red;">'.$word.'</span>'.'<a title="'.$sug.'" style="text-decoration: none; color:red;" href="javascript:replaceSome(\'editwiki\',\''.$word.'\',\''.$sug.'\');">.</a>';
		//	$first = 0;
		//  } else {
		//	$repl.='<a title="'.$sug.'" style="text-decoration: none; color:red;" href="javascript:replaceSome(\'editwiki\',\''.$word.'\',\''.$sug.'\');">.</a>';
		//	//$repl.='|'.'<a style="color:red;" href="javascript:replaceSome(\'editwiki\',\''.$word.'\',\''.$sug.'\');">'.$sug.'</a>';
		//  }
		//}
		//if($repl) {
		//  $repl.=' ';
		//}
		if (count($sugs) > 0) {
		    $asugs = array_keys($sugs);

		    for ($i = 0; $i < count($asugs) && $i < 5; $i++) {
			$sug = $asugs[$i];

			// If you want to use the commented out line below, please remove the \ in <\/script>; it was breaking vim highlighting.  -rlpowell
			// $repl.="<script language='Javascript' type='text/javascript'>param_${word}_$i = new Array(\\\"$element\\\",\\\"$word\\\",\\\"$sug\\\");<\/script><a href=\\\"javascript:replaceLimon(param_${word}_$i);\\"."\">$sug</a><br/>";
			$repl .= "<a href=\\\"javascript:param=doo_${word}_$i();replaceLimon(param);\\\">$sug</a><br/>";
			$trl .= "<script language='Javascript' type='text/javascript'>function doo_${word}_$i(){ aux = new Array(\"$element\",\"$word\",\"$sug\"); return aux;}</script>";
		    }

		    //$popup_text = " <a title=\"".$sug."\" style=\"text-decoration:none; color:red;\" onClick='"."return overlib(".'"'.$repl.'"'.",STICKY,CAPTION,".'"'."SpellChecker suggestions".'"'.");'>".$word.'</a> ';
		    $popup_text = " <a title='$sug' style='text-decoration:none; color:red;' onClick='return overlib(\"" . $repl . "\",STICKY,CAPTION,\"Spellchecker suggestions\");'>$word</a> ";
		}

		//print("popup: <pre>".htmlentities($popup_text)."</pre><br/>");
		if ($popup_text) {
		    $where = preg_replace("/\s$word\s/", $popup_text, $where);
		} else {
		    $where = preg_replace("/\s$word\s/", ' <span style="color:red;">' . $word . '</span> ', $where);
		}

		$smarty->assign('trl', $trl);
		//$parsed = preg_replace("/\s$word\s/",' <a style="color:red;">'.$word.'</a> ',$parsed);
	    }
	}
    }

    return $where;
}

/*shared*/
function spellcheckword($word, $lang) {
    include_once ("bablotron.php");

    $b = new bablotron($this->db, $lang);
    $result = $b->spellcheck_word($word);
    return $result;
}

// \todo remove html hardcoded in diff2
function diff2($page1, $page2) {
    $page1 = split("\n", $page1);
    $page2 = split("\n", $page2);
    $z = new WikiDiff($page1, $page2);
    if ($z->isEmpty()) {
	$html = '<hr><br/>[' . tra("Versions are identical"). ']<br/><br/>';
    } else {
	//$fmt = new WikiDiffFormatter;
	$fmt = new WikiUnifiedDiffFormatter;
	$html = $fmt->format($z, $page1);
    }
    return $html;
}

/*shared*/
function get_forum($forumId) {
    $query = "select * from `tiki_forums` where `forumId`=?";
    $result = $this->query($query,array((int)$forumId));
    $res = $result->fetchRow();
    return $res;
}

/*shared*/
function list_all_forum_topics($offset, $maxRecords, $sort_mode, $find) {
    $bindvars = array("forum",0);
    if ($find) {
	$findesc = '%'.$find.'%';
	$mid = " and (`title` like ? or `data` like ?)";
	$bindvars[] = $findesc;
	$bindvars[] = $findesc;
    } else {
	$mid = "";
    }

    $query = "select * from `tiki_comments`,`tiki_forums` ";
    $query.= " where `object`=`forumId` and `objectType`=? and `parentId`=? $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_comments`,`tiki_forums` ";
    $query_cant.= " where `object`=`forumId` and `objectType`=? and `parentId`=? $mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$bindvars);
    $now = date("U");
    $ret = array();

    while ($res = $result->fetchRow()) {
	$ret[] = $res;
    }

    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
}

/*shared*/
function list_forum_topics($forumId, $offset, $maxRecords, $sort_mode, $find) {
    $bindvars = array($forumId,$forumId,'forum',0);
    if ($find) {
	$findesc = '%'.$find.'%';
	$mid = " and (`title` like ? or `data` like ?)";
	$bindvars[] = $findesc;
	$bindvars[] = $findesc;
    } else {
	$mid = "";
    }

    $query = "select * from `tiki_comments`,`tiki_forums` where ";
    $query.= " `forumId`=? and `object`=? and `objectType`=? and `parentId`=? $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_comments`,`tiki_forums` where ";
    $query_cant.= " `forumId`=? and `object`=? and `objectType`=? and `parentId`=? $mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$bindvars);
    $now = date("U");
    $ret = array();

    while ($res = $result->fetchRow()) {
	$ret[] = $res;
    }

    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
}

/*shared*/
function list_forums($offset, $maxRecords, $sort_mode, $find) {
    $bindvars = array();
    if ($find) {
	$findesc = '%'.$find.'%';
	$mid = " where (`name` like ? or `description` like ?)";
	$bindvars[] = $findesc;
	$bindvars[] = $findesc;
    } else {
	$mid = "";
    }

    $query = "select * from `tiki_forums` $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_forums` $mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$bindvars);
    $now = date("U");
    $ret = array();

    while ($res = $result->fetchRow()) {
	$forum_age = ceil(($now - $res["created"]) / (24 * 3600));
	$res["age"] = $forum_age;

	if ($forum_age) {
	    $res["posts_per_day"] = $res["comments"] / $forum_age;
	} else {
	    $res["posts_per_day"] = 0;
	}

	// Now select `users` 
	$objectId = $res["forumId"];
	$query = "select distinct `userName` from `tiki_comments` where `object`=? and `objectType`=?";
	$result2 = $this->query($query,array((string) $objectId,"forum"));
	$res["users"] = $result2->numRows();

	if ($forum_age) {
	    $res["users_per_day"] = $res["users"] / $forum_age;
	} else {
	    $res["users_per_day"] = 0;
	}

	$ret[] = $res;
    }

    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
}

/*shared*/
function remove_object($type, $id) {
    $this->uncategorize_object($type, $id);
    // Now remove comments
    $object = $type . $id;
    $query = "delete from `tiki_comments` where `object`=?  and `objectType`=?";
    $result = $this->query($query, array( $id, $type ));
    // Remove individual permissions for this object if they exist
    $query = "delete from `users_objectpermissions` where `objectId`=? and `objectType`=?";
    $result = $this->query($query,array((int)$object,$type));
    return true;
}

/*shared*/
function uncategorize_object($type, $id) {
    // Fixed query. -rlpowell
    $query = "select `catObjectId`  from `tiki_categorized_objects` where `type`=? and `objId`=?";
    $catObjectId = $this->getOne($query, array((string) $type,(string) $id));

    if ($catObjectId) {
	$query = "delete from `tiki_category_objects` where `catObjectId`=?";
	$result = $this->query($query,array((int) $catObjectId));
	$query = "delete from `tiki_categorized_objects` where `catObjectId`=?";
	$result = $this->query($query,array((int) $catObjectId));
    }
}

/*shared*/
// \todo remove hardcoded html from get_categorypath()
function get_categorypath($cats) {
    global $dbTiki;
    global $smarty;
    global $tikilib;
    global $feature_categories;
    global $categlib;

    if (!is_object($categlib)) {
	require_once ("lib/categories/categlib.php");
    }

    $catpath = '';
    foreach ($cats as $categId) {
	$catpath .= '<span class="categpath">';
	$path = '';
	$info = $categlib->get_category($categId);
	$path = '<a class="categpath" href="tiki-browse_categories.php?parentId=' . $info["categId"] . '">' . $info["name"] . '</a>';

	while ($info["parentId"] != 0) {
	    $info = $categlib->get_category($info["parentId"]);
	    $path = '<a class="categpath" href="tiki-browse_categories.php?parentId=' . $info["categId"] . '">' . $info["name"] . '</a> > ' . $path;
	}
	$catpath .= $path . '</span><br/>';
    }
    return $catpath;
}

/*shared*/
// function enhancing php in_array() function
function in_multi_array($needle, $haystack) {
    $in_multi_array = false;

    if (in_array($needle, $haystack)) {
	$in_multi_array = true;
    } else {
	while (list($tmpkey, $tmpval) = each($haystack)) {
	    if (is_array($haystack[$tmpkey])) {
		if ($this->in_multi_array($needle, $haystack[$tmpkey])) {
		    $in_multi_array = true;
		    break;
		}
	    }
	}
    }
    return $in_multi_array;
}

/*shared*/
function get_categoryobjects($catids) {
    global $dbTiki;

    global $smarty;
    global $tikilib;
    global $feature_categories;
    global $categlib;

    if (!is_object($categlib)) {
	require_once ("lib/categories/categlib.php");
    }

    // TODO: move this array to a lib
    // array for converting long type names to translatable headers (same strings as in application menu)
    $typetitles = array(
	    "article" => "Articles",
	    "blog" => "Blogs",
	    "directory" => "Directories",
	    "faq" => "FAQs",
	    "file gallery" => "File Galleries",
	    "forum" => "Forums",
	    "image gallery" => "Image Galleries",
	    "newsletter" => "Newsletters",
	    "poll" => "Polls",
	    "quiz" => "Quizzes",
	    "survey" => "Surveys",
	    "tracker" => "Trackers",
	    "wiki page" => "Wiki"
	    );

    // string given back to caller
    $out = "";

    // array with items to be displayed
    $listcat = array();
    // title of categories
    $title = '';
    $find = "";
    $offset = 0;
    $maxRecords = 500;
    $count = 0;
    $sort = 'name_asc';

    foreach ($catids as $id) {
	// get data of category
	$cat = $categlib->get_category($id);

	// store name of category
	// \todo remove hardcoded html
	if ($count != 0) {
	    $title .= "| <a href='tiki-browse_categories.php?parentId=" . $id . "'>" . $cat['name'] . "</a> ";
	} else {
	    $title .= "<a href='tiki-browse_categories.php?parentId=" . $id . "'>" . $cat['name'] . "</a> ";
	}

	// keep track of how many categories there are for split mode off
	$count++;
	$subcategs = array();
	$subcategs = $categlib->get_category_descendants($id);

	// array with objects in category
	$objectcat = array();
	$objectcat = $categlib->list_category_objects($id, $offset, $maxRecords, $sort, $find);

	foreach ($objectcat["data"] as $obj) {
	    $type = $obj["type"];
	    if (!($this->in_multi_array($obj['name'], $listcat))) {
		if (isset($typetitles["$type"])) {
		    $listcat["{$typetitles["$type"]}"][] = $obj;
		} elseif (isset($type)) {
		    $listcat["$type"][] = $obj;
		}
	    }
	}

	// split mode: appending onto $out each time
	$smarty->assign("title", $title);
	$smarty->assign("listcat", $listcat);
	$out .= $smarty->fetch("tiki-simple_plugin.tpl");
	// reset array for next loop
	$listcat = array();
	// reset title
	$title = '';
	$count = 0;
    }

    // non-split mode
    //	$smarty -> assign("title", $title);
    //	$smarty -> assign("listcat", $listcat);
    //	$out = $smarty -> fetch("tiki-simple_plugin.tpl");
    return $out;
}

/*shared*/
function list_received_pages($offset, $maxRecords, $sort_mode = 'pageName_asc', $find) {
    $bindvars = array();
    if ($find) {
	$findesc = '%'.$find.'%';
	$mid = " where (`pagename` like ? or `data` like ?)";
	$bindvbars[] = $findesc;
	$bindvbars[] = $findesc;
    } else {
	$mid = "";
    }

    $query = "select * from `tiki_received_pages` $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_received_pages` $mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = array();

    while ($res = $result->fetchRow()) {
	if ($this->page_exists($res["pageName"])) {
	    $res["exists"] = 'y';
	} else {
	    $res["exists"] = 'n';
	}

	$ret[] = $res;
    }

    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
}

// Functions for polls ////
/*shared*/
function get_poll($pollId) {
    $query = "select * from `tiki_polls` where `pollId`=?";
    $result = $this->query($query,array((int)$pollId));
    if (!$result->numRows()) return false;
    $res = $result->fetchRow();
    return $res;
}

//This should be moved to a poll module (currently in tiki-setup.php
/*shared*/
function poll_vote($pollId, $optionId) {
    $query = "update `tiki_poll_options` set `votes`=`votes`+1 where `optionId`=?";
    $result = $this->query($query,array((int)$optionId));
    $query = "update `tiki_polls` set `votes`=`votes`+1 where `pollId`=?";
    $result = $this->query($query,array((int)$pollId));
}

// end polls ////

// Functions for the menubuilder and polls////
/*Shared*/
function get_menu($menuId) {
    $query = "select * from `tiki_menus` where `menuId`=?";
    $result = $this->query($query,array((int)$menuId));
    if (!$result->numRows()) return false;
    $res = $result->fetchRow();
    return $res;
}

/*shared*/
function list_menu_options($menuId, $offset, $maxRecords, $sort_mode, $find) {
    $bindvars = array((int)$menuId);
    if ($find) {
	$mid = " where `menuId`=? and (`name` like ? or `url` like ?)";
	$bindvars[] = '%'. $find . '%';
	$bindvars[] = '%'. $find . '%';
    } else {
	$mid = " where `menuId`=? ";
    }

    $query = "select * from `tiki_menu_options` $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_menu_options` $mid";
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
// Menubuilder ends ////

// User voting system ////
// Used to vote everything (polls,comments,files,submissions,etc) ////
// Checks if a user has voted
/*shared*/
function user_has_voted($user, $id) {
    // If user is not logged in then check the session
    if (!$user) {
	$votes = $_SESSION["votes"];

	if (in_array($id, $votes)) {
	    $ret = true;
	} else {
	    $ret = false;
	}
    } else {
	$query = "select count(*) from `tiki_user_votings` where `user`=? and `id`=?";
	$result = $this->getOne($query,array($user,(string) $id));
	if ($result) {
	    $ret = true;
	} else {
	    $ret = false;
	}
    }
    return $ret;
}

// Registers a user vote
/*shared*/
function register_user_vote($user, $id) {
    // If user is not logged in then register in the session
    if (!$user) {
	$_SESSION["votes"][] = $id;
	} else {
	$query = "delete from `tiki_user_votings` where `user`=? and `id`=?";
	$result = $this->query($query,array($user,(string) $id));
	$query = "insert into `tiki_user_votings`(`user`,`id`) values(?,?)";
	$result = $this->query($query,array($user,(string) $id));
    }
}

// FILE GALLERIES ////
/*shared*/
function list_files($offset, $maxRecords, $sort_mode, $find) {
    $bindvars = array();
    if ($find) {
	$findesc = '%' . $find . '%';
	$mid = " where (`name` like ? or `description` like ?)";
	$bindvars[] = '%'. $find . '%';
	$bindvars[] = '%'. $find . '%';
    } else {
	$mid = "";
    }
    $query = "select `fileId` ,`name`,`description`,`created`,`filename`,`filesize`,`user`,`downloads` ";
    $query.= " from `tiki_files` $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_files` $mid";
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

/*shared*/
function get_file($id) {
    $query = "select `path` ,`galleryId`,`filename`,`filetype`,`data`,`filesize` from `tiki_files` where `fileId`=?";
    $result = $this->query($query,array((int) $id));
    $res = $result->fetchRow();
    return $res;
}

/*Shared*/
function get_files($offset, $maxRecords, $sort_mode, $find, $galleryId) {

    if ($find) {
	$findesc='%' . $find . '%';
	$mid = " where `galleryId`=? and (`name` like ? or `description` like ?)";
	$bindvars=array((int) $galleryId,$findesc,$findesc);
    } else {
	$mid = "where `galleryId`=?";
	$bindvars=array((int) $galleryId);
    }

    $query = "select `fileId` ,`name`,`description`,`created`,`filename`,`filesize`,`user`,`downloads` from `tiki_files` $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_files` $mid";
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

/*shared*/
function add_file_hit($id) {
    global $count_admin_pvs, $user;
    if ($count_admin_pvs == 'y' || $user != 'admin') {
	$query = "update `tiki_files` set `downloads`=`downloads`+1 where `fileId`=?";
	$result = $this->query($query,array((int) $id));
    }
    return true;
}

/*shared*/
function add_file_gallery_hit($id) {
    global $count_admin_pvs, $user;
    if ($count_admin_pvs == 'y' || $user != 'admin') {
	$query = "update `tiki_file_galleries` set `hits`=`hits`+1 where `galleryId`=?";
	$result = $this->query($query,array((int) $id));
    }
    return true;
}

/*shared*/
function get_file_gallery($id) {
    $query = "select * from `tiki_file_galleries` where `galleryId`=?";
    $result = $this->query($query,array((int) $id));
    $res = $result->fetchRow();
    return $res;
}

/*shared*/
function list_visible_file_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user, $find) {
    // If $user is admin then get ALL galleries, if not only user galleries are shown

    $old_sort_mode = '';
    $bindvars = array('y');
    $whuser = "";

    if (in_array($sort_mode, array( 'files_desc', 'files_asc'))) {
	$old_offset = $offset;
	$old_maxRecords = $maxRecords;
	$old_sort_mode = $sort_mode;
	$sort_mode = 'user_desc';
	$offset = 0;
	$maxRecords = -1;
    }

    // If the user is not admin then select `it` 's own galleries or public galleries
    if ($user != 'admin') {
	$whuser.= " and (`user`=? or `public`=?)";
	$bindvars[] = $user;
	$bindvars[] = "y";
    }

    if ($find) {
	$findesc = '%' . $find . '%';
	$whuser .= " and (`name` like ? or `description` like ?)";
	$bindvars[] = $findesc;
	$bindvars[] = $findesc;
    }

    $query = "select * from `tiki_file_galleries` where `visible`=? $whuser order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_file_galleries` where `visible`=? $whuser";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = array();

    while ($res = $result->fetchRow()) {
	$aux = array();

	$aux["name"] = $res["name"];
	$gid = $res["galleryId"];
	$aux["id"] = $gid;
	$aux["visible"] = $res["visible"];
	$aux["galleryId"] = $res["galleryId"];
	$aux["description"] = $res["description"];
	$aux["created"] = $res["created"];
	$aux["lastModif"] = $res["lastModif"];
	$aux["user"] = $res["user"];
	$aux["hits"] = $res["hits"];
	$aux["public"] = $res["public"];
	$aux["files"] = $this->getOne("select count(*) from `tiki_files` where `galleryId`=?",array((int)$gid));
	$ret[] = $aux;
    }
    if ($old_sort_mode == 'files_asc') {
	usort($ret, 'compare_files');
    }
    if ($old_sort_mode == 'files_desc') {
	usort($ret, 'r_compare_files');
    }

    if (in_array($old_sort_mode, array( 'files_desc', 'files_asc'))) {
	$ret = array_slice($ret, $old_offset, $old_maxRecords);
    }

    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
}

// \todo that function is used ?
function logui($line) {
    $fw = fopen("log.txt", "a+");
    fputs($fw, $line . "\n");
    fclose ($fw);
}

// Semaphore functions ////
function get_semaphore_user($semName) {
    return $this->getOne("select `user` from `tiki_semaphores` where `semName`=?",array($semName));
}

function semaphore_is_set($semName, $limit) {
    $now = date("U");
    $lim = $now - $limit;
    $query = "delete from `tiki_semaphores` where `semName`=? and `timestamp`<?";
    $result = $this->query($query,array($semName,(int)$lim));
    $query = "select `semName`  from `tiki_semaphores` where `semName`=?";
    $result = $this->query($query,array($semName));
    return $result->numRows();
}

function semaphore_set($semName) {
    global $user;

    if ($user == '') {
	$user = 'anonymous';
    }

    $now = date("U");
    //	$cant=$this->getOne("select count(*) from `tiki_semaphores` where `semName`='$semName'");
    $query = "delete from `tiki_semaphores` where `semName`=?";
    $this->query($query,array($semName));
    $query = "insert into `tiki_semaphores`(`semName`,`timestamp`,`user`) values(?,?,?)";
    $result = $this->query($query,array($semName,(int)$now,$user));
    return $now;
}

function semaphore_unset($semName, $lock) {
    $query = "delete from `tiki_semaphores` where `semName`=? and `timestamp`=?";
    $result = $this->query($query,array($semName,(int)$lock));
}

// Hot words methods ////
/*shared*/
function get_hotwords() {
    $query = "select * from `tiki_hotwords`";
    $result = $this->query($query, array(),-1,-1, false);
    $ret = array();
    while ($res = $result->fetchRow()) {
	$ret[$res["word"]] = $res["url"];
    }
    return $ret;
}

// BLOG METHODS ////
/*shared*/
function list_blogs($offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '') {

    if ($find) {
	$findesc = '%' . $find . '%';

	$mid = " where (`title` like ? or `description` like ?) ";
	$bindvars=array($findesc,$findesc);
    } else {
	$mid = '';
	$bindvars=array();
    }
    $query = "select * from `tiki_blogs` $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_blogs` $mid";
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

/*shared*/
function get_blog($blogId) {
    $query = "select * from `tiki_blogs` where `blogId`=?";
    $result = $this->query($query,array((int)$blogId));
    if ($result->numRows()) {
	$res = $result->fetchRow();
    } else {
	return false;
    }

    return $res;
}

/*shared*/
function list_user_blogs($user, $include_public = false) {
    $query = "select * from `tiki_blogs` where `user`=?";
    $bindvars=array($user);
    if ($include_public) {
	$query .= " or `public`=?";
	$bindvars[]='y';
    }
    $result = $this->query($query,$bindvars);
    $ret = array();

    while ($res = $result->fetchRow()) {
	$ret[] = $res;
    }
    return $ret;
}

/*shared*/
function list_posts($offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '') {

    if ($find) {
	$findesc = '%' . $find . '%';
	$mid = " where (`data` like ?) ";
	$bindvars=array($findesc);
    } else {
	$mid = '';
	$bindvars=array();
    }

    $query = "select * from `tiki_blog_posts` $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_blog_posts` $mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = array();

    while ($res = $result->fetchRow()) {
	$blogId = $res["blogId"];

	$query = "select `title`  from `tiki_blogs` where `blogId`=?";
	$cant_com = $this->getOne("select count(*) from
		`tiki_comments` where `object`=? and `objectType` = ?",
		array((string) $res["postId"],'blog'));
	$res["comments"] = $cant_com;
	$res["blogTitle"] = $this->getOne($query,array((int)$blogId));
	$res["size"] = strlen($res["data"]);
	$ret[] = $res;
    }
    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
}

// CMS functions -ARTICLES- & -SUBMISSIONS- ////
/*shared*/
function list_articles($offset = 0, $maxRecords = -1, $sort_mode = 'publishDate_desc', $find = '', $date = '', $user, $type = '', $topicId = '') {
    global $userlib;

    $mid = " where `tiki_articles`.`type` = `tiki_article_types`.`type` and `tiki_articles`.`author` = `users_users`.`login` ";
    $bindvars=array();
    if ($find) {
	$findesc = '%' . $find . '%';
	$mid .= " and (`title` like ? or `heading` like ? or `body` like ?) ";
	$bindvars=array($findesc,$findesc,$findesc);
    }
    if ($type) {
	$bindvars[]=$type;
	if ($mid) {
	    $mid .= " and `tiki_articles`.`type`=? ";
	} else {
	    $mid = " where `tiki_articles`.`type`=? ";
	}
    }

    if ($topicId) {
	$bindvars[] = (int) $topicId;
	if ($mid) {
	    $mid .= " and `topicId`=? ";
	} else {
	    $mid = " where `topicId`=? ";
	}

    }

    $query = "select `tiki_articles`.*,
	`users_users`.`avatarLibName`,
	`tiki_article_types`.`use_ratings`,
	`tiki_article_types`.`show_pre_publ`,
	`tiki_article_types`.`show_post_expire`,
	`tiki_article_types`.`heading_only`,
	`tiki_article_types`.`allow_comments`,
	`tiki_article_types`.`show_image`,
	`tiki_article_types`.`show_avatar`,
	`tiki_article_types`.`show_author`,
	`tiki_article_types`.`show_pubdate`,
	`tiki_article_types`.`show_expdate`,
	`tiki_article_types`.`show_reads`,
	`tiki_article_types`.`show_size`
	from `tiki_articles`, `tiki_article_types`, `users_users` $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_articles`, `tiki_article_types`, `users_users` $mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = array();

    while ($res = $result->fetchRow()) {
	$res["entrating"] = floor($res["rating"]);

	$add = 1;

	if ($userlib->object_has_one_permission($res["topicId"], 'topic')) {
	    if (!$userlib->object_has_permission($user, $res["topicId"], 'topic', 'tiki_p_topic_read')) {
		$add = 0;
	    }
	}
	if (empty($res["body"])) {
	    $res["isEmpty"] = 'y';
	} else {
	    $res["isEmpty"] = 'n';
	}
	if (strlen($res["image_data"]) > 0) {
	    $res["hasImage"] = 'y';
	} else {
	    $res["hasImage"] = 'n';
	}
	$res['count_comments'] = 0;

	// Determine if the article would be displayed in the view page
	$res["disp_article"] = 'y';
	$now = date("U");
	//if ($date) {
	   if (($res["show_pre_publ"] != 'y') and ($now < $res["publishDate"])) {
	       $res["disp_article"] = 'n';
	   }
	   if (($res["show_post_expire"] != 'y') and ($now > $res["expireDate"])) {
	       $res["disp_article"] = 'n';
	   }
	//}

	if ($add) {
	    $ret[] = $res;
	}
    }

    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
}

/*shared*/
function list_submissions($offset = 0, $maxRecords = -1, $sort_mode = 'publishDate_desc', $find = '', $date = '') {

    if ($find) {
	$findesc = $this->qstr('%' . $find . '%');
	$mid = " where (`title` like ? or `heading` like ? or `body` like ?) ";
	$bindvars = array($findesc,$findesc,$findesc);
    } else {
	$mid = '';
	$bindvars = array();
    }

    if ($date) {
	if ($mid) {
	    $mid .= " and `publishDate` <= ? ";
	} else {
	    $mid = " where `publishDate` <= ? ";
	}
	$bindvars[] = $date;
    }

    $query = "select * from `tiki_submissions` $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_submissions` $mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = array();

    while ($res = $result->fetchRow()) {
	$res["entrating"] = floor($res["rating"]);

	if (empty($res["body"])) {
	    $res["isEmpty"] = 'y';
	} else {
	    $res["isEmpty"] = 'n';
	}

	if (strlen($res["image_data"]) > 0) {
	    $res["hasImage"] = 'y';
	} else {
	    $res["hasImage"] = 'n';
	}

	$ret[] = $res;
    }

    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
}

function get_article($articleId) {
    $query = "select * from `tiki_articles` where `articleId`=?";
    $result = $this->query($query,array((int)$articleId));
    if ($result->numRows()) {
	$res = $result->fetchRow();
	$res["entrating"] = floor($res["rating"]);
    } else {
	return false;
    }
    return $res;
}

function get_submission($subId) {
    $query = "select * from `tiki_submissions` where `subId`=?";
    $result = $this->query($query,array((int) $subId));
    if ($result->numRows()) {
	$res = $result->fetchRow();
	$res["entrating"] = floor($res["rating"]);
    } else {
	return false;
    }
    return $res;
}

function replace_article($title, $authorName, $topicId, $useImage, $imgname, $imgsize, $imgtype, $imgdata, $heading, $body, $publishDate, $expireDate, $user, $articleId, $image_x, $image_y, $type, $rating = 0, $isfloat = 'n') {

    $hash = md5($title . $heading . $body);
    $now = date("U");
    // Fixed query. -rlpowell
    $query = "select `name`  from `tiki_topics` where `topicId` = ?";
    $topicName = $this->getOne($query, array($topicId) );
    $size = strlen($body);

    // Fixed query. -rlpowell
    if ($articleId) {
	// Update the article
	$query = "update `tiki_articles` set `title` = ?, `authorName` = ?, `topicId` = ?, `topicName` = ?, `size` = ?, `useImage` = ?, `image_name` = ?, ";
	$query.= " `image_type` = ?, `image_size` = ?, `image_data` = ?, `isfloat` = ?, `image_x` = ?, `image_y` = ?, `heading` = ?, `body` = ?, ";
	$query.= " `publishDate` = ?, `expireDate` = ?, `created` = ?, `author` = ?, `type` = ?, `rating` = ?  where `articleId` = ?";

	$result = $this->query($query, array(
		    $title, $authorName, (int) $topicId, $topicName, (int) $size, $useImage, $imgname, $imgtype, (int) $imgsize, $imgdata, $isfloat,
		    (int) $image_x, (int) $image_y, $heading, $body, (int) $publishDate, (int) $expireDate, (int) $now, $user, $type, (float) $rating, (int) $articleId ) );
    } else {
	// Fixed query. -rlpowell
	// Insert the article
	$query = "insert into `tiki_articles` (title, authorName, topicId, useImage, image_name, image_size, image_type, image_data, ";
	$query.= " publishDate, expireDate, created, heading, body, hash, author, reads, votes, points, size, topicName, image_x, image_y, type, rating, isfloat) ";
	$query.= " values( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

	$result = $this->query($query, array(
		    $title, $authorName, (int) $topicId, $useImage, $imgname, (int) $imgsize, $imgtype, $imgdata, (int) $publishDate, (int) $expireDate, (int) $now, $heading,
		    $body, $hash, $user, 0, 0, 0, (int) $size, $topicName, (int) $image_x, (int) $image_y, $type, (float) $rating, $isfloat));

	// Fixed query. -rlpowell
	$query2 = "select max(`articleId`) from `tiki_articles` where `created` = ? and `title`=? and `hash`=?";
	$articleId = $this->getOne($query2, array( (int) $now, $title, $hash ) );
    }

    return $articleId;
}

/*shared*/
function get_topic_image($topicId) {
    // Fixed query. -rlpowell
    $query = "select `image_name` ,`image_size`,`image_type`, `image_data` from `tiki_topics` where `topicId`=?";
    $result = $this->query($query, array((int) $topicId));
    $res = $result->fetchRow();
    return $res;
}

/*shared*/
function get_featured_links($max = 10) {
    $query = "select * from `tiki_featured_links` where `position` > ? order by ".$this->convert_sortmode("position_asc");
    $result = $this->query($query, array(0), (int)$max, 0 );
    $ret = array();
    while ($res = $result->fetchRow()) {
	$ret[] = $res;
    }
    return $ret;
}

function update_session($sessionId) {
    global $user;
    if ($user === false) $user = '';
    $now = date("U");
    $oldy = $now - (5 * 60);
    if ($user) {
    	$this->query('delete from `tiki_sessions` where `user`=?', array($user), -1, -1, false);
    }
    $this->query('delete from `tiki_sessions` where `sessionId`=?', array($sessionId), -1, -1, false);
    $query = "insert into `tiki_sessions`(`sessionId`,`timestamp`,`user`) values(?,?,?)";
    $result = $this->query($query, array( $sessionId, (int)$now, $user));
    $query = "delete from `tiki_sessions` where `timestamp`<?";
    $result = $this->query($query, array($oldy));
    return true;
}

function count_sessions() {
    $query = "select count(*) from `tiki_sessions`";
    $cant = $this->getOne($query,array());
    return $cant;
}

/*shared*/
function get_assigned_modules($position, $displayed="n") {
    $filter = '';
    if ($displayed != 'n') {
	$filter = " and (`type` is null or `type` !='h')";
    }
    $query = "select `params`,`name`,`title`,`position`,`ord`,`cache_time`,`rows`,`groups` from `tiki_modules` ";
    $query.= " where `position`= ? $filter order by ".$this->convert_sortmode("ord_asc");

    $result = $this->query($query, array($position));
    $ret = array();

    while ($res = $result->fetchRow()) {
	if ($res["groups"] && strlen($res["groups"]) > 1) {
	    $grps = unserialize($res["groups"]);

	    $res["module_groups"] = '';
	    foreach ($grps as $grp) {
		$res["module_groups"] .= " $grp ";
	    }
	} else {
	    $res["module_groups"] = '&nbsp;';
	}
	$ret[] = $res;
    }
    return $ret;
}

/*shared*/
function is_user_module($name) {
    $query = "select `name`  from `tiki_user_modules` where `name`=?";
    $result = $this->query($query,array($name));
    return $result->numRows();
}

/*shared*/
function get_user_module($name) {
    $query = "select * from `tiki_user_modules` where `name`=?";
    $result = $this->query($query,array($name));
    $res = $result->fetchRow();
    return $res;
}

function cache_links($links) {
    $cachepages = $this->get_preference("cachepages", 'y');
    if ($cachepages != 'y') return false;
    foreach ($links as $link) {
	if (!$this->is_cached($link)) {
	    $this->cache_url($link);
	}
    }
}

function get_links($data) {
    $links = array();

    // Match things like [...], but ignore things like [[foo].
    // -Robin
    if (preg_match_all("/(?<!\[)\[([^\[\|\]]+)(\||\])/", $data, $r1)) {
	$res = $r1[1];
	$links = array_unique($res);
    }

    return $links;
}

function get_links_nocache($data) {
    $links = array();

    if (preg_match_all("/\[([^\]]+)/", $data, $r1)) {
	$res = array();

	foreach ($r1[1] as $alink) {
	    $parts = explode('|', $alink);

	    if (isset($parts[1]) && $parts[1] == 'nocache') {
		$res[] = $parts[0];
	    } else {
		if (isset($parts[2]) && $parts[2] == 'nocache') {
		    $res[] = $parts[0];
		}
	    }
	    // avoid caching URLs with common binary file extensions
	    $extension = substr($parts[0], -4);
	    $binary = array(
		    '.arj',
		    '.asf',
		    '.avi',
		    '.bz2',
		    '.dat',
		    '.doc',
		    '.exe',
		    '.hqx',
		    '.mov',
		    '.mp3',
		    '.mpg',
		    '.ogg',
		    '.pdf',
		    '.ram',
		    '.rar',
		    '.rpm',
		    '.rtf',
		    '.sea',
		    '.sit',
		    '.tar',
		    '.tgz',
		    '.wav',
		    '.wmv',
		    '.xls',
		    '.zip',
		    'ar.Z', // .tar.Z
		    'r.gz'  // .tar.gz
			);
		    if (in_array($extension, $binary)) {
			$res[] = $parts[0];
		    }

	}

	$links = array_unique($res);
    }

    return $links;
}

function is_cacheable($url) {
    // simple implementation: future versions should analyse
    // if this is a link to the local machine
    if (strstr($url, 'tiki-')) {
	return false;
    }

    if (strstr($url, 'messu-')) {
	return false;
    }

    return true;
}

function is_cached($url) {
    $query = "select `cacheId`  from `tiki_link_cache` where `url`=?";
    $result = $this->query($query, array($url) );
    $cant = $result->numRows();
    return $cant;
}

function list_cache($offset, $maxRecords, $sort_mode, $find) {

    if ($find) {
	$findesc = '%' . $find . '%';

	$mid = " where (`url` like ?) ";
	$bindvars=array($findesc);
    } else {
	$mid = "";
	$bindvars=array();
    }

	$query = "select `cacheId` ,`url`,`refresh` from `tiki_link_cache` $mid order by ".$this->convert_sortmode($sort_mode);
	$query_cant = "select count(*) from `tiki_link_cache` $mid";
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

function refresh_cache($cacheId) {
    $query = "select `url`  from `tiki_link_cache`
	where `cacheId`=?";

    $url = $this->getOne($query, array( $cacheId ) );
    $data = $this->httprequest($url);
    $refresh = date("U");
    $query = "update `tiki_link_cache`
	set `data`=?, `refresh`=?
	where `cacheId`=? ";
    $result = $this->query($query, array( $data, $refresh, $cacheId) );
    return true;
}

function remove_cache($cacheId) {
    $query = "delete from `tiki_link_cache` where `cacheId`=?";

    $result = $this->query($query, array( $cacheId ) );
    return true;
}

function get_cache($cacheId) {
    $query = "select * from `tiki_link_cache`
	where `cacheId`=?";

    $result = $this->query($query, array( $cacheId ) );
    $res = $result->fetchRow();
    return $res;
}

function get_cache_id($url) {
	if (!$this->is_cached($url))
	    return false;

	$query = "select `cacheId`  from `tiki_link_cache`
	    where `url`=?";
	$id = $this->getOne($query, array( $url ) );
	return $id;
}

function vote_page($page, $points) {
    $query = "update `pages`
	set `points`=`points`+$points, `votes`=`votes`+1
	where `pageName`=?";
    $result = $this->query($query, array( $page ));
}

function get_votes($page) {
    $query = "select `points` ,`votes`
	from `pages` where `pageName`=?";
    $result = $this->query($query, array( $page ));
    $res = $result->fetchRow();
    return $res;
}

// This funcion return the $limit most accessed pages
// it returns pageName and hits for each page
function get_top_pages($limit) {
    $query = "select `pageName` , `hits`
	from `tiki_pages`
	order by `hits` desc";

    $result = $this->query($query, array(),$limit);
    $ret = array();

    while ($res = $result->fetchRow()) {
	$aux["pageName"] = $res["pageName"];

	$aux["hits"] = $res["hits"];
	$ret[] = $aux;
    }

    return $ret;
}

// Returns the name of "n" random pages
function get_random_pages($n) {
    $query = "select count(*) from `tiki_pages`";

	$cant = $this->getOne($query,array());

    // Adjust the limit if there are not enough pages
    if ($cant < $n)
	$n = $cant;

    // Now that we know the number of pages to pick select `n`  random positions from `0` to cant
    $positions = array();

    for ($i = 0; $i < $n; $i++) {
	$pick = rand(0, $cant - 1);

	if (!in_array($pick, $positions))
	    $positions[] = $pick;
    }

    // Now that we have the positions we just build the data
    $ret = array();

    for ($i = 0; $i < count($positions); $i++) {
	$index = $positions[$i];

	$query = "select `pageName`  from `tiki_pages`";
	$name = $this->getOne($query,array(),1,$index);
	$ret[] = $name;
    }

    return $ret;
}
/**
 * \brief Cache given url
 * If \c $data present (passed) it is just associated \c $url and \c $data.
 * Else it will request data for given URL and store it in DB.
 * Actualy (currently) data may be proviced by TIkiIntegrator only.
 */
function cache_url($url, $data = '') {
    // Avoid caching internal references... (only if $data not present)
    // (cdx) And avoid other protocols than http...
    // 03-Nov-2003, by zaufi
    // preg_match("_^(mailto:|ftp:|gopher:|file:|smb:|news:|telnet:|javascript:|nntp:|nfs:)_",$url)
    // was removed (replaced to explicit http[s]:// detection) bcouse
    // I now (and actualy use in my production Tiki) another bunch of protocols
    // available in my konqueror... (like ldap://, ldaps://, nfs://, fish://...)
    // ... seems like it is better to enum that allowed explicitly than all
    // noncacheable protocols.
    if (((strstr($url, 'tiki-') || strstr($url, 'messu-')) && $data == '')
     || (substr($url, 0, 7) != 'http://' && substr($url, 0, 8) != 'https://'))
        return false;
    // Request data for URL if nothing given in parameters
    // (reuse $data var)
    if ($data == '') $data = $this->httprequest($url);

    // If stuff inside [] is *really* malformatted, $data
    // will be empty.  -rlpowell
    if ($data)
    {
	    $refresh = date("U");
    	$query = "insert into `tiki_link_cache`(`url`,`data`,`refresh`) values(?,?,?)";
	    $result = $this->queryError($query, $error, array($url,$data,$refresh) );
        return !isset($error);
    }
    else return false;
}

// Removes all the versions of a page and the page itself
/*shared*/
function remove_all_versions($page, $comment = '') {
	$this->invalidate_cache($page);
	$query = "delete from `tiki_pages` where `pageName` = ?";
	$result = $this->query($query, array( $page ) );
	$query = "delete from `tiki_history` where `pageName` = ?";
	$result = $this->query($query, array( $page ) );
	$query = "delete from `tiki_links` where `fromPage` = ?";
	$result = $this->query($query, array( $page ) );
	$action = "Removed";
	$t = date("U");
	$query = "insert into
	`tiki_actionlog`(`action`,`pageName`,`lastModif`,`user`,`ip`,`comment`)
	values(?,?,?,?,?,?)";
	$result = $this->query($query, array(
		$action,$page,(int) $t,'admin',$_SERVER["REMOTE_ADDR"],$comment
		) );
    $this->remove_object('wiki page', $page);
    $this->remove_from_structure($page);
    return true;
}

/*shared*/
function remove_from_structure($page) {
    // Now recursively remove
    $query = "select `page` from `tiki_structures`
	where `parent`=?";
    $result = $this->query($query, array( $page ) );

    while ($res = $result->fetchRow()) {
	$this->remove_from_structure($res["page"]);
    }

    $query = "delete from `tiki_structures` where `page`=?";
    $result = $this->query($query, array( $page ) );
    return true;
}

function remove_user($user) {
    $query = "delete from `users_users` where `login` = ?";

    $result = $this->query($query, array($user) );
    return true;
}

function user_exists($user) {
    $query = "select count(*) from `users_users` where `login`=?";

    $result = $this->getOne($query, array($user));

    if ($result)
	return true;

    return false;
}

function add_user($user, $pass, $email) {
    global $wikiHomePage;

    if (user_exists($user))
	return false;

    $query = "insert into `users_users`(`login`,`password`,`email`)
	values(?, ?, ?)";
    $result = $this->query($query, array($user,$pass,$email) );
    $action = "user $user added";
    $t = date("U");
    $query = "insert into `tiki_actionlog`(`action`,`pageName`,`lastModif`,`user`,`ip`,`comment`) values(?,?,?,?,?,?)";
    $result = $this->query($query,array($action,$wikiHomePage,(int) $t,'admin',$_SERVER["REMOTE_ADDR"],''));
    return true;
}

function get_user_password($user) {
    return $this->getOne("select `password`  from `users_users` where " . $this->convert_binary(). " `login`=?", array($user));
}

function get_user_email($user) {
    return $this->getOne("select `email` from `users_users` where " . $this->convert_binary(). " `login`=?", array($user));
}

function get_user_info($user) {
    $query = "select `login` , `email`, `lastLogin`
	from `tiki_users` where `user`=?";

    $result = $this->query($query, array( $user ));
    $res = $result->fetchRow();
    $aux = array();
    $aux["user"] = $res["user"];
    $user = $aux["user"];
    $aux["email"] = $res["email"];
    $aux["lastLogin"] = $res["lastLogin"];
    // Obtain lastChanged
    $query2 = "select count(*) from `tiki_pages` where `user`='$user'";
    $result2 = $this->query($query2);
    $res2 = $result2->fetchRow();
    $aux["versions"] = $res2[0];
    // Obtain versions
    $query3 = "select count(*) from `tiki_history` where `user`='$user'";
    $result3 = $this->query($query3);
    $res3 = $result3->fetchRow();
    $aux["lastChanged"] = $res3[0];
    $ret[] = $aux;
    return $aux;
}

/*shared*/
function list_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user, $find) {
    // If $user is admin then get ALL galleries, if not only user galleries are shown
    global $tiki_p_admin_galleries;

    $old_sort_mode = '';

    if (in_array($sort_mode, array(
		    'images desc',
		    'images asc'
		    ))) {
	$old_offset = $offset;

	$old_maxRecords = $maxRecords;
	$old_sort_mode = $sort_mode;
	$sort_mode = 'user desc';
	$offset = 0;
	$maxRecords = -1;
    }

    // If the user is not admin then select `it` 's own galleries or public galleries
    if (($tiki_p_admin_galleries == 'y') or ($user == 'admin')) {
	$whuser = "";
	$bindvars=array();
    } else {
	$whuser = "where `user`=? or public=?";
	$bindvars=array($user,'y');
    }

    if ($find) {
	$findesc = '%' . $find . '%';

	if (empty($whuser)) {
	    $whuser = "where `name` like ? or `description` like ?";
	    $bindvars=array($findesc,$findesc);
	} else {
	    $whuser .= " and `name` like ? or `description` like ?";
	    $bindvars[]=$findesc;
	    $bindvars[]=$findesc;
	}
    }

    // If sort mode is versions then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    // If sort mode is links then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    // If sort mode is backlinks then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    $query = "select * from `tiki_galleries` $whuser order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_galleries` $whuser";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = array();

    while ($res = $result->fetchRow()) {
	$aux = array();

	$aux["name"] = $res["name"];
	$gid = $res["galleryId"];
	$aux["visible"] = $res["visible"];
	$aux["id"] = $gid;
	$aux["galleryId"] = $res["galleryId"];
	$aux["description"] = $res["description"];
	$aux["created"] = $res["created"];
	$aux["lastModif"] = $res["lastModif"];
	$aux["user"] = $res["user"];
	$aux["hits"] = $res["hits"];
	$aux["public"] = $res["public"];
	$aux["theme"] = $res["theme"];
	$aux["images"] = $this->getOne("select count(*) from `tiki_images` where `galleryId`=?",array($gid));
	$ret[] = $aux;
    }

    if ($old_sort_mode == 'images asc') {
	usort($ret, 'compare_images');
    }

    if ($old_sort_mode == 'images desc') {
	usort($ret, 'r_compare_images');
    }

    if (in_array($old_sort_mode, array(
		    'images desc',
		    'images asc'
		    ))) {
	$ret = array_slice($ret, $old_offset, $old_maxRecords);
    }

    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
}

/*shared*/
function list_visible_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user, $find) {
    global $tiki_p_admin_galleries;
    // If $user is admin then get ALL galleries, if not only user galleries are shown

    $old_sort_mode = '';

    if (in_array($sort_mode, array(
		    'images desc',
		    'images asc'
		    ))) {
	$old_offset = $offset;

	$old_maxRecords = $maxRecords;
	$old_sort_mode = $sort_mode;
	$sort_mode = 'user desc';
	$offset = 0;
	$maxRecords = -1;
    }

    // If the user is not admin then select `it` 's own galleries or public galleries
    if (($user != 'admin') and ($tiki_p_admin_galleries != 'y')) {
	$whuser = "and `user`=? or `public`=?";
	$bindvars=array('y',$user,'y');
    } else {
	$whuser = "";
	$bindvars=array('y');
    }

    if ($find) {
	$findesc = '%' . $find . '%';

	if (empty($whuser)) {
	    $whuser = " and (`name` like ? or `description` like ?)";
	    $bindvars=array('y',$findesc,$findesc);
	} else {
	    $whuser .= " and (`name` like ? or `description` like ?)";
	    $bindvars[]=$findesc;
	    $bindvars[]=$findesc;
	}
    }

    // If sort mode is versions then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    // If sort mode is links then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    // If sort mode is backlinks then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    $query = "select * from `tiki_galleries` where `visible`=? $whuser order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_galleries` where `visible`=? $whuser";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = array();

    while ($res = $result->fetchRow()) {
	$aux = array();

	$aux["name"] = $res["name"];
	$gid = $res["galleryId"];
	$aux["visible"] = $res["visible"];
	$aux["id"] = $gid;
	$aux["galleryId"] = $res["galleryId"];
	$aux["description"] = $res["description"];
	$aux["created"] = $res["created"];
	$aux["lastModif"] = $res["lastModif"];
	$aux["user"] = $res["user"];
	$aux["hits"] = $res["hits"];
	$aux["public"] = $res["public"];
	$aux["theme"] = $res["theme"];
	$aux["images"] = $this->getOne("select count(*) from `tiki_images` where `galleryId`=?",array($gid));
	$ret[] = $aux;
    }

    if ($old_sort_mode == 'images asc') {
	usort($ret, 'compare_images');
    }

    if ($old_sort_mode == 'images desc') {
	usort($ret, 'r_compare_images');
    }

    if (in_array($old_sort_mode, array(
		    'images desc',
		    'images asc'
		    ))) {
	$ret = array_slice($ret, $old_offset, $old_maxRecords);
    }

    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
}

function list_pages($offset = 0, $maxRecords = -1, $sort_mode = 'pageName_desc', $find = '') {

    if ($sort_mode == 'size_desc') {
	$sort_mode = 'page_size_desc';
    }

    if ($sort_mode == 'size_asc') {
	$sort_mode = 'page_size_asc';
    }

    $old_sort_mode = '';

    if (in_array($sort_mode, array(
		    'versions_desc',
		    'versions_asc',
		    'links_asc',
		    'links_desc',
		    'backlinks_asc',
		    'backlinks_desc'
		    ))) {
	$old_offset = $offset;

	$old_maxRecords = $maxRecords;
	$old_sort_mode = $sort_mode;
	$sort_mode = 'user_desc';
	$offset = 0;
	$maxRecords = -1;
    }

    if (is_array($find)) { // you can use an array of pages
	$mid = " where `pageName` IN (".implode(',',array_fill(0,count($find),'?')).")";
	$bindvars = $find;
    } elseif (is_string($find)) { // or a string
	$mid = " where `pageName` like ? ";
	$bindvars = array('%' . $find . '%');
    } else {
	$mid = "";
	$bindvars = array();
    }

    // If sort mode is versions then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    // If sort mode is links then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    // If sort mode is backlinks then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    $query = "select `creator` ,`pageName`, `hits`, `page_size` as `len`, `lastModif`, `user`, `ip`, `comment`, `version`, `flag` ";
    $query.= " from `tiki_pages` $mid order by ".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `tiki_pages` $mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = array();

    while ($res = $result->fetchRow()) {
	$aux = array();

	$aux["pageName"] = $res["pageName"];
	$page = $aux["pageName"];
	$aux["hits"] = $res["hits"];
	$aux["lastModif"] = $res["lastModif"];
	$aux["user"] = $res["user"];
	$aux["ip"] = $res["ip"];
	$aux["len"] = $res["len"];
	$aux["comment"] = $res["comment"];
	$aux["creator"] = $res["creator"];
	$aux["version"] = $res["version"];
	$aux["flag"] = $res["flag"] == 'L' ? tra('locked') : tra('unlocked');
	$aux["versions"] = $this->getOne("select count(*) from `tiki_history` where `pageName`=?",array($page));
	$aux["links"] = $this->getOne("select count(*) from `tiki_links` where `fromPage`=?",array($page));
	$aux["backlinks"] = $this->getOne("select count(*) from `tiki_links` where `toPage`=?",array($page));
	$ret[] = $aux;
    }

    // If sortmode is versions, links or backlinks sort using the ad-hoc function and reduce using old_offse and old_maxRecords
    if ($old_sort_mode == 'versions_asc') {
	usort($ret, 'compare_versions');
    }

    if ($old_sort_mode == 'versions_desc') {
	usort($ret, 'r_compare_versions');
    }

    if ($old_sort_mode == 'links_desc') {
	usort($ret, 'compare_links');
    }

    if ($old_sort_mode == 'links_asc') {
	usort($ret, 'r_compare_links');
    }

    if ($old_sort_mode == 'backlinks_desc') {
	usort($ret, 'compare_backlinks');
    }

    if ($old_sort_mode == 'backlinks_asc') {
	usort($ret, 'r_compare_backlinks');
    }

    if (in_array($old_sort_mode, array(
		    'versions_desc',
		    'versions_asc',
		    'links_asc',
		    'links_desc',
		    'backlinks_asc',
		    'backlinks_desc'
		    ))) {
	$ret = array_slice($ret, $old_offset, $old_maxRecords);
    }

    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
}

function get_users($offset = 0, $maxRecords = -1, $sort_mode = 'user_desc') {
    $sort_mode = str_replace("_", " ", $sort_mode);

    $old_sort_mode = '';

    if (in_array($sort_mode, array(
		    'versions desc',
		    'versions asc',
		    'changed asc',
		    'changed desc'
		    ))) {
	$old_offset = $offset;

	$old_maxRecords = $maxRecords;
	$old_sort_mode = $sort_mode;
	$sort_mode = 'user desc';
	$offset = 0;
	$maxRecords = -1;
    }

	// Return an array of users indicating name, email, last changed pages, versions, lastLogin
	$query = "select `user` , `email`, `lastLogin` from `tiki_users` order by ".$this->convert_sortmode($sort_mode);
	$cant = $this->getOne("select count(*) from `tiki_users`",array());
	$result = $this->query($query,array(),$maxRecords,$offset);
	$ret = array();

    while ($res = $result->fetchRow()) {
	$aux = array();

	$aux["user"] = $res["user"];
	$user = $aux["user"];
	$aux["email"] = $res["email"];
	$aux["lastLogin"] = $res["lastLogin"];
	// Obtain lastChanged
	$aux["versions"] = $this->getOne("select count(*) from `tiki_pages` where `user`=?",array($user));
	// Obtain versions
	$aux["lastChanged"] = $this->getOne("select count(*) from `tiki_history` where `user`=?",array($user));
	$ret[] = $aux;
    }

    if ($old_sort_mode == 'changed asc') {
	usort($ret, 'compare_changed');
    }

    if ($old_sort_mode == 'changed desc') {
	usort($ret, 'r_compare_changed');
    }

    if ($old_sort_mode == 'versions asc') {
	usort($ret, 'compare_versions');
    }

    if ($old_sort_mode == 'versions desc') {
	usort($ret, 'r_compare_versions');
    }

    if (in_array($old_sort_mode, array(
		    'versions desc',
		    'versions asc',
		    'changed asc',
		    'changed desc'
		    ))) {
	$ret = array_slice($ret, $old_offset, $old_maxRecords);
    }

    $retval = array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
}

function get_all_preferences() {
    static $preferences;

    if (!$preferences) {
	$query = "select `name` ,`value` from `tiki_preferences`";

	$result = $this->query($query,array());
	$preferences = array();

	while ($res = $result->fetchRow()) {
	    $preferences[$res["name"]] = $res["value"];
	}
    }

    return $preferences;
}

function get_preference($name, $default = '') {
    static $preferences;

    if (!$preferences) {
	$preferences = $this->get_all_preferences();
    }

    if (!isset($preferences[$name])) {
	$preferences[$name] = $default;
    }

    return $preferences[$name];
}

function set_preference($name, $value) {
    global $preferences, $tikidomain;

    @unlink ("templates_c/" . $tikidomain . "preferences.php");

    //refresh cache
    if (isset($preferences[$name])) {
	unset ($preferences[$name]);

	$preferences[$name] = $value;
    }

    $query = "delete from `tiki_preferences` where `name`=?";
    $result = $this->query($query,array($name),-1,-1,false);
    $query = "insert into `tiki_preferences`(`name`,`value`) values(?,?)";
    $result = $this->query($query,array($name,$value));
    return true;
}

function get_user_preference($user, $name, $default = '') {
    global $user_preferences;

    if (!isset($user_preferences[$user][$name])) {
	$query = "select `value` from `tiki_user_preferences` where `prefName`=? and `user`=?";

	$result = $this->query($query, array( "$name", "$user"));

	if ($result->numRows()) {
	    $res = $result->fetchRow();

	    $user_preferences[$user][$name] = $res["value"];
	} else {
	    $user_preferences[$user][$name] = $default;
	}
    }

    return $user_preferences[$user][$name];
}

function set_user_preference($user, $name, $value) {
    global $user_preferences;

	$user_preferences[$user][$name] = $value;
	$query = "delete from `tiki_user_preferences`where `user`=? and `prefName`=?";
	$bindvars=array($user,$name);
	$result = $this->query($query, $bindvars, -1,-1,false);
	$query = "insert into `tiki_user_preferences`(`user`,`prefName`,`value`) values(?, ?, ?)";
	$bindvars[]=$value;
	$result = $this->query($query, $bindvars);
	return true;
}

function validate_user($user, $pass) {
    $query = "select count(*) from `tiki_users`
	where `user`=? and `password`=?";

    $result = $this->getOne($query, array( $user, $pass ) );

    if ($result) {
	$t = date("U");

	$query = "update `tiki_users` set `lastLogin`=?
	    where `user`=?";
	$result = $this->query($query, array( $t, $user));
	return true;
    }

    return false;
}

// This implements all the functions needed to use Tiki
/*shared*/
function page_exists($pageName, $casesensitive=false) {
    $query = "select `pageName` from `tiki_pages` where `pageName` = ?";
    $result = $this->query($query, array($pageName));
		
		// if casesensitive, check the name of the returned page:
		if ( ($casesensitive) && ($result->numRows()) ) {
	    $res = $result->fetchRow();
	    if ($res["pageName"] <> $pageName) return 0;
		}
		
		return $result->numRows();
}

function page_exists_desc($pageName) {
    $query = "select `description`  from `tiki_pages`
	where `pageName` = ?";
    $result = $this->query($query, array( $pageName ));

    if (!$result->numRows())
	return false;

    $res = $result->fetchRow();

    if (!$res["description"])
	$res["description"] = tra('no description');

    return $res["description"];
}

function page_exists_modtime($pageName) {
    $query = "select `lastModif`  from `tiki_pages`
	where `pageName` = ?";
    $result = $this->query($query, array( $pageName ));

    if (!$result->numRows())
	return false;

    $res = $result->fetchRow();

    if (!$res["lastModif"])
	$res["lastModif"] = 0;

    return $res["lastModif"];
}

function add_hit($pageName) {
    $query = "update `tiki_pages` set `hits`=`hits`+1 where `pageName` = ?";
    $result = $this->query($query, array($pageName));
    return true;
}

function create_page($name, $hits, $data, $lastModif, $comment, $user = 'system', $ip = '0.0.0.0', $description = '') {
    global $smarty;
    global $dbTiki;
    global $notificationlib;
    global $sender_email;
    include_once ('lib/notifications/notificationlib.php');
    include_once ("lib/commentslib.php");

    $commentslib = new Comments($dbTiki);

    // Collect pages before modifying data
    $pages = $this->get_pages($data);

    // This *really* shouldn't be necessary now that the
    // query itself has been fixed up, and it causes much
    // badness to the phpwiki import.  -rlpowell 
    // 	$name = addslashes($name);
    // 	$description = addslashes($description);
    // 	$data = addslashes($data);
    // 	$comment = addslashes($comment);

    if ($this->page_exists($name))
	return false;

    $query = "insert into `tiki_pages`(`pageName`,`hits`,`data`,`lastModif`,`comment`,`version`,`user`,`ip`,`description`,`creator`,`page_size`) ";
    $query.= " values(?,?,?,?,?,?,?,?,?,?,?)";
    $result = $this->query($query, array(
		$name,
		(int)$hits,
		$data,
		(int)$lastModif,
		$comment,
		1,
		$user,
		$ip,
		$description,
		$user,
		(int)strlen($data)
		));

    $this->clear_links($name);

    // Pages are collected before adding slashes
    foreach ($pages as $a_page) {
	$this->replace_link($name, $a_page);
    }

    // Update the log
    if ($name != 'SandBox') {
	$action = "Created";

	$query = "insert into `tiki_actionlog`(`action`,`pageName`,`lastModif`,`user`,`ip`,`comment`) values(?,?,?,?,?,?)";
	$result = $this->query($query, array(
		    $action,
		    $name,
		    (int)$lastModif,
		    $user,
		    $ip,
		    $comment
		    ));
    }

    $emails = $notificationlib->get_mail_events('wiki_page_changes', '*');

    foreach ($emails as $email) {
	$smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);

	$smarty->assign('mail_page', $name);
	$smarty->assign('mail_date', date("U"));
	$smarty->assign('mail_user', $user);
	$smarty->assign('mail_comment', $comment);
	$smarty->assign('mail_last_version', 1);
	$smarty->assign('mail_data', $data);
	$foo = parse_url($_SERVER["REQUEST_URI"]);
	$machine = httpPrefix(). dirname( $foo["path"] );
	$smarty->assign('mail_machine', $machine);
	$smarty->assign('mail_pagedata', $data);
	$mail_data = $smarty->fetch('mail/wiki_change_notification.tpl');

	if( $this->get_preference('wiki_forum') )
	{
	    $forums = $commentslib->list_forums( 0, 1,
		    'name_asc',
		    $this->get_preference('wiki_forum') );

	    $forumEmail = $forums["data"][0]["outbound_from"];

	    @mail($email, $name, $mail_data,
		    "From: $forumEmail\r\nContent-type: text/plain;charset=utf-8\r\n"
		 );
	} else {
	    @mail($email, tra('Wiki page'). ' ' . $name . '
		    ' . tra('changed'), $mail_data,
		    "From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n"
		 );
	}
    }

    return true;
}

function get_user_pages($user, $max, $who='user') {
	$query = "select `pageName` from `tiki_pages` where `$who`=?";

	$result = $this->query($query,array($user),$max);
	$ret = array();

    while ($res = $result->fetchRow()) {
	$ret[] = $res;
    }

    return $ret;
}

function get_user_galleries($user, $max) {
	$query = "select `name` ,`galleryId`  from `tiki_galleries` where `user`=?";

	$result = $this->query($query,array($user),$max);
	$ret = array();

    while ($res = $result->fetchRow()) {
	$ret[] = $res;
    }

    return $ret;
}

function get_page_info($pageName) {
    $query = "select * from `tiki_pages` where `pageName`=?";

    $result = $this->query($query, array($pageName));

    if (!$result->numRows())
	return false;
    else
	return $result->fetchRow();
}

function how_many_at_start($str, $car) {
    $cant = 0;

    $i = 0;

    while (($i < strlen($str)) && (isset($str{$i})) && ($str{$i}== $car)) {
	$i++;

	$cant++;
    }

    return $cant;
}

function parse_data_raw($data) {
    $data = $this->parse_data($data);

    $data = str_replace("tiki-index", "tiki-index_raw", $data);
    return $data;
}

function add_pre_handler($name) {
    if (!in_array($name, $this->pre_handlers)) {
	$this->pre_handlers[] = $name;
    }
}

function add_pos_handler($name) {
    if (!in_array($name, $this->pos_handlers)) {
	$this->pos_handlers[] = $name;
    }
}

// This function handles wiki codes for those special HTML characters
// that textarea won't leave alone.
function parse_htmlchar(&$data) {
    // cleaning some user input
    $data = preg_replace("/&(?!([a-z]{1,7};))/", "&amp;", $data);

    // oft-used characters (case insensitive)
    $data = preg_replace("/~bs~/i", "&#92;", $data);
    $data = preg_replace("/~hs~/i", "&nbsp;", $data);
    $data = preg_replace("/~amp~/i", "&amp;", $data);
    $data = preg_replace("/~ldq~/i", "&ldquo;", $data);
    $data = preg_replace("/~rdq~/i", "&rdquo;", $data);
    $data = preg_replace("/~lsq~/i", "&lsquo;", $data);
    $data = preg_replace("/~rsq~/i", "&rsquo;", $data);
    $data = preg_replace("/~c~/i", "&copy;", $data);
    $data = preg_replace("/~--~/", "&mdash;", $data);
    $data = preg_replace("/ -- /", " &mdash; ", $data);
    $data = preg_replace("/~lt~/i", "&lt;", $data);
    $data = preg_replace("/~gt~/i", "&gt;", $data);

    // HTML numeric character entities
    $data = preg_replace("/~([0-9]+)~/", "&#$1;", $data);
}

// AWC ADDITION
// This function replaces pre- and no-parsed sections with unique keys
// and saves the section contents for later reinsertion.
function parse_pp_np(&$data, &$preparsed, &$noparsed) {
    // Find all sections delimited by ~pp~ ... ~/pp~
    // and replace them in the data stream with a unique key
    preg_match_all("/\~pp\~((.|\n)*?)\~\/pp\~/", $data, $preparse);

    foreach (array_unique($preparse[1])as $pp) {
	$key = md5($this->genPass());

	$aux["key"] = $key;
	$aux["data"] = $pp;
	$preparsed[] = $aux;
	$data = str_replace("~pp~$pp~/pp~", $key, $data);
    }

    // Temporary remove <pre> tags too
    // TODO: Is this a problem if user insert <PRE> but after parsing
    //	   will get <pre> (lowercase)?? :)
    preg_match_all("/(<[Pp][Rr][Ee]>)((.|\n)*?)(<\/[Pp][Rr][Ee]>)/", $data, $preparse);
    $idx = 0;

    foreach (array_unique($preparse[2])as $pp) {
	$key = md5($this->genPass());

	$aux["key"] = $key;
	$aux["data"] = $pp;
	$preparsed[] = $aux;
	$data = str_replace($preparse[1][$idx] . $pp . $preparse[4][$idx], $key, $data);
	$idx = $idx + 1;
    }

    // Find all sections delimited by ~np~ ... ~/np~
    $new_data = '';
    $nopa = '';
    $state = true;
    $skip = false;

    for ($i = 0; $i < strlen($data); $i++) {
	$tag5 = substr($data, $i, 5);

	$tag4 = substr($tag5, 0, 4);
	$tag1 = substr($tag4, 0, 1);

	// Beginning of a noparse section found
	if ($state && $tag4 == '~np~') {
	    $i += 3;

	    $state = false;
	    $skip = true;
	}

	// Termination of a noparse section found
	if (!$state && ($tag5 == '~/np~')) {
	    $state = true;

	    $i += 4;
	    $skip = true;
	    $key = md5($this->genPass());
	    $new_data .= $key;
	    $aux["key"] = $key;
	    $aux["data"] = $nopa;
	    $noparsed[] = $aux;
	    $nopa = '';
	}

	if (!$skip) { // This character is not part of a noparse tag
	    if ($state) { // This character is not within a noparse section
		$new_data .= $tag1;
	    } else { // This character is within a noparse section
		$nopa .= $tag1;
	    }
	} else { // Tag is now skipped over
	    $skip = false;
	}
    }

    $data = $new_data;
}

// This recursive function handles pre- and no-parse sections and plugins
function parse_first(&$data, &$preparsed, &$noparsed) {
		global $dbTiki;
    // Handle pre- and no-parse sections
    $this->parse_pp_np($data, $preparsed, $noparsed);

    // Find the plugins
    // note: [1] is plugin name, [2] is plugin arguments
    preg_match_all("/\{([A-Z]+)\(([^\)]*)\)\}/", $data, $plugins);

    // if true, replace only CODE plugin, if false, replace all other plugins
    $code_first = true;

    // Process plugins in reverse order, so that nested plugins are handled
    // from the inside out.
    $i = count($plugins[0]) - 1;

    while ($i >= 0) {
	$plugin_start = $plugins[0][$i];

	$plugin = $plugins[1][$i];
	$plugin_end = '{' . $plugin . '}';
	$plugin_start_base = '{' . $plugins[1][$i] . '(';
	$pos = strpos($data, $plugin_start); // where plugin starts
	$pos_end = strpos($data, $plugin_end, $pos); // where plugin data ends

	if (
		// when in CODE parsing mode, replace only CODE plugins 
		((($code_first) && ($plugin == 'CODE')) ||
		 // when NOT in CODE parsing mode, replace all other plugins 
		 ((!$code_first) && ($plugin <> 'CODE'))) && ($pos_end > $pos)) {
	    // Extract the plugin data
	    $plugin_data_len = $pos_end - $pos - strlen($plugins[0][$i]);

	    $plugin_data = substr($data, $pos + strlen($plugin_start), $plugin_data_len);

	    // Construct plugin file pathname
	    $php_name = 'lib/wiki-plugins/wikiplugin_';
	    $php_name .= strtolower($plugins[1][$i]). '.php';

	    // Construct plugin function name
	    $func_name = 'wikiplugin_' . strtolower($plugins[1][$i]);

	    // Construct argument list array
	    $params = split(',', trim($plugins[2][$i]));
	    $arguments = array();

	    foreach ($params as $param) {
		// the following str_replace line is to decode the &gt; char when html is turned off
		// perhaps the plugin syntax should be changed in 1.8 not to use any html special chars
		$decoded_param = str_replace('&gt;', '>', $param);
		$parts = explode( '=>', $decoded_param );

		if (isset($parts[0]) && isset($parts[1])) {
		    $name = trim($parts[0]);

		    $arguments[$name] = trim($parts[1]);
		}
	    }

	    if (file_exists($php_name)) {
		include_once ($php_name);

		$ret = $func_name($plugin_data, $arguments);

		// Handle pre- & no-parse sections and plugins inserted by this plugin
		$this->parse_first($ret, $preparsed, $noparsed);

		// Replace plugin section with its output in data
		$data = substr_replace($data, $ret, $pos, $pos_end - $pos + strlen($plugin_end));
	    }
	}

	$i--;

	// if we are in CODE parsing mode and list is done, switch to 'parse other plugins' mode and start all over
	if (($code_first) && ($i < 0)) {
	    $i = count($plugins[0]) - 1;

	    $code_first = false;
	}
    } // while
}

// Replace hotwords in given line
function replace_hotwords($line, $words) {
    global $feature_hotwords;

    global $feature_hotwords_nw;
    $hotw_nw = ($feature_hotwords_nw == 'y') ? "target='_blank'" : '';

    // Replace Hotwords
    if ($feature_hotwords == 'y') {
	foreach ($words as $word => $url) {
	    // \b is a word boundary, \s is a space char
	    $line = preg_replace("/^$word(\b)/i","<a class=\"wiki\" href=\"$url\" $hotw_nw>$word</a>$1",$line);
	    $line = preg_replace("/\s$word(\b)/i"," <a class=\"wiki\" href=\"$url\" $hotw_nw>$word</a>$1",$line);
	}
    }

    return $line;
}

//Updates a dynamic variable found in some object
/*Shared*/ function update_dynamic_variable($name,$value) {
	$query = "delete from `tiki_dynamic_variables` where `name`=?";
	$this->query($query,array($name),-1,-1,false);
	$query = "insert into `tiki_dynamic_variables`(`name`,`data`) values(?,?)";
	$this->query($query,Array($name,$value));
	return true;
}

//PARSEDATA
function parse_data($data) {
    global $page_regex;

    global $slidemode;
    global $feature_hotwords;
    global $cachepages;
    global $ownurl_father;
    global $feature_drawings;
    global $tiki_p_admin_drawings;
    global $tiki_p_edit_drawings;
    global $tiki_p_edit_dynvar;
    global $feature_wiki_pictures;
    global $tiki_p_upload_picture;
    global $feature_wiki_plurals;
    global $feature_wiki_tables;
    global $page;
    global $rsslib;
    global $dbTiki;
    global $structlib;
    global $user;
    global $tikidomain;
    global $feature_wikiwords;

    // Process pre_handlers here
    foreach ($this->pre_handlers as $handler) {
	$data = $handler($data);
    }

    // Handle pre- and no-parse sections and plugins
    $preparsed = array();
    $noparsed = array();
    $this->parse_first($data, $preparsed, $noparsed);

    // Extract [link] sections (to be re-inserted later)
    $noparsedlinks = array();

    // This section matches [...].
    // Added handling for [[foo] sections.  -rlpowell
    preg_match_all("/(?<!\[)\[([^\[][^\]]+)\]/", $data, $noparseurl);

    foreach (array_unique($noparseurl[1])as $np) {
	$key = md5($this->genPass());

	$aux["key"] = $key;
	$aux["data"] = $np;
	$noparsedlinks[] = $aux;
	$data = str_replace("$np", $key, $data);
    }

    // Replace special characters
    //done after url catching because otherwise urls of dyn. sites will be modified
    $this->parse_htmlchar($data);

    // Now replace a TOC
    preg_match_all("/\{toc\}/i", $data, $tocs);

    if (count($tocs[0]) > 0) {
	include_once ("lib/structures/structlib.php");

	if ($structlib->page_is_in_structure($page)) {
	    $html = '';

	    if ($slidemode) {
		$toc = $structlib->get_subtree_toc_slide($page, $page, $html);
	    } else {
		$toc = $structlib->get_subtree_toc($page, $page, $html);
	    }

	    // Loop over all the case-specific versions of {toc} used
	    // (if the user is consistent, this is a loop of count 1)
	    for ($i = 0; $i < count($tocs[0]); $i++) {
		$data = str_replace($tocs[0], $html, $data);
	    }
	}
    }

    //unset($smc);

    // Now search for images uploaded by users
    if ($feature_wiki_pictures == 'y') {
	preg_match_all("/\{picture file=([^\}]+)\}/", $data, $pics);

	for ($i = 0; $i < count($pics[0]); $i++) {
	    // Check if the image exists
	    $name = $pics[1][$i];

	    if (file_exists($name)) {
		// Replace by the img tag to show the image
		$repl = "<img src='$name?nocache=1' alt='$name' />";
	    } else {
		$repl = tra('picture not found');
	    }

	    // Replace by $repl
	    $data = str_replace($pics[0][$i], $repl, $data);
	}
    }

    //$data = strip_tags($data);
    // BiDi markers
    $bidiCount = 0;
    $bidiCount = preg_match_all("/(\{l2r\})/", $data, $pages);
    $bidiCount += preg_match_all("/(\{r2l\})/", $data, $pages);

    $data = preg_replace("/\{l2r\}/", "<div dir='ltr'>", $data);
    $data = preg_replace("/\{r2l\}/", "<div dir='rtl'>", $data);
    $data = preg_replace("/\{lm\}/", "&lrm;", $data);
    $data = preg_replace("/\{rm\}/", "&rlm;", $data);
    // smileys
    $data = $this->parse_smileys($data);

    // Replace links to slideshows
    if ($feature_drawings == 'y') {
	// Replace drawings
	// Replace rss modules
	$pars = parse_url($_SERVER["REQUEST_URI"]);

	$pars_parts = split('/', $pars["path"]);
	$pars = array();

	for ($i = 0; $i < count($pars_parts) - 1; $i++) {
	    $pars[] = $pars_parts[$i];
	}

	$pars = join('/', $pars);

	if (preg_match_all("/\{draw +name=([A-Za-z_\-0-9]+) *\}/", $data, $draws)) {
	    //$this->invalidate_cache($page);
	    for ($i = 0; $i < count($draws[0]); $i++) {
		$id = $draws[1][$i];

		$repl = '';
		$name = $id . '.gif';

		if (file_exists("img/wiki/$tikidomain$name")) {
		    if ($tiki_p_edit_drawings == 'y' || $tiki_p_admin_drawings == 'y') {
			$repl = "<a href='#' onClick=\"javascript:window.open('tiki-editdrawing.php?page=" . urlencode($page). "&amp;path=$pars&amp;drawing={$id}','','menubar=no,width=252,height=25');\"><img border='0' src='img/wiki/$tikidomain$name' alt='click to edit' /></a>";
		    } else {
			$repl = "<img border='0' src='img/wiki/$tikidomain$name' alt='a drawing' />";
		    }
		} else {
		    if ($tiki_p_edit_drawings == 'y' || $tiki_p_admin_drawings == 'y') {
			$repl = "<a class='wiki' href='#' onClick=\"javascript:window.open('tiki-editdrawing.php?page=" . urlencode($page). "&amp;path=$pars&amp;drawing={$id}','','menubar=no,width=252,height=25');\">click here to create draw $id</a>";
		    } else {
			$repl = tra('drawing not found');
		    }
		}

		$data = str_replace($draws[0][$i], $repl, $data);
	    }
	}
    }

    // Replace cookies
    if (preg_match_all("/\{cookie\}/", $data, $rsss)) {
	for ($i = 0; $i < count($rsss[0]); $i++) {
	    $cookie = $this->pick_cookie();

	    $data = str_replace($rsss[0][$i], $cookie, $data);
	}
    }

    // Replace dynamic variables
    // Dynamic variables are similar to dynamic content but they are editable
    // from the page directly, intended for short data, not long text but text
    // will work too
    if (preg_match_all("/%([^% ]+)%/",$data,$dvars)) {
	// remove repeated elements
	$dvars = array_unique($dvars[1]);
	// Now replace each dynamic variable by a pair composed of the
	// variable value and a text field to edit the variable. Each 
	foreach($dvars as $dvar) {
		$query = "select `data` from `tiki_dynamic_variables` where `name`=?";
		$result = $this->query($query,Array($dvar));
		if($result->numRows()) {
		$value = $result->fetchRow();
		$value = $value["data"];
	    } else {
		//Default value is NULL
		$value = "NaV";
	    }
	    // Now build 2 divs
	    $id = 'dyn_'.$dvar;

	    if(isset($tiki_p_edit_dynvar)&& $tiki_p_edit_dynvar=='y') {
		$span1 = "<span  style='display:inline;' id='dyn_".$dvar."_display'><a class='dynavar' onClick='javascript:toggle_dynamic_var(\"$dvar\");' title='".tra('Click to edit dynamic variable').": $dvar'>$value</a></span>";
		$span2 = "<span style='display:none;' id='dyn_".$dvar."_edit'><input type='text' name='dyn_".$dvar."' value='".$value."' /></span>";
	    } else {
		$span1 = "<span class='dynavar' style='display:inline;' id='dyn_".$dvar."_display'>$value</span>";
		$span2 = '';							
	    }
	    $html = $span1.$span2;
	    //It's important to replace only once
	    $dvar_preg = preg_quote( $dvar );
	    $data = preg_replace("/%$dvar_preg%/",$html,$data,1);
	    //Further replacements only with the value
	    $data = str_replace("%$dvar%",$value,$data);			

	}
	//At the end put an update button
	//<br /><div align="center"><input type="submit" name="dyn_update" value="'.tra('Update variables').'"/></div>
	$data='<form method="post" name="dyn_vars">'.$data.'<div style="display:none;"><input type="submit" name="_dyn_update" value="'.tra('Update variables').'"/></div></form>';
    }

    // Replace dynamic content occurrences
    if (preg_match_all("/\{content +id=([0-9]+)\}/", $data, $dcs)) {
	for ($i = 0; $i < count($dcs[0]); $i++) {
	    $repl = $this->get_actual_content($dcs[1][$i]);

	    $data = str_replace($dcs[0][$i], $repl, $data);
	}
    }

    // Replace Dynamic content with random selection
    if (preg_match_all("/\{rcontent +id=([0-9]+)\}/", $data, $dcs)) {
	for ($i = 0; $i < count($dcs[0]); $i++) {
	    $repl = $this->get_random_content($dcs[1][$i]);

	    $data = str_replace($dcs[0][$i], $repl, $data);
	}
    }

    // Replace boxes
    $data = preg_replace("/\^([^\^]+)\^/", "<div class=\"simplebox\">$1</div>", $data);
    // Replace colors ~~color:text~~
    $data = preg_replace("/\~\~([^\:]+):([^\~]+)\~\~/", "<span style=\"color:$1;\">$2</span>", $data);
    // Underlined text
    $data = preg_replace("/===([^\=]+)===/", "<span style=\"text-decoration:underline;\">$1</span>", $data);
    // Center text
    $data = preg_replace("/::([^\:]+)::/", "<div align=\"center\">$1</div>", $data);

    // New syntax for wiki pages ((name|desc)) Where desc can be anything
    preg_match_all("/\(\(($page_regex)\|(.+?)\)\)/", $data, $pages);

    for ($i = 0; $i < count($pages[1]); $i++) {
	$pattern = $pages[0][$i];

	$pattern = preg_quote($pattern, "/");

	$pattern = "/" . $pattern . "/";

	// Replace links to external wikis
	$repl2 = true;

	if (strstr($pages[1][$i], ':')) {
	    $wexs = explode(':', $pages[1][$i]);

	    if (count($wexs) == 2) {
		$wkname = $wexs[0];

		if ($this->db->getOne("select count(*) from `tiki_extwiki` where `name`=?",array($wkname)) == 1) {
			$wkurl = $this->db->getOne("select `extwiki`  from `tiki_extwiki` where `name`=?",array($wkname));

		    $wkurl = '<a href="' . str_replace('$page', urlencode($wexs[1]), $wkurl). '" class="wiki">' . $wexs[1] . '</a>';
		    $data = preg_replace($pattern, "$wkurl", $data);
		    $repl2 = false;
		}
	    }
	}

	if ($repl2) {
	    // 24-Jun-2003, by zaufi
	    // TODO: future optimize: get page description and modification time at once.

	    // text[0] = link description (previous format)
	    // text[1] = timeout in seconds (new field)
	    // text[2..N] = drop
	    $text = explode("|", $pages[5][$i]);

	    if ($desc = $this->page_exists_desc($pages[1][$i])) {
		$uri_ref = "tiki-index.php?page=" . urlencode($pages[1][$i]);

		$repl = '<a title="'.$desc.'" href="'.$uri_ref.'" class="wiki">' . (strlen(trim($text[0])) > 0 ? $text[0] : $pages[1][$i]) . '</a>';

		// Check is timeout expired?
		if (isset($text[1]) && (time() - intval($this->page_exists_modtime($pages[1][$i]))) < intval($text[1]))
		    // Append small 'new' image. TODO: possible 'updated' image more suitable...
		    $repl .= '&nbsp;<img src="img/icons/new.gif" border="0">';
	    } else {
		$uri_ref = "tiki-editpage.php?page=" . urlencode($pages[1][$i]);

		$repl = (strlen(trim($text[0])) > 0 ? $text[0] : $pages[1][$i]) . '<a href="'.$uri_ref.'" class="wiki">?</a>';
	    }

	    $data = preg_replace($pattern, "$repl", $data);
	}
    }

    // New syntax for wiki pages ((name)) Where name can be anything
    preg_match_all("/\(\(($page_regex)\)\)/", $data, $pages);

    foreach (array_unique($pages[1])as $page_parse) {
	$repl2 = true;

	if (strstr($page_parse, ':')) {
	    $wexs = explode(':', $page_parse);

	    if (count($wexs) == 2) {
		$wkname = $wexs[0];

		if ($this->db->getOne("select count(*) from `tiki_extwiki` where `name`=?",array($wkname)) == 1) {
			$wkurl = $this->db->getOne("select `extwiki`  from `tiki_extwiki` where `name`=?",array($wkname));

		    $wkurl = '<a href="' . str_replace('$page', urlencode($wexs[1]), $wkurl). '" class="wiki">' . $wexs[1] . '</a>';
		    $data = preg_replace("/\(\($page_parse\)\)/", "$wkurl", $data);
		    $repl2 = false;
		}
	    }
	}

	if ($repl2) {
	    if ($desc = $this->page_exists_desc($page_parse)) {
		$repl = "<a title=\"$desc\" href='tiki-index.php?page=" . urlencode($page_parse). "' class='wiki'>$page_parse</a>";
	    } else {
		$repl = "$page_parse<a href='tiki-editpage.php?page=" . urlencode($page_parse). "' class='wiki'>?</a>";
	    }

	    $page_parse_pq = preg_quote($page_parse, "/");
	    $data = preg_replace("/\(\($page_parse_pq\)\)/", "$repl", $data);
	}
    }

    // Links to internal pages
    // If they are parenthesized then don't treat as links
    // Prevent ))PageName(( from being expanded	\"\'
    //[A-Z][a-z0-9_\-]+[A-Z][a-z0-9_\-]+[A-Za-z0-9\-_]*
    if ($feature_wikiwords == 'y') {
	// The first part is now mandatory to prevent [Foo|MyPage] from being converted!
	preg_match_all("/([ \n\t\r\,\;]|^)([A-Z][a-z0-9_\-]+[A-Z][a-z0-9_\-]+[A-Za-z0-9\-_]*)($|[ \n\t\r\,\;\.])/", $data, $pages);
	$words = $this->get_hotwords();
	foreach (array_unique($pages[2])as $page_parse) {
	    if (!array_key_exists($page_parse, $words)) {
		if ($desc = $this->page_exists_desc($page_parse)) {
		    $repl = '<a title="' . $desc . '" href="tiki-index.php?page=' . urlencode($page_parse). '" class="wiki">' . $page_parse . '</a>';
		} elseif ($feature_wiki_plurals == 'y' && $this->get_locale() == 'en_US') {
# Link plural topic names to singular topic names if the plural
# doesn't exist, and the language is english
		    $plural_tmp = $page_parse;
# Plurals like policy / policies
		    $plural_tmp = preg_replace("/ies$/", "y", $plural_tmp);
# Plurals like address / addresses
		    $plural_tmp = preg_replace("/sses$/", "ss", $plural_tmp);
# Plurals like box / boxes
		    $plural_tmp = preg_replace("/([Xx])es$/", "$1", $plural_tmp);
# Others, excluding ending ss like address(es)
		    $plural_tmp = preg_replace("/([A-Za-rt-z])s$/", "$1", $plural_tmp);
		    if($desc = $this->page_exists_desc($plural_tmp)) {
			$repl = "<a title='".$desc."' href='tiki-index.php?page=$plural_tmp' class='wiki'>$page_parse</a>";
		    } else {
			$repl = "$page_parse<a href='tiki-editpage.php?page=".urlencode($page_parse)."' class='wiki'>?</a>";
		    }
		} else {
		    $repl = "$page_parse<a href='tiki-editpage.php?page=" . urlencode($page_parse). "' class='wiki'>?</a>";
		}

		$data = preg_replace("/([ \n\t\r\,\;]|^)$page_parse($|[ \n\t\r\,\;\.])/", "$1" . "$repl" . "$2", $data);
		//$data = str_replace($page_parse,$repl,$data);
	    }
	}
    }

    // This protects ))word((, I think?
    $data = preg_replace("/([ \n\t\r\,\;]|^)\)\)([^\(]+)\(\(($|[ \n\t\r\,\;\.])/", "$1" . "$2" . "$3", $data);

    // reinsert hash-replaced links into page
    foreach ($noparsedlinks as $np) {
	$data = str_replace($np["key"], $np["data"], $data);
    }

    // TODO: I think this is 1. just wrong and 2. not needed here? remove it?
    // Replace ))Words((
    $data = preg_replace("/\(\(([^\)]+)\)\)/", "$1", $data);

    // Images
    preg_match_all("/(\{img [^\}]+})/", $data, $pages);

    foreach (array_unique($pages[1])as $page_parse) {
	$parts = explode(" ", $page_parse);

	$imgdata = array();
	$imgdata["src"] = '';
	$imgdata["height"] = '';
	$imgdata["width"] = '';
	$imgdata["link"] = '';
	$imgdata["align"] = '';
	$imgdata["desc"] = '';

	foreach ($parts as $part) {
	    $part = str_replace('}', '', $part);

	    $part = str_replace('{', '', $part);
	    $part = str_replace('\'', '', $part);
	    $part = str_replace('"', '', $part);

	    if (strstr($part, '=')) {
		$subs = explode("=", $part, 2);

		$imgdata[$subs[0]] = $subs[1];
	    }
	}

	//print("todo el tag es: ".$page_parse."<br/>");
	//print_r($imgdata);
	$repl = '<img alt="' . tra('Image') . '" src="'.$imgdata["src"].'" border="0" ';

	if ($imgdata["width"])
	    $repl .= ' width="' . $imgdata["width"] . '"';

	if ($imgdata["height"])
	    $repl .= ' height="' . $imgdata["height"] . '"';

	$repl .= ' />';

	if ($imgdata["link"]) {
	    $repl = '<a href="' . $imgdata["link"] . '">' . $repl . '</a>';
	}

	if ($imgdata["desc"]) {
	    $repl = '<table cellpadding="0" cellspacing="0"><tr><td>' . $repl . '</td></tr><tr><td class="mini">' . $imgdata["desc"] . '</td></tr></table>';
	}

	if ($imgdata["align"]) {
	    $repl = '<div align="' . $imgdata["align"] . '">' . $repl . "</div>";
	}

	$data = str_replace($page_parse, $repl, $data);
    }

    $links = $this->get_links($data);

    $notcachedlinks = $this->get_links_nocache($data);

    $cachedlinks = array_diff($links, $notcachedlinks);

    $this->cache_links($cachedlinks);

    // Note that there're links that are replaced
    foreach ($links as $link)
    {
	$target = '';

	if ($this->get_preference('popupLinks', 'n') == 'y')
	{
	    $target = 'target="_blank"';
	}

	if (strstr($link, $_SERVER["SERVER_NAME"]))
	{
	    $target = '';
	}

	if (!strstr($link, '//'))
	{
	    $target = '';
	}

	// The (?<!\[) stuff below is to give users an easy way to
	// enter square brackets in their output; things like [[foo]
	// get rendered as [foo]. -rlpowell

	if ($this->is_cached($link) && $cachepages == 'y')
	{
	    //use of urlencode for using cached versions of dynamic sites
	    $cosa = "<a class=\"wikicache\" target=\"_blank\" href=\"tiki-view_cache.php?url=".urlencode($link)."\">(cache)</a>";

	    //$link2 = str_replace("/","\/",$link);
	    //$link2 = str_replace("?","\?",$link2);
	    //$link2 = str_replace("&","\&",$link2);
	    $link2 = str_replace("/", "\/", preg_quote($link));
	    $pattern = "/(?<!\[)\[$link2\|([^\]\|]+)\|([^\]]+)\]/";
	    $data = preg_replace($pattern, "<a class='wiki' $target href='$link'>$1</a>", $data);
	    $pattern = "/(?<!\[)\[$link2\|([^\]\|]+)\]/";
	    $data = preg_replace($pattern, "<a class='wiki' $target href='$link'>$1</a> $cosa", $data);
	    $pattern = "/(?<!\[)\[$link2\]/";
	    $data = preg_replace($pattern, "<a class='wiki' $target href='$link'>$link</a> $cosa", $data);
	} else {
	    //$link2 = str_replace("/","\/",$link);
	    //$link2 = str_replace("?","\?",$link2);
	    //$link2 = str_replace("&","\&",$link2);
	    $link2 = str_replace("/", "\/", preg_quote($link));

	    $pattern = "/(?<!\[)\[$link2\|([^\]\|]+)([^\]])*\]/";
	    $data = preg_replace($pattern, "<a class='wiki' $target href='$link'>$1</a>", $data);
	    $pattern = "/(?<!\[)\[$link2\]/";
	    $data = preg_replace($pattern, "<a class='wiki' $target href='$link'>$link</a>", $data);
	}

    }

    // Handle double square brackets.  -rlpowell
    $data = str_replace( "[[", "[", $data );

    if ($feature_wiki_tables != 'new') {
	// New syntax for tables
	if (preg_match_all("/\|\|(.*)\|\|/", $data, $tables)) {
	    $maxcols = 1;

	    $cols = array();

	    for ($i = 0; $i < count($tables[0]); $i++) {
		$rows = explode('||', $tables[0][$i]);

		$col[$i] = array();

		for ($j = 0; $j < count($rows); $j++) {
		    $cols[$i][$j] = explode('|', $rows[$j]);

		    if (count($cols[$i][$j]) > $maxcols)
			$maxcols = count($cols[$i][$j]);
		}
	    }

	    for ($i = 0; $i < count($tables[0]); $i++) {
		$repl = '<table class="wikitable">';

		for ($j = 0; $j < count($cols[$i]); $j++) {
		    $ncols = count($cols[$i][$j]);

		    if ($ncols == 1 && !$cols[$i][$j][0])
			continue;

		    $repl .= '<tr>';

		    for ($k = 0; $k < $ncols; $k++) {
			$repl .= '<td class="wikicell" ';

			if ($k == $ncols - 1 && $ncols < $maxcols)
			    $repl .= ' colspan=' . ($maxcols - $k);

			$repl .= '>' . $cols[$i][$j][$k] . '</td>';
		    }

		    $repl .= '</tr>';
		}

		$repl .= '</table>';
		$data = str_replace($tables[0][$i], $repl, $data);
	    }
	}
    } else {
	// New syntax for tables
	// REWRITE THIS CODE
	if (preg_match_all("/\|\|(.*?)\|\|/s", $data, $tables)) {
	    $maxcols = 1;

	    $cols = array();

	    for ($i = 0; $i < count($tables[0]); $i++) {
		$rows = split("\n|\<br\/\>", $tables[0][$i]);

		$col[$i] = array();

		for ($j = 0; $j < count($rows); $j++) {
		    $rows[$j] = str_replace('||', '', $rows[$j]);

		    $cols[$i][$j] = explode('|', $rows[$j]);

		    if (count($cols[$i][$j]) > $maxcols)
			$maxcols = count($cols[$i][$j]);
		}
	    }

	    for ($i = 0; $i < count($tables[0]); $i++) {
		$repl = '<table class="wikitable">';

		for ($j = 0; $j < count($cols[$i]); $j++) {
		    $ncols = count($cols[$i][$j]);

		    if ($ncols == 1 && !$cols[$i][$j][0])
			continue;

		    $repl .= '<tr>';

		    for ($k = 0; $k < $ncols; $k++) {
			$repl .= '<td class="wikicell" ';

			if ($k == $ncols - 1 && $ncols < $maxcols)
			    $repl .= ' colspan=' . ($maxcols - $k);

			$repl .= '>' . $cols[$i][$j][$k] . '</td>';
		    }

		    $repl .= '</tr>';
		}

		$repl .= '</table>';
		$data = str_replace($tables[0][$i], $repl, $data);
	    }
	}
    }


    // 26-Jun-2003, by zaufi
    //
    // {maketoc} --> create TOC from '!', '!!', '!!!' in current document
    //
    preg_match_all("/\{maketoc\}/", $data, $tocs);
    $anch = array();

    // 08-Jul-2003, by zaufi
    // HotWords will be replace only in ordinal text
    // It looks __realy__ goofy in Headers or Titles

    // Get list of HotWords
    $words = $this->get_hotwords();

    // Now tokenize the expression and process the tokens
    // Use tab and newline as tokenizing characters as well  ////
    $lines = explode("\n", $data);
    $data = '';
    $listbeg = array();
    $divdepth = array();
    $inTable = 0;

    // loop: process all lines
    foreach ($lines as $line) {

	// Check for titlebars...
	// NOTE: that title bar should be start from begining of line and
	//	   be alone on that line to be autoaligned... else it is old styled
	//	   styled title bar...
	if (substr(ltrim($line), 0, 2) == '-=' && substr(rtrim($line), -2, 2) == '=-') {
	    // This is not list item -- must close lists currently opened
	    while (count($listbeg))
		$data .= array_shift($listbeg);

	    //
	    $align_len = strlen($line) - strlen(ltrim($line));

	    // My textarea size is about 120 space chars.
	    //define('TEXTAREA_SZ', 120);

	    // NOTE: That strict math formula (split into 3 areas) gives
	    //	   bad visual effects...
	    // $align = ($align_len < (TEXTAREA_SZ / 3)) ? "left" 
	    //		: (($align_len > (2 * TEXTAREA_SZ / 3)) ? "right" : "center");
	    //
	    // Going to introduce some heuristic here :)
	    // Visualy (remember that space char is thin) center starts at 25 pos
	    // and 'right' from 60 (HALF of full width!) -- thats all :)
	    //
	    // NOTE: Guess align only if more than 10 spaces before -=title=-
	    if ($align_len > 10) {
		$align = ($align_len < 25) ? "left" : (($align_len > 60) ? "right" : "center");

		$align = ' style="text-align: ' . $align . ';"';
	    } else
		$align = '';

	    //
	    $line = trim($line);
	    $line = '<div class="titlebar"' . $align . '>' . substr($line, 2, strlen($line) - 4). '</div>';
	    $data .= $line;
	    // TODO: Case is handled ...  no need to check other conditions
	    //	   (it is apriory known all they false, moreover sometimes
	    //	   check procedure need > O(0) of compexity)
	    //	   -- continue to next line...
	    //	   MUST replace all remaining parse blocks to the same logic...
	    continue;
	}

	// Replace old styled titlebars 
	if (strlen($line) != strlen($line = preg_replace("/-=(.+?)=-/", "<div class='titlebar'>$1</div>", $line))) {
	    $data .= $line;

	    continue;
	}

	// check if we are inside a table, if so, ignore monospaced and do
	// not insert <br/>
	$inTable += substr_count($line, "<table");
	$inTable -= substr_count($line, "</table");

	// If the first character is ' ' and we are not in pre then we are in pre
	global $feature_wiki_monosp;

	if (substr($line, 0, 1) == ' ' && $feature_wiki_monosp == 'y' && $inTable == 0) {
	    // This is not list item -- must close lists currently opened
	    while (count($listbeg))
		$data .= array_shift($listbeg);

	    // If the first character is space then
	    // change spaces for &nbsp;
	    $line = '<font face="courier">' . str_replace(' ', '&nbsp;', substr($line, 1)). '</font>';
	}

	// Replace Hotwords before begin
	$line = $this->replace_hotwords($line, $words);

	// Replace monospaced text
	$line = preg_replace("/-\+(.*?)\+-/", "<code>$1</code>", $line);
	// Replace bold text
	$line = preg_replace("/__(.*?)__/", "<b>$1</b>", $line);
	$line = preg_replace("/\'\'(.*?)\'\'/", "<i>$1</i>", $line);
	// Replace definition lists
	$line = preg_replace("/^;([^:]+):([^\n]+)/", "<dl><dt>$1</dt><dd>$2</dd></dl>", $line);

	if (0) {
	    $line = preg_replace("/\[([^\|]+)\|([^\]]+)\]/", "<a class='wiki' $target href='$1'>$2</a>", $line);

	    // Segundo intento reemplazar los [link] comunes
	    $line = preg_replace("/\[([^\]]+)\]/", "<a class='wiki' $target href='$1'>$1</a>", $line);
	    $line = preg_replace("/\-\=([^=]+)\=\-/", "<div class='wikihead'>$1</div>", $line);
	}

	// This line is parseable then we have to see what we have
	if (substr($line, 0, 3) == '---') {
	    // This is not list item -- must close lists currently opened
	    while (count($listbeg))
		$data .= array_shift($listbeg);

	    $line = '<hr/>';
	} else {
	    $litype = substr($line, 0, 1);

	    if ($litype == '*' || $litype == '#') {
		$listlevel = $this->how_many_at_start($line, $litype);

		$liclose = '</li>';
		$addremove = 0;

		if ($listlevel < count($listbeg)) {
		    while ($listlevel != count($listbeg))
			$data .= array_shift($listbeg);

		    if (substr(current($listbeg), 0, 5) != '</li>')
			$liclose = '';
		} elseif ($listlevel > count($listbeg)) {
		    $listyle = '';

		    while ($listlevel != count($listbeg)) {
			array_unshift($listbeg, ($litype == '*' ? '</ul>' : '</ol>'));

			if ($listlevel == count($listbeg)) {
			    $listate = substr($line, $listlevel, 1);

			    if (($listate == '+' || $listate == '-') && !($litype == '*' && !strstr(current($listbeg), '</ul>') || $litype == '#' && !strstr(current($listbeg), '</ol>'))) {
				$thisid = 'id' . microtime() * 1000000;

				$data .= '<br/><a id="flipper' . $thisid . '" class="link" href="javascript:flipWithSign(\'' . $thisid . '\')">[' . ($listate == '-' ? '+' : '-') . ']</a>';
				$listyle = ' id="' . $thisid . '" style="display:' . ($listate == '+' ? 'block' : 'none') . ';"';
				$addremove = 1;
			    }
			}

			$data .= ($litype == '*' ? "<ul$listyle>" : "<ol$listyle>");
		    }

		    $liclose = '';
		}

		if ($litype == '*' && !strstr(current($listbeg), '</ul>') || $litype == '#' && !strstr(current($listbeg), '</ol>')) {
		    $data .= array_shift($listbeg);

		    $listyle = '';
		    $listate = substr($line, $listlevel, 1);

		    if (($listate == '+' || $listate == '-')) {
			$thisid = 'id' . microtime() * 1000000;

			$data .= '<br/><a id="flipper' . $thisid . '" class="link" href="javascript:flipWithSign(\'' . $thisid . '\')">[' . ($listate == '-' ? '+' : '-') . ']</a>';
			$listyle = ' id="' . $thisid . '" style="display:' . ($listate == '+' ? 'block' : 'none') . ';"';
			$addremove = 1;
		    }

		    $data .= ($litype == '*' ? "<ul$listyle>" : "<ol$listyle>");
		    $liclose = '';
		    array_unshift($listbeg, ($litype == '*' ? '</li></ul>' : '</li></ol>'));
		}

		$line = $liclose . '<li>' . substr($line, $listlevel + $addremove);

		if (substr(current($listbeg), 0, 5) != '</li>')
		    array_unshift($listbeg, '</li>' . array_shift($listbeg));
	    } elseif ($litype == '+') {
		// Must append paragraph for list item of given depth...
		$listlevel = $this->how_many_at_start($line, $litype);

		// Close lists down to requested level
		while ($listlevel < count($listbeg))
		    $data .= array_shift($listbeg);

		if (count($listbeg)) {
		    if (substr(current($listbeg), 0, 5) != '</li>') {
			array_unshift($listbeg, '</li>' . array_shift($listbeg));

			$liclose = '<li>';
		    } else
			$liclose = '<br/>';
		} else
		    $liclose = '';

		$line = $liclose . substr($line, count($listbeg));
	    } else {
		// This is not list item -- must close lists currently opened
		while (count($listbeg))
		    $data .= array_shift($listbeg);

		// Get count of (possible) header signs at start
		$hdrlevel = $this->how_many_at_start($line, '!');

		// If 1st char on line is '!' and its count less than 6 (max in HTML)
		if ($litype == '!' && $hdrlevel > 0 && $hdrlevel <= 6) {
		    // Remove possible hotwords replaced :)
		    //   Umm, *why*?  Taking this out lets page
		    //   links in headers work, which can be nice.
		    //   -rlpowell
		    // $line = strip_tags($line);

		    // OK. Parse headers here...
		    $anchor = '';
		    $aclose = '';
		    $addremove = 0;

		    // Close lower level divs if opened
		    for (;current($divdepth) >= $hdrlevel; array_shift($divdepth))
			$data .= '</div>';

		    // May be spesial signs present after '!'s?
		    $divstate = substr($line, $hdrlevel, 1);

		    if ($divstate == '+' || $divstate == '-') {
			// OK. Must insert flipper after HEADER, and then open new div...
			$thisid = 'id' . microtime() * 1000000;

			$aclose = '<a id="flipper' . $thisid . '" class="link" href="javascript:flipWithSign(\'' . $thisid . '\')">[' . ($divstate == '-' ? '+' : '-') . ']</a>';
			$aclose .= '<div id="' . $thisid . '" style="display:' . ($divstate == '+' ? 'block' : 'none') . ';">';
			array_unshift($divdepth, $hdrlevel);
			$addremove = 1;
		    }

		    // Is any {maketoc} present on page?
		    if (count($tocs[0]) > 0) {
			// OK. Must insert <a id=...> before HEADER and collect TOC entry
			$thisid = 'id' . microtime() * 1000000;

			array_push($anch, str_repeat("*", $hdrlevel). " <a href='#$thisid'>" . substr($line, $hdrlevel + $addremove). '</a>');
			$anchor = "<a id='$thisid'>";
			$aclose = '</a>' . $aclose;
		    }

		    $line = $anchor . "<h$hdrlevel>" . substr($line, $hdrlevel + $addremove). "</h$hdrlevel>" . $aclose;
		} elseif (!strcmp($line, "...page...")) {
		    // Close lists and divs currently opened
		    while (count($listbeg))
			$data .= array_shift($listbeg);

		    while (count($divdepth)) {
			$data .= '</div>';

			array_shift ($divdepth);
		    }

		    // Leave line unchanged... tiki-index.php will split wiki here
		    $line = "...page...";
		} else {
		    // Usual paragraph.
		    if ($inTable == 0) {
			$line .= '<br/>';
		    }
		}
	    }
	}

	$data .= $line;
    }

    // Close lists may remains opened
    while (count($listbeg))
	$data .= array_shift($listbeg);

    // Close header divs may remains opened
    for ($i = 1; $i <= count($divdepth); $i++)
	$data .= '</div>';

    // 26-Jun-2003, by zaufi
    // Replace {maketoc} from collected list of headers
    $html = '';

    foreach ($anch as $tocentry) {
	$html .= $tocentry . "\n";
    }

    if (count($anch))
	$html = $this->parse_data($html);

    $data = str_replace("{maketoc}", $html, $data);

    // Replace rss modules
    if (preg_match_all("/\{rss +id=([0-9]+) *(max=([0-9]+))? *\}/", $data, $rsss)) {
	if (!isset($rsslib)) {
	    include ('lib/rss/rsslib.php');
	}

	for ($i = 0; $i < count($rsss[0]); $i++) {
	    $id = $rsss[1][$i];

	    $max = $rsss[3][$i];

	    if (empty($max))
		$max = 99;

	    $rssdata = $rsslib->get_rss_module_content($id);
	    $items = $rsslib->parse_rss_data($rssdata, $id);

	    $repl = '<ul>';

	    for ($j = 1; $j < count($items) && $j < $max; $j++) {
		$repl .= '<li><a target="_blank" href="' . $items[$j]["link"] . '" class="wiki">' . $items[$j]["title"] . '</a>';
		$repl .= ' ('.$items[$j]["pubdate"].')';
			$repl .= '</li>';
			}

			$repl .= '</ul>';
			$data = str_replace($rsss[0][$i], $repl, $data);
			}
			}

			// Close BiDi DIVs if any
			for ($i = 0; $i < $bidiCount; $i++) {
			$data .= "</div>";
			}

			foreach ($noparsed as $np) {
			$data = str_replace($np["key"], $np["data"], $data);
			}

			foreach ($preparsed as $pp) {
			$data = str_replace($pp["key"], "<pre>" . $pp["data"] . "</pre>", $data);
			}

			// Process pos_handlers here
			foreach ($this->pos_handlers as $handler) {
			    $data = $handler($data);
			}

			return $data;
}

function parse_smileys($data) {
    global $feature_smileys;

    if ($feature_smileys == 'y') {
	$data = preg_replace("/\(:([^:]+):\)/", "<img alt=\"$1\" src=\"img/smiles/icon_$1.gif\" />", $data);
    }

    return $data;
}

function parse_comment_data($data) {
    $data = preg_replace("/\[([^\|\]]+)\|([^\]]+)\]/", "<a class=\"commentslink\" href=\"$1\">$2</a>", $data);

    // Segundo intento reemplazar los [link] comunes
    $data = preg_replace("/\[([^\]\|]+)\]/", "<a class=\"commentslink\" href=\"$1\">$1</a>", $data);
    // Llamar aqui a parse smileys
    $data = $this->parse_smileys($data);
    $data = preg_replace("/---/", "<hr/>", $data);
    // Reemplazar --- por <hr/>
    return $data;
}

function get_pages($data) {
    global $page_regex;

    global $feature_wikiwords;

    if ($feature_wikiwords == 'y') {
	preg_match_all("/\(\(($page_regex)\)\)/", $data, $pages2);
	preg_match_all("/\(\(($page_regex)\|(.+?)\)\)/", $data, $pages3);

	preg_match_all("/([ \n\t\r\,\;]|^)?([A-Z][a-z0-9_\-]+[A-Z][a-z0-9_\-]+[A-Za-z0-9\-_]*)($|[ \n\t\r\,\;\.])/", $data, $pages);
	$pages = array_unique(array_merge($pages[2], $pages2[1], $pages3[1]));
    } else {
	preg_match_all("/\(\(($page_regex)\)\)/", $data, $pages);

	preg_match_all("/\(\(($page_regex)\|(.+?)\)\)/", $data, $pages2);
	$pages = array_unique(array_merge($pages[1], $pages2[1]));
    }

    return $pages;
}

function clear_links($page) {
    $query = "delete from `tiki_links` where `fromPage`=?";
    $result = $this->query($query, array($page));
}

function replace_link($pageFrom, $pageTo) {
    $query = "delete from `tiki_links` where `fromPage`=? and `toPage`=?";
    $result = $this->query($query, array($pageFrom,$pageTo));
    $query = "insert into `tiki_links`(`fromPage`,`toPage`) values(?, ?)";
    $result = $this->query($query, array($pageFrom,$pageTo));
}

function invalidate_cache($page) {
    $query = "update `tiki_pages` set `cache_timestamp`=? where `pageName`=?";
    $this->query($query, array(0,$page) );
}

function update_page($pageName, $edit_data, $edit_comment, $edit_user, $edit_ip, $description = '', $minor = false) {
    global $smarty;

    global $dbTiki;
    global $notificationlib;
    global $feature_user_watches;
    global $wiki_watch_editor;
    global $sender_email;
    include_once ('lib/notifications/notificationlib.php');
    include_once ("lib/commentslib.php");

    $commentslib = new Comments($dbTiki);

    $this->invalidate_cache($pageName);
    // Collect pages before modifying edit_data (see update of links below)
    $pages = $this->get_pages($edit_data);

    if (!$this->page_exists($pageName))
	return false;

    $t = date("U");
    // Get this page information
    $info = $this->get_page_info($pageName);
    // Store the old version of this page in the history table
    $version = $info["version"];
    $lastModif = $info["lastModif"];
    $user = $info["user"];
    $ip = $info["ip"];
    $comment = $info["comment"];
    $data = $info["data"];
    // WARNING: POTENTIAL BUG
    // The line below is not consistent with the rest of Tiki
    // (I commented it out so it can be further examined by CVS change control)
    //$pageName=addslashes($pageName);
    // But this should work (comment added by redflo):
    $version += 1;

    if (!$minor) {
	$query = "insert into `tiki_history`(`pageName`, `version`, `lastModif`, `user`, `ip`, `comment`, `data`, `description`)
	    values(?,?,?,?,?,?,?,?)";

	if ($pageName != 'SandBox') {
	    $result = $this->query($query,array($pageName,(int) $version,(int) $lastModif,$user,$ip,$comment,$data,$description));
	}

	// Update the pages table with the new version of this page

	$emails = $notificationlib->get_mail_events('wiki_page_changes', 'wikipage' . $pageName);

	foreach ($emails as $email) {
	    $smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);

	    $smarty->assign('mail_page', $pageName);
	    $smarty->assign('mail_date', date("U"));
	    $smarty->assign('mail_user', $edit_user);
	    $smarty->assign('mail_comment', $edit_comment);
	    $smarty->assign('mail_last_version', $version);
	    $smarty->assign('mail_data', $edit_data);
	    $foo = parse_url($_SERVER["REQUEST_URI"]);
	    $machine = httpPrefix(). dirname( $foo["path"] );
	    $smarty->assign('mail_machine', $machine);
	    $smarty->assign('mail_pagedata', $edit_data);
	    $mail_data = $smarty->fetch('mail/wiki_change_notification.tpl');

	    if( $this->get_preference('wiki_forum') )
	    {
		$forums = $commentslib->list_forums( 0, 1,
			'name_asc',
			$this->get_preference('wiki_forum') );

		$forumEmail = $forums["data"][0]["outbound_from"];

		@mail($email, $pageName, $mail_data,
			"From: $forumEmail\r\nContent-type: text/plain;charset=utf-8\r\n"
		     );
	    } else {
		@mail($email, tra('Wiki page'). ' ' . $pageName . '
			' . tra('changed'), $mail_data,
			"From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n"
		     );
	    }
	}

	if ($feature_user_watches == 'y') {
	    $nots = $this->get_event_watches('wiki_page_changed', $pageName);

	    foreach ($nots as $not) {
		if ($wiki_watch_editor != 'y' && $not['user'] == $user) break;
		$smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);

		$smarty->assign('mail_page', $pageName);
		$smarty->assign('mail_date', date("U"));
		$smarty->assign('mail_user', $edit_user);
		$smarty->assign('mail_comment', $edit_comment);
		$smarty->assign('mail_last_version', $version);
		$smarty->assign('mail_data', $edit_data);
		$smarty->assign('mail_hash', $not['hash']);
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$machine = httpPrefix(). dirname( $foo["path"] );
		$smarty->assign('mail_machine', $machine);
		$parts = explode('/', $foo['path']);

		if (count($parts) > 1)
		    unset ($parts[count($parts) - 1]);

		$smarty->assign('mail_machine_raw', httpPrefix(). implode('/', $parts));
		$smarty->assign('mail_pagedata', $edit_data);
		$mail_data = $smarty->fetch('mail/user_watch_wiki_page_changed.tpl');
		@mail($not['email'], tra('Wiki page'). ' ' . $pageName . ' ' . tra('changed'), $mail_data, "From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n");
	    }
	}
    }

    $query = "update `tiki_pages` set `description`=?, `data`=?, `comment`=?, `lastModif`=?, `version`=?, `user`=?, `ip`=?, `page_size`=? where `pageName`=?";
    $result = $this->query($query,array($description,$edit_data,$edit_comment,(int) $t,$version,$edit_user,$edit_ip,(int)strlen($data),$pageName));
    // Parse edit_data updating the list of links from this page
    $this->clear_links($pageName);

    // Pages collected above
    foreach ($pages as $page) {
	$this->replace_link($pageName, $page);
    }

    // Update the log
    if ($pageName != 'SandBox' && !$minor) {
	$action = "Updated";

	$query = "insert into `tiki_actionlog`(`action`,`pageName`,`lastModif`,`user`,`ip`,`comment`) values(?,?,?,?,?,?)";
	$result = $this->query($query,array($action,$pageName,(int) $t,$edit_user,$edit_ip,$edit_comment));
	$maxversions = $this->get_preference("maxVersions", 0);

	if ($maxversions) {
	    // Select only versions older than keep_versions days
	    $keep = $this->get_preference('keep_versions', 0);

	    $now = date("U");
	    $oktodel = $now - ($keep * 24 * 3600);
	    $query = "select `pageName` ,`version` from `tiki_history` where `pageName`=? and `lastModif`<=? order by `lastModif` desc";
	    $result = $this->query($query,array($pageName,$oktodel),-1,$maxversions);
	    $toelim = $result->numRows();

	    while ($res = $result->fetchRow()) {
		$page = $res["pageName"];

		$version = $res["version"];
		$query = "delete from `tiki_history` where `pageName`=? and `version`=?";
		$this->query($query,array($pageName,$version));
	    }
	}
    }
}

function update_page_version($pageName, $version, $edit_data, $edit_comment, $edit_user, $edit_ip, $lastModif, $description = '') {
    global $smarty;

    if ($pageName == 'SandBox')
	return;

    // Collect pages before modifying edit_data
    $pages = $this->get_pages($edit_data);

    if (!$this->page_exists($pageName))
	return false;

    $t = date("U");
    $query = "delete from `tiki_history` where `pageName`=? and `version`=?";
    $result = $this->query($query, array( $pageName,(int) $version) );
    $query = "insert into `tiki_history`(pageName, version, lastModif, user, ip, comment, data,description) values(?,?,?, ?,?,?, ?,?)";
    $result = $this->query($query, array($pageName,(int)$version,(int)$lastModif, $edit_user,$edit_ip,$edit_comment, $edit_data,$description)
	    );

    //print("version: $version<br/>");
    // Get this page information
    $info = $this->get_page_info($pageName);

    if ($version >= $info["version"]) {
	$query = "update `tiki_pages` set `data`=?, `comment`=?, `lastModif`=?, `version`=?, `user`=?, `ip`=?, `description`=?,`page_size`=?  where `pageName`=?";
	$result = $this->query($query, array( $edit_data, $edit_comment, (int) $t, (int) $version, $edit_user, $edit_ip, $description, (int) strlen($data), $pageName ) );
	// Parse edit_data updating the list of links from this page
	$this->clear_links($pageName);

	// Pages are collected at the top of the function before adding slashes
	foreach ($pages as $page) {
	    $this->replace_link($pageName, $page);
	}
    }
}

# TODO move all of these date/time functions to a static class: TikiDate
function get_timezone_list($use_default = false) {
    static $timezone_options;

    if (!$timezone_options) {
	$timezone_options = array();

	if ($use_default)
	    $timezone_options['default'] = '-- Use Default Time Zone --';

	foreach ($GLOBALS['_DATE_TIMEZONE_DATA'] as $tz_key => $tz) {
	    $offset = $tz['offset'];

	    $absoffset = abs($offset /= 60000);
	    $plusminus = $offset < 0 ? '-' : '+';
	    $gmtoff = sprintf("GMT%1s%02d:%02d", $plusminus, $absoffset / 60, $absoffset - (intval($absoffset / 60) * 60));
	    $tzlongshort = $tz['longname'] . ' (' . $tz['shortname'] . ')';
		    $timezone_options[$tz_key] = sprintf('%-28.28s: %-36.36s %s', $tz_key, $tzlongshort, $gmtoff);
		    }
		    }

		    return $timezone_options;
		    }

		    function get_server_timezone() {
		    static $server_timezone;

		    if (!$server_timezone) {
		    $server_time = new Date();

		    $server_timezone = $server_time->tz->getID();
		    }

		    return $server_timezone;
		    }

# TODO rename get_site_timezone()
		    function get_display_timezone($user = false) {
			static $display_timezone = false;

			if (!$display_timezone) {
			    $server_time = $this->get_server_timezone();

			    if ($user) {
				$display_timezone = $this->get_user_preference($user, 'display_timezone');

				if (!$display_timezone || $display_timezone == 'default') {
				    $display_timezone = $this->get_preference('display_timezone', $server_time);
				}
			    } else {
				$display_timezone = $this->get_preference('display_timezone', $server_time);
			    }
			}

			return $display_timezone;
		    }

/**
 * Retrieves the user's preferred offset for displaying dates.
 *
 * $user: the logged-in user.
 * returns: the preferred offset to UTC.
 */
function get_display_offset($_user = false) {

    // Cache preference from DB
    $display_tz = "UTC";

    // Default to UTCget_display_offset
    $display_offset = 0;

    // Load pref from DB is cache is empty
    if ($_user)
	$display_tz = $this->get_display_timezone($_user);

    // Recompute offset each request in case DST kicked in
    if ($display_tz != "UTC" && isset($_COOKIE["tz_offset"]))
	$display_offset = intval($_COOKIE["tz_offset"]);

    return $display_offset;
}

/**
 * Retrieves a TikiDate object for converting to/from display/UTC timezones
 *
 * $user: the logged-in user
 * returns: reference to a TikiDate instance with the appropriate offsets
 */
function &get_date_converter($_user = false) {
    static $date_converter;

    if (!$date_converter) {
	$display_offset = $this->get_display_offset($_user);

	$date_converter = &new TikiDate($display_offset);
    }

    return $date_converter;
}

function get_long_date_format() {
    static $long_date_format = false;

    if (!$long_date_format)
	$long_date_format = $this->get_preference('long_date_format', '%A %d of %B, %Y');

    return $long_date_format;
}

function get_short_date_format() {
    static $short_date_format = false;

    if (!$short_date_format)
	$short_date_format = $this->get_preference('short_date_format', '%a %d of %b, %Y');

    return $short_date_format;
}

function get_long_time_format() {
    static $long_time_format = false;

    if (!$long_time_format)
	$long_time_format = $this->get_preference('long_time_format', '%H:%M:%S %Z');

    return $long_time_format;
}

function get_short_time_format() {
    static $short_time_format = false;

    if (!$short_time_format)
	$short_time_format = $this->get_preference('short_time_format', '%H:%M %Z');

    return $short_time_format;
}

function get_long_datetime_format() {
    static $long_datetime_format = false;

    if (!$long_datetime_format)
	$long_datetime_format = $this->get_long_date_format(). ' [' . $this->get_long_time_format(). ']';

    return $long_datetime_format;
}

function get_short_datetime_format() {
    static $short_datetime_format = false;

    if (!$short_datetime_format)
	$short_datetime_format = $this->get_short_date_format(). ' [' . $this->get_short_time_format(). ']';

    return $short_datetime_format;
}

function server_time_to_site_time($timestamp, $user = false) {
    $date = new Date($timestamp);

    $date->setTZbyID($this->get_server_timezone());
    $date->convertTZbyID($this->get_display_timezone($user));
    return $date->getTime();
}

/**

 */
function get_site_date($timestamp, $user = false) {
    static $localed = false;

    if (!$localed) {
	$this->set_locale($user);

	$localed = true;
    }

    $original_tz = date('T', $timestamp);

    $format = '%b %e, %Y';
    $rv = strftime($format, $timestamp);
    $rv .= " =timestamp\n";
    $rv .= strftime('%Z', $timestamp);
    $rv .= " =strftime('%Z')\n";
    $rv .= date('T', $timestamp);
    $rv .= " =date('T')\n";

    $date = &new Date($timestamp);

# Calling new Date() changes the timezone of the $timestamp var!
# so we only change the timezone to UTC if the original TZ wasn't UTC
# to begin with.
# This seems really buggy, but I don't have time to delve into right now.
    $rv .= date('T', $timestamp);
    $rv .= " =date('T')\n";

    $rv .= $date->format($format);
    $rv .= " =new Date()\n";

    $rv .= date('T', $timestamp);
    $rv .= " =date('T')\n";

    if ($original_tz == 'UTC') {
	$date->setTZbyID('UTC');

	$rv .= $date->format($format);
	$rv .= " =setTZbyID('UTC')\n";
    }

    $tz_id = $this->get_display_timezone($user);

    if ($date->tz->getID() != $tz_id) {
# let's convert to the displayed timezone
	$date->convertTZbyID($tz_id);

	$rv .= $date->format($format);
	$rv .= " =convertTZbyID($tz_id)\n";
    }

#return $rv;

# if ($format == "%b %e, %Y")
#   $format = $tikilib->get_short_date_format();
    return $date;
}

# TODO rename to server_time_to_site_time()
function get_site_time($timestamp, $user = false) {
#print "<pre>get_site_time()</pre>";
    $date = $this->get_site_date($timestamp, $user);

    return $date->getTime();
}

function date_format($format, $timestamp, $user = false) {
    //$date = $this->get_site_date($timestamp, $user);
    // JJ - ignore conversion - we have no idea what TZ they're using

    // strftime doesn't do translations correctly
    // return strftime($format,$timestamp);
    $date = new Date($timestamp);

    return $date->format($format);
}

function get_long_date($timestamp, $user = false) {
    return $this->date_format($this->get_long_date_format(), $timestamp, $user);
}

function get_short_date($timestamp, $user = false) {
    return $this->date_format($this->get_short_date_format(), $timestamp, $user);
}

function get_long_time($timestamp, $user = false) {
    return $this->date_format($this->get_long_time_format(), $timestamp, $user);
}

function get_short_time($timestamp, $user = false) {
    return $this->date_format($this->get_short_time_format(), $timestamp, $user);
}

function get_long_datetime($timestamp, $user = false) {
    return $this->date_format($this->get_long_datetime_format(), $timestamp, $user);
}

function get_short_datetime($timestamp, $user = false) {
    return $this->date_format($this->get_short_datetime_format(), $timestamp, $user);
}

function get_site_timezone_shortname($user = false) {
    // UTC, or blank for local
    $dc = &$this->get_date_converter($user);

    return $dc->getTzName();
}

function get_server_timezone_shortname($user = false) {
    // Site time is always UTC, from the user's perspective.
    return "UTC";
}

/**
  get_site_time_difference - Return the number of seconds needed to add to a
  'system' time to return a 'site' time.
 */
function get_site_time_difference($user = false) {
    $dc = &$this->get_date_converter($user);

    $display_offset = $dc->display_offset;
    $server_offset = $dc->server_offset;
    return $display_offset - $server_offset;
}

/**
  Timezone saavy replacement for mktime()
 */
function make_time($hour, $minute, $second, $month, $day, $year, $timezone_id = false) {
    global $user; # ugh!

	if ($year <= 69)
	    $year += 2000;

    if ($year <= 99)
	$year += 1900;

    $date = new Date();
    $date->setHour($hour);
    $date->setMinute($minute);
    $date->setSecond($second);
    $date->setMonth($month);
    $date->setDay($day);
    $date->setYear($year);

#$rv = sprintf("make_time(): $date->format(%D %T %Z)=%s<br/>\n", $date->format('%D %T %Z'));
#print "<pre> make_time() start";
#print_r($date);
    if ($timezone_id)
	$date->setTZbyID($timezone_id);

#print_r($date);
#$rv .= sprintf("make_time(): $date->format(%D %T %Z)=%s<br/>\n", $date->format('%D %T %Z'));
#print $rv;
    return $date->getTime();
}

/**
  Timezone saavy replacement for mktime()
 */
function make_server_time($hour, $minute, $second, $month, $day, $year, $timezone_id = false) {
    global $user; # ugh!

	if ($year <= 69)
	    $year += 2000;

    if ($year <= 99)
	$year += 1900;

    $date = new Date();
    $date->setHour($hour);
    $date->setMinute($minute);
    $date->setSecond($second);
    $date->setMonth($month);
    $date->setDay($day);
    $date->setYear($year);

#print "<pre> make_server_time() start\n";
#print_r($date);
    if ($timezone_id)
	$date->setTZbyID($timezone_id);

#print_r($date);
    $date->convertTZbyID($this->get_server_timezone());
#print_r($date);
#print "make_server_time() end\n</pre>";
    return $date->getTime();
}

/**
  Per http://www.w3.org/TR/NOTE-datetime
 */
function get_iso8601_datetime($timestamp, $user = false) {
    return $this->date_format('%Y-%m-%dT%H:%M:%S%O', $timestamp, $user);
}

    function get_rfc2822_datetime($timestamp = false, $user = false) {
	if (!$timestamp)
	    $timestamp = time();

# rfc2822 requires dates to be en formatted
	$saved_locale = @setlocale(0);
	@setlocale ('en_US');
#was return date('D, j M Y H:i:s ', $time) . $this->timezone_offset($time, 'no colon');
	$rv = $this->date_format('%a, %e %b %Y %H:%M:%S', $timestamp, $user). $this->get_rfc2822_timezone_offset($timestamp, $user);

# switch back to the 'saved' locale
	if ($saved_locale)
	    @setlocale ($saved_locale);

	return $rv;
    }

    function get_rfc2822_timezone_offset($time = false, $no_colon = false, $user = false) {
	if ($time === false)
	    $time = time();

	$secs = $this->date_format('%Z', $time, $user);

	if ($secs < 0) {
	    $sign = '-';

	    $secs = -$secs;
	} else {
	    $sign = '+';
	}

	$colon = $no_colon ? '' : ':';
	$mins = intval(($secs + 30) / 60);

	return sprintf("%s%02d%s%02d", $sign, $mins / 60, $colon, $mins % 60);
    }

function list_languages($path = false) {
    $languages = array();

    if (!$path)
	$path = "lang";

    if (!is_dir($path))
	return array();

    $h = opendir($path);

    while ($file = readdir($h)) {
	if ($file != '.' && $file != '..' && is_dir("$path/$file") && strlen($file) == 2) {
	    $languages[] = $file;
	}
    }

    closedir ($h);

    // Format and return the list
    return $this->format_language_list($languages);
}

// Comparison function used to sort languages by their name in the
// current locale.
function formatted_language_compare($a, $b) {
    return strcmp($a['name'], $b['name']);
}

// Returns a list of languages formatted as a twodimensionel array
// with 'value' being the language code and 'name' being the name of
// the language.
function format_language_list($languages) {
    // The list of available languages so far with both English and
    // translated names.
          include_once("lang/langmapping.php");
	    $formatted = array();

	    // run through all the language codes:
	    foreach ($languages as $lc) {
		if (isset($langmapping[$lc])) {
		    // known language
		    if ($langmapping[$lc][0] == $langmapping[$lc][1]) {
			// Skip repeated text, 'English (English, en)' looks silly.
			$formatted[] = array(
				'value' => $lc,
				'name' => $langmapping[$lc][0] . " ($lc)"
				);
		    } else {
			$formatted[] = array(
				'value' => $lc,
				'name' => $langmapping[$lc][1] . ' (' . $langmapping[$lc][0] . ', ' . $lc . ')'
				    );
				}
				} else {
				// unknown language
				$formatted[] = array(
				    'value' => $lc,
				    'name' => tra("Unknown language"). " ($lc)"
				    );
				}
				}

				// Sort the languages by their name in the current locale
				usort($formatted, 'formatted_language_compare');
				return $formatted;
				}

				function get_language($user = false) {
				static $language = false;

				if (!$language) {
				    if ($user) {
					$language = $this->get_user_preference($user, 'language', 'en');

					if (!$language || $language == 'default')
					    $language = $this->get_preference('language', 'en');
				    } else
					$language = $this->get_preference('language', 'en');
				}

				return $language;
				}

function get_locale($user = false) {
# TODO move to admin preferences screen
    static $locales = array(
	    'de' => 'de_DE',
	    'dk' => 'da_DK',
	    'en' => 'en_US',
	    'fr' => 'fr_FR',
	    'he' => 'he_IL', # hebrew
	    'it' => 'it_IT', # italian
	    'pl' => 'pl_PL', # polish
	    'po' => 'po',
	    'ru' => 'ru_RU',
	    'sp' => 'es_ES',
	    'sw' => 'sw_SW', # swahili
	    'tw' => 'tw_TW',
	    );

    if (!isset($locale) or !$locale) {
	if (isset($locales[$this->get_language($user)]))
	    $locale = $locales[$this->get_language($user)];
#print "<pre>get_locale(): locale=$locale\n</pre>";
    }

    return $locale;
}

function set_locale($user = false) {
    static $locale = false;

    if (!$locale) {
# breaks the RFC 2822 code
	$locale = @setlocale(LC_TIME, $this->get_locale($user));
#print "<pre>set_locale(): locale=$locale\n</pre>";
    }

    return $locale;
}
} //end of class

function compare_links($ar1, $ar2) {
    return $ar1["links"] - $ar2["links"];
}

function compare_backlinks($ar1, $ar2) {
    return $ar1["backlinks"] - $ar2["backlinks"];
}

function r_compare_links($ar1, $ar2) {
    return $ar2["links"] - $ar1["links"];
}

function r_compare_backlinks($ar1, $ar2) {
    return $ar2["backlinks"] - $ar1["backlinks"];
}

function compare_images($ar1, $ar2) {
    return $ar1["images"] - $ar2["images"];
}

function r_compare_images($ar1, $ar2) {
    return $ar2["images"] - $ar1["images"];
}

function compare_files($ar1, $ar2) {
    return $ar1["files"] - $ar2["files"];
}

function r_compare_files($ar1, $ar2) {
    return $ar2["files"] - $ar1["files"];
}

function compare_versions($ar1, $ar2) {
    return $ar1["versions"] - $ar2["versions"];
}

function r_compare_versions($ar1, $ar2) {
    return $ar2["versions"] - $ar1["versions"];
}

function compare_changed($ar1, $ar2) {
    return $ar1["lastChanged"] - $ar2["lastChanged"];
}

function r_compare_changed($ar1, $ar2) {
    return $ar2["lastChanged"] - $ar1["lastChanged"];
}

function chkgd2() {
    if (!isset($_SESSION['havegd2'])) {
#   TODO test this logic in PHP 4.3
#   if (version_compare(phpversion(), "4.3.0") >= 0) {
#	 $_SESSION['havegd2'] = true;
#   } else {
    ob_start();

    phpinfo (INFO_MODULES);
    $_SESSION['havegd2'] = preg_match('/GD Version.*2.0/', ob_get_contents());
    ob_end_clean();
#	}
    }

    return $_SESSION['havegd2'];
}

function httpScheme() {
    return 'http' . ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 's' : '');
}

function httpPrefix() {
    /*
       if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
       $rv = 'https://' . $_SERVER['HTTP_HOST'];

       if ($_SERVER['SERVER_PORT'] != 443)
       $rv .= ':' . $_SERVER['SERVER_PORT'];
       } else {
       $rv = 'http://' . $_SERVER['HTTP_HOST'];

       if ($_SERVER['SERVER_PORT'] != 80)
       $rv .= ':' . $_SERVER['SERVER_PORT'];
       }

       return $rv;
     */
    /* Warning by zaufi: as far as I saw in my apache 1.3.27
     * there is no need to add port if it is non default --
     * $_SERVER['HTTP_HOST'] already contain it ...
     */
    return 'http'.((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 's' : '').'://'.$_SERVER['HTTP_HOST'];
}

if (!function_exists('file_get_contents')) {
    function file_get_contents($f) {
	ob_start();

	$retval = @readfile($f);

	if (false !== $retval) { // no readfile error
	    $retval = ob_get_contents();
	}

	ob_end_clean();
	return $retval;
    }

}

?>
