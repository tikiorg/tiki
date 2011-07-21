<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (!defined('ROLE_ORGANIZER')) define('ROLE_ORGANIZER', '6');
if (!defined('weekInSeconds')) define('weekInSeconds', 604800);

class CalendarLib extends TikiLib
{
	function convertSortMode($sort_mode) {
		$tmp = explode("_",$sort_mode);
		if (count($tmp) == 2) {
			if ($tmp[0] == "categoryName" || $tmp[0] == "locationName")
				return "name " . $tmp[1];
		}
		return parent::convertSortMode($sort_mode);
	}

	function list_calendars($offset = 0, $maxRecords = -1, $sort_mode = 'name_asc', $find = '') {
		$mid = '';
		$res = array();
		$bindvars = array();
		if ($find) {
			$mid = "and tcal.`name` like ?";
			$bindvars[] = '%'.$find.'%';
		}
	
		global $categlib; require_once( 'lib/categories/categlib.php' );
		
		$join = '';
		if( $jail = $categlib->get_jail() ) {
			$categlib->getSqlJoin($jail, 'calendar', 'tcal.`calendarId`', $join, $mid, $bindvars);
		}	

		$query = "select * from `tiki_calendars` as tcal $join where 1=1 $mid order by tcal.".$this->convertSortMode($sort_mode);
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$query_cant = "select count(*) from `tiki_calendars` as tcal $join where 1=1 $mid";
		$cant = $this->getOne($query_cant,$bindvars);

		$res = array();
		while ($r = $result->fetchRow()) {
			$k = $r["calendarId"];
			$res2 = $this->query("select `optionName`,`value` from `tiki_calendar_options` where `calendarId`=?",array((int)$k));
			while ($r2 = $res2->fetchRow()) {
				$r[$r2['optionName']] = $r2['value'];
			}
			$res["$k"] = $r;
		}
		$retval["data"] = $res;
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_calendarId_from_name($name) {
		$query = 'select `calendarId` from `tiki_calendars` where `name`=?';
		return $this->getOne($query, array($name));
	}

	function set_calendar($calendarId, $user, $name, $description, $customflags=array(),$options=array()) {
		global $prefs;
		$name = strip_tags($name);
		$description = strip_tags($description);
		$now = time();
		if ($calendarId > 0) {
			// modification of a calendar
			$query = "update `tiki_calendars` set `name`=?, `user`=?, `description`=?, ";
			$bindvars = array($name,$user,$description);
			foreach ($customflags as $k => $v) {
				$query .= "`$k`=?, ";
				$bindvars[]=$v;
			}
			$query .= "`lastmodif`=?  where `calendarId`=?";
			$bindvars[] = $now;
			$bindvars[] = $calendarId;
			$result = $this->query($query,$bindvars);
		} else {
			// create a new calendar
			$query = 'insert into `tiki_calendars` (`name`,`user`,`description`,`created`,`lastmodif`';
			$bindvars = array($name,$user,$description,$now,$now);
			if (!empty($customflags)) {
				$query .= ',`' . implode("`,`", array_keys($customflags)). '`';
			}
			$query .= ') values (?,?,?,?,?';
			if (!empty($customflags)) {
				$query .= ',' . implode(",", array_fill(0,count($customflags),"?"));
				foreach ($customflags as $k => $v)
					$bindvars[] = $v;
			}
			$query .= ')';
			$result = $this->query($query,$bindvars);
			$calendarId = $this->GetOne("select `calendarId` from `tiki_calendars` where `created`=?",array($now));
		}
		$this->query('delete from `tiki_calendar_options` where `calendarId`=?',array((int)$calendarId));
		if (count($options)) {
			if ( isset($options['viewdays']) ) {
				$options['viewdays'] = serialize($options['viewdays']);
			} else {
				$options['viewdays'] = serialize($prefs['calendar_view_days']);
			}
			foreach ($options as $name=>$value) {
				$name = preg_replace('/[^-_a-zA-Z0-9]/','',$name);
				$this->query('insert into `tiki_calendar_options` (`calendarId`,`optionName`,`value`) values (?,?,?)',array((int)$calendarId,$name,$value));
			}
		}
		return $calendarId;
	}

	function get_calendar($calendarId) {
		global $prefs;
		$res = $this->query("select * from `tiki_calendars` where `calendarId`=?",array((int)$calendarId));
		$cal = $res->fetchRow();
		$res2 = $this->query("select `optionName`,`value` from `tiki_calendar_options` where `calendarId`=?",array((int)$calendarId));
		while ($r = $res2->fetchRow()) {
			$cal[$r['optionName']] = $r['value'];
		}
		if (!isset($cal['startday']) and !isset($cal['endday'])) {
			$cal['startday'] = 0;
			$cal['endday'] = 23*60*60;
		}
		if ( isset($cal['viewdays']) ) {
			$cal['viewdays'] = unserialize($cal['viewdays']);
		} else {
			$cal['viewdays'] = $prefs['calendar_view_days'];
		}
		return $cal;
	}

	function get_calendarid($calitemId) {
		return $this->getOne("select `calendarId` from `tiki_calendar_items` where `calitemId`=?",array((int)$calitemId));
	}

	function drop_calendar($calendarId) {
		// find and remove roles for all calendar items:
		$query = "select `calitemId` from `tiki_calendar_items` where `calendarId`=?";
		$result = $this->query($query, array( $calendarId ) );
		$allItemsFromCalendar = array();
		while ($res = $result->fetchRow()) {
			$allItemsFromCalendar[] = $res['calitemId'];
		}
		if (count($allItemsFromCalendar) > 0) {
			$query = "delete from `tiki_calendar_roles` where `calitemId` in (".implode(',', array_fill(0,count($allItemsFromCalendar),'?')).")";
			$this->query($query,array($allItemsFromCalendar));
		}
		// remove calendar items, categories and locations:
		$query = "delete from `tiki_calendar_items` where `calendarId`=?";
		$this->query($query,array($calendarId));
		$query = "delete from `tiki_calendar_categories` where `calendarId`=?";
		$this->query($query,array($calendarId));
		$query = "delete from `tiki_calendar_options` where `calendarId`=?";
		$this->query($query,array($calendarId));
		$query = "delete from `tiki_calendar_locations` where `calendarId`=?";
		$this->query($query,array($calendarId));
		// uncategorize calendar
		global $categlib; require_once('lib/categories/categlib.php');
		$categlib->uncategorize_object('calendar', $calendarId);
		// now remove the calendar itself:
		$query = "delete from `tiki_calendars` where `calendarId`=?";
		$this->query($query,array($calendarId));
	}

	/* tsart ans tstop are in user time - the data base is in server time */
	function list_raw_items($calIds, $user, $tstart, $tstop, $offset, $maxRecords, $sort_mode='start_asc', $find='', $customs=array()) {

		if (count($calIds) == 0) {
		    return array();
		}

		$where = array();
		$bindvars=array();
		foreach ($calIds as $calendarId) {
		    $where[] = "i.`calendarId`=?";
		    $bindvars[] = (int)$calendarId;
		}

		$cond = "(" . implode(" or ", $where). ") and ";
		$cond .= " ((i.`start` > ? and i.`end` < ?) or (i.`start` < ? and i.`end` > ?))";

		$bindvars[] = (int)$tstart;
		$bindvars[] = (int)$tstop;
		$bindvars[] = (int)$tstop;
		$bindvars[] = (int)$tstart;

		$cond .= " and ((c.`personal`='y' and i.`user`=?) or c.`personal` != 'y')";
		$bindvars[] = $user;

		$query = "select i.`calitemId` as `calitemId` ";
		$queryCompl = "";
		$joinCompl = "";
		$tblRef = "i.";
		if (substr($sort_mode,0,12) == "categoryName") {
			$queryCompl = "`tiki_calendar_categories` as compl right join ";
			$joinCompl = " on i.categoryId = compl.calcatid ";
			$tblRef = "compl.";
		} elseif (substr($sort_mode,0,12) == "locationName") {
			$queryCompl = "`tiki_calendar_locations` as compl right join ";
			$joinCompl = " on i.locationId = compl.callocid ";
			$tblRef = "compl.";
		}
		$query .= "from " . $queryCompl . "`tiki_calendar_items` as i ".$joinCompl." left join `tiki_calendars` as c on i.`calendarId`=c.`calendarId` where ($cond)  order by ". $tblRef . $this->convertSortMode("$sort_mode");
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $this->get_item($res["calitemId"], $customs);
		}
		return $ret;
	}

	function list_items($calIds, $user, $tstart, $tstop, $offset, $maxRecords, $sort_mode='start_asc', $find='', $customs=array()) {
		global $tiki_p_change_events, $prefs;
		$ret = array();
		$list = $this->list_raw_items($calIds, $user, $tstart, $tstop, $offset, $maxRecords, $sort_mode, $find, $customs);
		foreach ($list as $res) {
			$mloop = TikiLib::date_format("%m", $res['start']);
			$dloop = TikiLib::date_format("%d", $res['start']);
			$yloop = TikiLib::date_format("%Y", $res['start']);
			$dstart = TikiLib::make_time(0, 0, 0, $mloop, $dloop, $yloop);
			$dend = TikiLib::make_time(0, 0, 0, TikiLib::date_format("%m", $res['end']), TikiLib::date_format("%d", $res['end']), TikiLib::date_format("%Y", $res['end']));
			$tstart = TikiLib::date_format("%H%M", $res["start"]);
			$tend = TikiLib::date_format("%H%M", $res["end"]);
			for ($i = $dstart; $i <= $dend; $i = TikiLib::make_time(0, 0, 0, $mloop, ++$dloop, $yloop)) {
				/* $head is in user time */
				if ($dstart == $dend) {
					$head = TikiLib::date_format($prefs['short_time_format'], $res["start"]). " - " . TikiLib::date_format($prefs['short_time_format'], $res["end"]);
				} elseif ($i == $dstart) {
					$head = TikiLib::date_format($prefs['short_time_format'], $res["start"]). " ...";
				} elseif ($i == $dend) {
					$head = " ... " . TikiLib::date_format($prefs['short_time_format'], $res["end"]);
				} else {
					$head = " ... " . tra("continued"). " ... ";
				}

				/* $i is timestamp unix of the beginning of a day */
				$ret["$i"][] = array(
					"result" => $res,
					"calitemId" => $res["calitemId"],
					"calname" => $res["calname"],
					"time" => $tstart, /* user time */
					"end" => $tend, /* user time */
					"type" => $res["status"],
					"web" => $res["url"],
					"startTimeStamp" => $res["start"],
					"endTimeStamp" => $res["end"],
					"nl" => $res["nlId"],
					"prio" => $res["priority"],
					"location" => $res["locationName"],
					"category" => $res["categoryName"],
					"name" => $res["name"],
					"head" => $head,
					"parsedDescription" => $this->parse_data($res["description"]),
					"description" => str_replace("\n|\r", "", $res["description"]),
					"calendarId" => $res['calendarId'],
					"status" => $res['status']
				);
			}
		}
		return $ret;
	}

	function list_items_by_day($calIds, $user, $tstart, $tstop, $offset, $maxRecords, $sort_mode='start_asc', $find='', $customs=array()) {
		global $prefs;
		$ret = array();
		$list = $this->list_raw_items($calIds, $user, $tstart, $tstop, $offset, $maxRecords, $sort_mode, $find, $customs);
		foreach ($list as $res) {
			$mloop = TikiLib::date_format("%m", $res['start']);
			$dloop = TikiLib::date_format("%d", $res['start']);
			$yloop = TikiLib::date_format("%Y", $res['start']);
			$dstart = TikiLib::make_time(0, 0, 0, $mloop, $dloop, $yloop);
			$dend = TikiLib::make_time(0, 0, 0, TikiLib::date_format("%m", $res['end']), TikiLib::date_format("%d", $res['end']), TikiLib::date_format("%Y", $res['end']));
			$tstart = TikiLib::date_format("%H%M", $res["start"]);
			$tend = TikiLib::date_format("%H%M", $res["end"]);
			for ( $i = $dstart; $i <= $dend; $i = TikiLib::make_time(0, 0, 0, $mloop, ++$dloop, $yloop) ) {
				/* $head is in user time */
				if ($res['allday'] == '1') {
					$head = tra('All day');
				} elseif ($dstart == $dend) {
					$head = TikiLib::date_format($prefs['short_time_format'], $res["start"]). " - " . TikiLib::date_format($prefs['short_time_format'], $res["end"]);
				} elseif ($i == $dstart) {
					$head = TikiLib::date_format($prefs['short_time_format'], $res["start"]). " ...";
				} elseif ($i == $dend) {
					$head = " ... " . TikiLib::date_format($prefs['short_time_format'], $res["end"]);
				} else {
					$head = " ... " . tra("continued"). " ... ";
				}

				/* $i is timestamp unix of the beginning of a day */
				$j = (isset($ret[$i]) && is_array($ret[$i])) ? count($ret[$i]) : 0;

				$ret[$i][$j] = $res;
				$ret[$i][$j]['head'] = $head;
				$ret[$i][$j]['parsedDescription'] = $this->parse_data($res["description"]);
				$ret[$i][$j]['description'] = str_replace("\n|\r", "", $res["description"]);
				$ret[$i][$j]['visible'] = 'y';
				$ret[$i][$j]['where'] = $res['locationName'];
						
				$ret[$i][$j]['show_description'] = 'y';
				/*	'time' => $tstart, /* user time */
				/*	'end' => $tend, /* user time */

				$ret[$i][$j]['group_description'] = htmlspecialchars($res['name']) . '<span class="calgrouptime">, '.$head.'</span>';
			}
		}
		return $ret;
	}

	function get_item($calitemId, $customs=array()) {
		global $user;

		$query = "select i.`calitemId` as `calitemId`, i.`calendarId` as `calendarId`, i.`user` as `user`, i.`start` as `start`, i.`end` as `end`, t.`name` as `calname`, ";
		$query.= "i.`locationId` as `locationId`, l.`name` as `locationName`, i.`categoryId` as `categoryId`, c.`name` as `categoryName`, i.`priority` as `priority`, i.`nlId` as `nlId`, ";
		$query.= "i.`status` as `status`, i.`url` as `url`, i.`lang` as `lang`, i.`name` as `name`, i.`description` as `description`, i.`created` as `created`, i.`lastmodif` as `lastModif`, i.`allday` as `allday`, i.`recurrenceId` as `recurrenceId`, ";
		$query.= "t.`customlocations` as `customlocations`, t.`customcategories` as `customcategories`, t.`customlanguages` as `customlanguages`, t.`custompriorities` as `custompriorities`, ";
		$query.= "t.`customsubscription` as `customsubscription`, ";
		$query.= "t.`customparticipants` as `customparticipants` ";

		foreach($customs as $k=>$v)
		    $query.=", i.`$k` as `$v`";

		$query.= "from `tiki_calendar_items` as i left join `tiki_calendar_locations` as l on i.`locationId`=l.`callocId` ";
		$query.= "left join `tiki_calendar_categories` as c on i.`categoryId`=c.`calcatId` left join `tiki_calendars` as t on i.`calendarId`=t.`calendarId` where `calitemId`=?";
		$result = $this->query($query,array((int)$calitemId));
		$res = $result->fetchRow();
		$query = "select `username`, `role` from `tiki_calendar_roles` where `calitemId`=? order by `role`";
		$rezult = $this->query($query,array((int)$calitemId));
		$ppl = array();
		$org = array();

		while ($rez = $rezult->fetchRow()) {
			if ($rez["role"] == ROLE_ORGANIZER) {
				$org[] = $rez["username"];
			} elseif ($rez["username"]) {
				$ppl[] = array('name'=>$rez["username"],'role'=>$rez["role"]);
			}
		}

		$res["participants"] = $ppl;
		$res["organizers"] = $org;
		
		$res['date_start'] = (int)$res['start'];
		$res['date_end'] = (int)$res['end'];
		
		$res['duration'] = $res['end'] - $res['start'];
		$res['parsed'] = $this->parse_data($res['description']);
		$res['parsedName'] = $this->parse_data($res['name']);
		return $res;
	}

	function set_item($user, $calitemId, $data, $customs=array()) {
		global $prefs;
		if (!isset($data['calendarId'])) {
			return false;
		}
		$caldata = $this->get_calendar($data['calendarId']);

		if ($caldata['customlocations'] == 'y') {
			if (!$data["locationId"] and !$data["newloc"]) {
				$data['locationId'] = 0;
			}
			if (trim($data["newloc"])) {
				$bindvars=array((int)$data["calendarId"],trim($data["newloc"]));
				$query = "delete from `tiki_calendar_locations` where `calendarId`=? and `name`=?";
				$this->query($query,$bindvars,-1,-1,false);
				$query = "insert into `tiki_calendar_locations` (`calendarId`,`name`) values (?,?)";
				$this->query($query,$bindvars);
				$data["locationId"] = $this->getOne("select `callocId` from `tiki_calendar_locations` where `calendarId`=? and `name`=?",$bindvars);
			}
		} else {
			$data['locationId'] = 0;
		}

		if ($caldata['customcategories'] == 'y') {
			if (!$data["categoryId"] and !$data["newcat"]) {
				$data['categoryId'] = 0;
			}
			if (trim($data["newcat"])) {
				$query = "delete from `tiki_calendar_categories` where `calendarId`=? and `name`=?";
				$bindvars=array((int)$data["calendarId"],trim($data["newcat"]));
				$this->query($query,$bindvars,-1,-1,false);
				$query = "insert into `tiki_calendar_categories` (`calendarId`,`name`) values (?,?)";
				$this->query($query,$bindvars);
				$data["categoryId"] = $this->getOne("select `calcatId` from `tiki_calendar_categories` where `calendarId`=? and `name`=?",$bindvars);
			}
		} else {
			$data['categoryId'] = 0;
		}

		if ($caldata['customparticipants'] == 'y') {
			$roles = array();
			if ($data["organizers"]) {
				$orgs = explode(',', $data["organizers"]);
				foreach ($orgs as $o) {
					if (trim($o)) {
						$roles[ROLE_ORGANIZER][] = trim($o);
					}
				}
			}
			if ($data["participants"]) {
				$parts = explode(',', $data["participants"]);
				foreach ($parts as $pa) {
					if (trim($pa)) {
						if (strstr($pa,':')) {
							$p = explode(':', trim($pa));
							$roles["$p[0]"][] = trim($p[1]);
						} else {
							$roles[0][] = trim($pa);
						}
					}
				}
			}
		}

		if ($caldata['customlanguages'] == 'y') {
			if (!isset($data['lang'])) {
				$data['lang'] = '';
			}
		} else {
			$data['lang'] = '';
		}

		if ($caldata['custompriorities'] == 'y') {
			if (!isset($data['priority'])) {
				$data['priority'] = 0;
			}
		} else {
			$data['priority'] = 0;
		}

		if ($caldata['customsubscription'] == 'y') {
			if (!isset($data['nlId'])) {
				$data['nlId'] = 0;
			}
		} else {
			$data['nlId'] = 0;
		}

		if (!isset($data['recurrenceId']) || !($data['recurrenceId'] > 0))
			$data['recurrenceId'] = null;

		$data['user']=$user;

		$realcolumns=array('calitemId', 'calendarId', 'start', 'end', 'locationId', 'categoryId', 'nlId','priority',
				   'status', 'url', 'lang', 'name', 'description', 'user', 'created', 'lastmodif', 'allday','recurrenceId','changed');
		foreach($customs as $custom) $realcolumns[]=$custom;

		if ($calitemId) {
			$new = false;
			$data['lastmodif']=$this->now;

			$l=array();
			$r=array();

			foreach($data as $k=>$v) {
			    if (!in_array($k, $realcolumns)) continue;
			    $l[]="`$k`=?";
			    $r[]=$v;
			}

			$query='UPDATE `tiki_calendar_items` SET '.implode(',', $l).' WHERE `calitemId`=?';
			$r[]=(int)$calitemId;

			$result = $this->query($query,$r);
		} else {
			$new = true;
			$data['lastmodif']=$this->now;
			$data['created']=$this->now;

			$l=array();
			$r=array();
			$z=array();

			foreach($data as $k=>$v) {
			    if (!in_array($k, $realcolumns)) continue;
			    $l[]="`$k`";
			    $z[]='?';
			    $r[]=($k=='priority')?(string)$v:$v;
			}

			$query = 'INSERT INTO `tiki_calendar_items` ('.implode(',', $l).') VALUES ('.implode(',', $z).')';
			$result = $this->query($query, $r);
			$calitemId = $this->GetOne("select `calitemId` from `tiki_calendar_items` where `calendarId`=? and `created`=?",array($data["calendarId"],$this->now));
		}

		if ($calitemId) {
			$query = "delete from `tiki_calendar_roles` where `calitemId`=?";
			$this->query($query,array((int)$calitemId));
		}

		foreach ($roles as $lvl=>$ro) {
			foreach ($ro as $r) {
				$query = "insert into `tiki_calendar_roles` (`calitemId`,`username`,`role`) values (?,?,?)";
				$this->query($query,array((int)$calitemId,$r,(string)$lvl));
			}
		}

		if ($prefs['feature_user_watches'] == 'y') {
			$this->watch($calitemId, $data);
		}

		return $calitemId;
	}

	function watch($calitemId, $data) {
		global $tikilib, $smarty, $prefs;
		if ($nots = $tikilib->get_event_watches('calendar_changed', $data['calendarId'])) {
			include_once('lib/webmail/tikimaillib.php');
			$mail = new TikiMail();
			$smarty->assign('mail_new', $new);
			$smarty->assign('mail_data', $data);
			$smarty->assign('mail_calitemId', $calitemId);
			$foo = parse_url($_SERVER["REQUEST_URI"]);
			$machine = $tikilib->httpPrefix( true ) . dirname( $foo["path"] );
			$machine = preg_replace("!/$!", "", $machine); // just incase
 			$smarty->assign('mail_machine', $machine);
			$defaultLanguage = $prefs['site_language'];
			foreach ($nots as $not) {
				$mail->setUser($not['user']);
				$mail_data = $smarty->fetchLang($defaultLanguage, "mail/user_watch_calendar_subject.tpl");
				$mail->setSubject($mail_data);
				$mail_data = $smarty->fetchLang($defaultLanguage, "mail/user_watch_calendar.tpl");
				$mail->setText($mail_data);
				$mail->buildMessage();
				$mail->send(array($not['email']));
			}
		}
	}

	function drop_item($user, $calitemId) {
		if ($calitemId) {
			$query = "delete from `tiki_calendar_items` where `calitemId`=?";
			$this->query($query,array($calitemId));
		}
	}

	function list_locations($calendarId) {
		$res = array();
		if ($calendarId > 0) {
			$query = "select `callocId` as `locationId`, `name` from `tiki_calendar_locations` where `calendarId`=? order by `name`";
			return $this->fetchAll($query,array($calendarId));
		}
		return $res;
	}

	function list_categories($calendarId) {
		$res = array();
		if ($calendarId > 0) {
			$query = "select `calcatId` as `categoryId`, `name` from `tiki_calendar_categories` where `calendarId`=? order by `name`";
			return $this->fetchAll($query,array($calendarId));
		}
		return $res;
	}
	
	// Returns the last $maxrows of modified events for an
	// optional $calendarId
	function last_modif_events($maxrows = -1, $calendarId = 0) {
		
		if ($calendarId > 0) {
			$cond = "where `calendarId` = ? ";
			$bindvars = array($calendarId);
		} else {
			$cond = '';
			$bindvars = array();
		}
				
		$query = "select `start`, `name`, `calitemId`, `calendarId`, `user`, `lastModif` from `tiki_calendar_items` ".$cond."order by ".$this->convertSortMode('lastModif_desc');
	
		return $this->fetchAll($query,$bindvars,$maxrows,0);
	}
	
	function importCSV($fname, $calendarId) {
		global $user, $smarty;
		$fields = false;
		if ($fhandle = fopen($fname, 'r')) {
			$fields = fgetcsv($fhandle, 1000);
		}
		if ($fields === false || !array_search('name', $fields)) {
			$smarty->assign('msg', tra("The file is not a CSV file or has not a correct syntax"));
			$smarty->display("error.tpl");
			die;
		}
		$nb = 0;
		while (($data = fgetcsv($fhandle, 1000)) !== FALSE) {
			$d = array("calendarId"=>$calendarId, "calitemId"=>"0", "name"=>"", "description" =>"", "locationId"=>"", 
					   "organizers"=>"", "participants"=>"","status"=>"1","priority"=>"5","categoryId"=>"0","newloc"=>"0","newcat"=>"","nlId"=>"","lang"=>"", 'start'=>'', 'end'=>'');
			foreach ($fields as $field) {
				$d[$field] = $data[array_search($field, $fields)];
			}
			if (isset($d["subject"]) && empty($d["name"]))
				$d["name"] = $d["subject"];
			if (isset($d['start date'])) {
				if (isset($d['start time'])) {
					$d['start'] = strtotime($d['start time'], strtotime($d['start date']));
				} else {
					$d['start'] = strtotime($d['start date']);
				}
			}
			if (isset($d['end date'])) {
				if (isset($d['end time'])) {
					$d['end'] = strtotime($d['end time'], strtotime($d['end date']));
				} else {
					$d['end'] = strtotime($d['end date']);
				}
			}
// TODO do a replace if name, calendarId, start, end exists
			if (!empty($d['start']) && !empty($d['end'])) {		
				$this->set_item($user, 0, $d);
				++$nb;
			}
		}
		fclose ($fhandle);
		return $nb;
	}

	// Returns an array of a maximum of $maxrows upcoming (but possibly past) events in the given $order. If $calendarId is set, events not in the specified calendars are filtered. $calendarId can be a calendar identifier or an array of calendar identifiers. If $maxDaysEnd is a natural, events ending after $maxDaysEnd days are filtered. If $maxDaysStart is a natural, events starting after $maxDaysStart days are filtered. Events ending more than $priorDays in the past are filtered.
	// Each event is represented by a string-indexed array with indices start, end, name, description, calitemId, calendarId, user, lastModif, url, allday in the same format as tiki_calendar_items fields, as well as location for the event's locations, parsed for the parsed description and category for the event's calendar category.
	function upcoming_events($maxrows = -1, $calendarId = null, $maxDaysEnd = -1, $order = 'start_asc', $priorDays = 0, $maxDaysStart = -1) {
		$cond = '';
		$bindvars = array();
		if (isset($calendarId)) {
			if(is_array($calendarId)) {
				$cond = $cond."and (0=1";
				foreach($calendarId as $id) {
					$cond = $cond." or i.`calendarId` = ? ";
				}
				$cond = $cond.")";
				$bindvars = array_merge( $bindvars, $calendarId );
			} else {
				$cond = $cond." and i.`calendarId` = ? ";
				$bindvars[] = $calendarId;
			}
		}
		$cond .= " and `end` >= (unix_timestamp(now()) - ?*3600*34)";
		$bindvars[] = $priorDays;

		if($maxDaysEnd > 0)
		{
			$maxSeconds = ($maxDaysEnd * 24 * 60 * 60);
			$cond .= " and `end` <= (unix_timestamp(now())) +".$maxSeconds;
		}
		if ($maxDaysStart > 0)
		{
			$maxSeconds = ($maxDaysStart * 24 * 60 * 60);
			$cond .= " and `start` <= (unix_timestamp(now())) +".$maxSeconds;
		}
		$ljoin = "left join `tiki_calendar_locations` as l on i.`locationId`=l.`callocId` left join `tiki_calendar_categories` as c on i.`categoryId`=c.`calcatId`";
		$query = "select i.`start`, i.`end`, i.`name`, i.`description`, i.`status`, i.`calitemId`, i.`calendarId`, i.`user`, i.`lastModif`, i.`url`, l.`name` as location, i.`allday`, c.`name` as category from `tiki_calendar_items` i $ljoin where 1=1 ".$cond." order by ".$this->convertSortMode($order);
		$ret = $this->fetchAll($query,$bindvars,$maxrows,0);
			
		foreach ( $ret as &$res ) {
			$res['parsed'] = $this->parse_data($res['description']);
		}
	
		return $ret;
	}
	function cleanEvents($calendarId, $days) {
		global $tikilib;
		$mid[] = " `end` < ? ";
		$bindvars[] = $tikilib->now - $days*24*60*60;
		if ($calendarId > 0) {
			$mid[] = " `calendarId` = ? ";
			$bindvars[] = $calendarId;
		}
		$query = "delete from `tiki_calendar_items` where ".implode(' and ', $mid);
		$tikilib->query($query, $bindvars);
	}
	function firstDayofWeek($user) {
		global $prefs;
		if ($prefs['calendar_firstDayofWeek'] == 'user') {
			$firstDayofWeek = (int)tra('First day of week: Sunday (its ID is 0) - translators you need to localize this string!');
			if ( $firstDayofWeek < 1 || $firstDayofWeek > 6 ) {
				$firstDayofWeek = 0;
			}
		} else {
			$firstDayofWeek = $prefs['calendar_firstDayofWeek'];
		} 
	}
	// return detail on a date
	function infoDate($focusDate) {
		$focus = array (
			'day' => intval(TikiLib::date_format('%d', $focusDate)),
			'month' => intval(TikiLib::date_format('%m', $focusDate)),
			'year' => TikiLib::date_format('%Y', $focusDate),
			'date' => $focusDate,
			'weekDay' => TikiLib::date_format('%w', $focusDate) // in (0, 6)
		);
		$focus['daysInMonth'] = Date_Calc::daysInMonth($focus['month'], $focus['year']);
		return $focus;
	}
	// Compute the start date (the 1 first of the month of the focus date or the day) and the next start date from the period around a focus date
	function focusStartEnd($focus, $view='month', $beginMonth='y', &$start, &$startNext) {
		$nbMonths = array('month' => 1, 'bimester' => 2, 'trimester' => 3, 'quarter' => 4, 'semester' => 6, 'year' => 12);
		// start of the period
		$start = $focus;
		if ($beginMonth == 'y') {
			$start['day'] = 1;
		}
		$start['date'] =  TikiLib::make_time(0, 0, 0, $start['month'], $start['day'], $start['year']);
		$start['weekDay'] = TikiLib::date_format('%w', $start['date']); // in (0, 6)
		// start of the next period - just shift some months
		$startNext['date'] = TikiLib::make_time(0, 0, 0, $start['month'] + $nbMonths[$view], $start['day'], $start['year']);
		$startNext['day'] = TikiLib::date_format('%d', $startNext['date']);
		$startNext['month'] = TikiLib::date_format('%m', $startNext['date']);
		$startNext['year'] = TikiLib::date_format('%Y', $startNext['date']);
		$startNext['weekDay'] = TikiLib::date_format('%w', $startNext['date']);
	}
	// Compute the date just $view from the focus
	function focusPrevious($focus, $view='month') {
		$nbMonths = array('day' => 0, 'week' => 0, 'month' => 1, 'bimester' => 2, 'trimester' => 3, 'quarter' => 4, 'semester' => 6, 'year' => 12);
		$nbDays = array('day' => 1, 'week' => 7, 'month' => 0, 'bimester' => 0, 'trimester' => 0, 'quarter' => 0, 'semester' => 0, 'year' => 0);
		$previous = $focus;
		$previous['day'] -= $nbDays[$view];
		// $tikilib->make_time() used with timezones doesn't support month = 0
		if ($previous['month'] - $nbMonths[$view] <= 0) { // need to change year
			$previous['month'] = ($previous['month'] +11 - $nbMonths[$view]) % 12 + 1;
			$previous['year'] -= 1;
		} else {
			$previous['month'] -= $nbMonths[$view];
		}
		$previous['daysInMonth'] = Date_Calc::daysInMonth($previous['month'], $previous['year']);
		if ($previous['day'] > $previous['daysInMonth']) {
			$previous['day'] = $previous['daysInMonth'];
		}
		$previous['date'] =  Tikilib::make_time(0, 0, 0, $previous['month'], $previous['day'], $previous['year']);
		$previous = $this->infoDate($previous['date']); // get back real day, month, year
		return $previous;
	}
	// Compute the date just $view after the focus
	function focusNext($focus, $view='month') {
		$nbMonths = array('day' => 0, 'week' => 0, 'month' => 1, 'bimester' => 2, 'trimester' => 3, 'quarter' => 4, 'semester' => 6, 'year' => 12);
		$nbDays = array('day' => 1, 'week' => 7, 'month' => 0, 'bimester' => 0, 'trimester' => 0, 'quarter' => 0, 'semester' => 0, 'year' => 0);
		$next = $focus;
		$next['day'] += $nbDays[$view];
		if ($next['month'] + $nbMonths[$view] > 12) {
			$next['month'] = ($next['month'] -1 + $nbMonths[$view]) % 12 + 1;
			$next['year'] += 1;
		} else {
			$next['month'] += $nbMonths[$view];
		}
		$next['daysInMonth'] = Date_Calc::daysInMonth($next['month'], $next['year']);
		if ($next['day'] > $next['daysInMonth']) {
			$next['day'] = $next['daysInMonth'];
		}
		$next['date'] = Tikilib::make_time(0, 0, 0, $next['month'], $next['day'], $next['year']);
		$next = $this->infoDate($next['date']); // get back real day, month, year
		return $next;
	}
	// Compute a table view of dates (one line per week)
	// $firstWeekDay = 0 (Sunday), 1 (Monday)
	function getTableViewCells($start, $startNext, $view='month', $firstWeekDay = 0) {
		// start of the view
		$viewStart = $start;
		$nbBackDays = $start['weekDay'] < $firstWeekDay? 6: $start['weekDay'] - $firstWeekDay;
		if ($nbBackDays == 0) {
			$viewStart['daysInMonth'] = Date_Calc::daysInMonth($viewStart['month'], $viewStart['year']);
		} elseif ($start['day'] - $nbBackDays < 0) {
			$viewStart['month'] = $start['month'] == 1? 12: $start['month'] - 1;
			$viewStart['year'] = $start['month'] == 1? $start['year'] -1 : $start['year'];
			$viewStart['daysInMonth'] = Date_Calc::daysInMonth($viewStart['month'], $viewStart['year']);
			$viewStart['day'] = $viewStart['daysInMonth'] - $nbBackDays + 1;
			$viewStart['date'] = TikiLib::make_time(0, 0, 0, $viewStart['month'], $viewStart['day'], $viewStart['year']);
		} else {
			$viewStart['daysInMonth'] = Date_Calc::daysInMonth($viewStart['month'], $viewStart['year']);
			$viewStart['day'] = $viewStart['day'] - $nbBackDays;
			$viewStart['date'] = TikiLib::make_time(0, 0, 0, $viewStart['month'], $viewStart['day'], $viewStart['year']);
		}
		// echo '<br/>VIEWSTART'; print_r($viewStart);
		// end of the period
		$cell = array();

		for ($ilign = 0, $icol = 0, $loop = $viewStart, $weekDay = $viewStart['weekDay']; ; ) {
			if ($loop['date'] >= $startNext['date'] && $icol == 0) {
				break;
			}
			$cell[$ilign][$icol] = $loop;
			$cell[$ilign][$icol]['focus'] = $loop['date'] < $start['date'] || $loop['date'] >= $startNext['date']? false: true;
			$cell[$ilign][$icol]['weekDay'] = $weekDay;
			$weekDay = ($weekDay + 1) % 7;
			if ($icol >= 6) {
				++$ilign;
				$icol = 0;
			} else {
				++$icol;
			}
			if ($loop['day'] >= $loop['daysInMonth']) {
				$loop['day'] = 1;
				if ($loop['month'] == 12) {
					$loop['month'] = 1;
					$loop['year'] += 1;
				} else {
					$loop['month'] += 1;
				}
				$loop['daysInMonth'] = Date_Calc::daysInMonth($loop['month'], $loop['year']);
			} else {
				$loop['day'] = $loop['day'] + 1;
			}
			$loop['date'] = TikiLib::make_time(0, 0, 0, $loop['month'], $loop['day'], $loop['year']);
		}
		//echo '<pre>CELL'; print_r($cell); echo '</pre>';
		return $cell;
	}
	function getDayNames($firstDayofWeek = 0, &$daysnames, &$daysnames_abr) {
		$daysnames = array();
		$daysnames_abr = array();
		if ($firstDayofWeek == 0) {
			$daysnames[] = tra('Sunday');
			$daysnames_abr[] = tra('Su');
		}
		array_push($daysnames, 
			tra('Monday'),
			tra('Tuesday'),
			tra('Wednesday'),
			tra('Thursday'),
			tra('Friday'),
			tra('Saturday')
		);
		array_push($daysnames_abr, 
			tra('Mo'),
			tra('Tu'),
			tra('We'),
			tra('Th'),
			tra('Fr'),
			tra('Sa')
		);
		if ($firstDayofWeek != 0) {
			$daysnames[] = tra('Sunday');
			$daysnames_abr[] = tra('Su');
		}
	}

	/**
	 * Get calendar and its events
	 * 
	 * @param $calIds
	 * @param $viewstart
	 * @param $viewend
	 * @param $group_by
	 * @param $item_name
	 * @param bool $listmode if set to true populate listevents key of the returned array
	 * @return array
	 */
	function getCalendar($calIds, &$viewstart, &$viewend, $group_by = '', $item_name = 'events', $listmode = false) {
		global $user, $prefs, $smarty;

		// Global vars used by tiki-calendar_setup.php (this has to be changed)
		global $tikilib, $calendarViewMode, $request_day, $request_month,
$request_year, $dayend, $myurl;
		global $weekdays, $daysnames, $daysnames_abr;
		include('tiki-calendar_setup.php');

		//FIXME : maxrecords = 50
		$listtikievents = $this->list_items_by_day($calIds, $user, $viewstart, $viewend, 0, 50);

		$mloop = TikiLib::date_format('%m', $viewstart);
		$dloop = TikiLib::date_format('%d', $viewstart);
		$yloop = TikiLib::date_format('%Y', $viewstart);
		$curtikidate = new TikiDate();
		$display_tz = $tikilib->get_display_timezone();
		if ( $display_tz == '' ) $display_tz = 'UTC';
		$curtikidate->setTZbyID($display_tz);
		$curtikidate->setLocalTime($dloop,$mloop,$yloop,0,0,0,0);
		$listevents = array();
	
		// note that number of weeks starts at ZERO (i.e., zero = 1 week to display).
		for ($i = 0; $i <= $numberofweeks; $i++) {
			$weeks[] = $curtikidate->getWeekOfYear();
	
			foreach ( $weekdays as $w ) {
				$leday = array();
				if ( $group_by == 'day' ) {
					$key = 0;
				}
				if ( $calendarViewMode['casedefault'] == 'day' ) {
					$dday = $daystart;
				} else {
					$dday = $curtikidate->getTime();
					$curtikidate->addDays(1);
				}
				$cell[$i][$w]['day'] = $dday;
	
				if ( $calendarViewMode['casedefault'] == 'day' or ( $dday >= $daystart && $dday <= $dayend ) ) {
					$cell[$i][$w]['focus'] = true;
				} else {
					$cell[$i][$w]['focus'] = false;
				}
				if ( isset($listtikievents["$dday"]) ) {
					$e = -1;
	
					foreach ( $listtikievents["$dday"] as $lte ) {
						$lte['desc_name'] = $lte['name'];
						if ( $group_by_item != 'n' ) {
							if ( $group_by != 'day' ) $key = $lte['id'].'|'.$lte['type'];
							if ( ! isset($leday[$key]) ) {
								$leday[$key] = $lte;
								if ( $group_by == 'day' ) {
									$leday[$key]['description'] = array($lte['where'] => array($lte['group_description']));
									$leday[$key]['head'] = TikiLib::date_format($prefs['short_date_format'], $cell[$i][$w]['day']);
								} else {
									$leday[$key]['description'] = ' - <b>'.$lte['when'].'</b> : '.tra($lte['action']).' '.$lte['description'];
									$leday[$key]['head'] = $lte['name'].', <i>'.tra('in').' '.$lte['where'].'</i>';
								}
								$leday[$key]['desc_name'] = '';
							} else {
								$leday_item =& $leday[$key];
								$leday_item['user'] .= ', '.$lte['user'];
	
								if ( !isset($leday_item['action']) || !is_integer($leday_item['action']) ) {
									$leday_item['action'] = 1;
								}
								$leday_item['action']++;
	
								if ( $group_by == 'day' ) {
									$leday_item['name'] .= '<br />'.$lte['name'];
									$leday_item['desc_name'] = $leday_item['action'].' '.tra($item_name).': ';
									$leday_item['description'][$lte['where']][] = $lte['group_description'];
								} else {
									$leday_item['name'] = $lte['name'].' (x<b>'.$leday_item['action'].'</b>)';
									$leday_item['desc_name'] = $leday_item['action'].' '.tra($item_name);
									if ( $lte['show_description'] == 'y' && ! empty($lte['description']) ) {
										$leday_item['description'] .= ",\n<br /> - <b>".$lte['when'].'</b> : '.tra($lte['action']).' '.$lte['description'];
										$leday_item['show_description'] = 'y';
									}
								}
							}
						} else {
							$e++;
							$key = "{$lte['time']}$e";
							$leday[$key] = $lte;
							$lte['desc_name'] .= tra($lte['action']);
						}
					}
					foreach ( $leday as $key => $lte ) {
			
						if ( $group_by == 'day' ) {
							$desc = '';
							foreach ( $lte['description'] as $desc_where => $desc_items ) {
								$desc_items = array_unique($desc_items);
								foreach ( $desc_items as $desc_item ) {
									if ( $desc != '' ) $desc .= '<br />';
									$desc .= '- '.$desc_item;
									if (!empty($lte['show_location']) && $lte['show_location'] == 'y' && $desc_where != '' ) {
										$desc .= ' <i>['.$desc_where.']</i>';
									}
								}
							}
							$lte['description'] = $desc;
						}

						$smarty->assign('calendar_type', ( $myurl == 'tiki-action_calendar.php' ? 'tiki_actions' : 'calendar' ) );
						$smarty->assign_by_ref('item_url', $lte["url"]);
						$smarty->assign_by_ref('cellhead', $lte["head"]);
						$smarty->assign_by_ref('cellprio', $lte["prio"]);
						$smarty->assign_by_ref('cellcalname', $lte["calname"]);
						$smarty->assign('celllocation', "");
						$smarty->assign('cellcategory', "");
						$smarty->assign_by_ref('cellname', $lte["desc_name"]);
						$smarty->assign('cellid', "");
						$smarty->assign_by_ref('celldescription', $lte["description"]);
						$smarty->assign('show_description', $lte["show_description"]);
	
						if ( ! isset($leday[$key]["over"]) ) {
							$leday[$key]["over"] = '';
						} else {
							$leday[$key]["over"] .= "<br />\n";
						}
						$leday[$key]["over"] .= $smarty->fetch("tiki-calendar_box.tpl");
					}
				}
	
				if ( is_array($leday) ) {
					ksort ($leday);
					$cell[$i][$w]['items'] = array_values($leday);
				}
			}
		}

		if ((isset($_SESSION['CalendarViewList']) && $_SESSION['CalendarViewList'] == 'list') || $listmode) {
			if ( is_array($listtikievents) ) {
				foreach ( $listtikievents as $le ) {
					if ( is_array($le) ) {
						foreach ( $le as $e ) {
							$listevents[] = $e;
						}
					}
				}
			}
		}
	
		return array(
			'cell' => $cell,
			'listevents' => $listevents,
			'weeks' => $weeks,
			'weekdays' => $weekdays,
			'daysnames' => $daysnames,
			'daysnames_abr' => $daysnames_abr,
			'trunc' => $trunc
		);
	}
	function update_participants($calitemId, $adds=null, $dels=null) {
		if (!empty($dels)) {
			foreach ($dels as $del) {
				$this->query('delete from `tiki_calendar_roles` where `calitemId`=? and `username`=? and `role`!=?', array($calitemId, $del, ROLE_ORGANIZER));
			}
		}
		if (!empty($adds)) {
			$all = $this->fetchAll('select * from `tiki_calendar_roles` where `calitemId`=?', array($calitemId));
			foreach ($adds as $add) {
				if (!isset($add['role']) || $add['role'] == ROLE_ORGANIZER) {
					$add['role'] = 0;
				}
				$found = false;
				foreach ($all as $u) {
					if ($u['username'] == $add['name'] && $u['role'] != ROLE_ORGANIZER) {
						if ($u['role'] != $add['role'])
							$this->query('update `tiki_calendar_roles` set `role`=? where `calitemId`=? and `username`=?', array($add['role'], $calitemId, $add['name']));
						$found = true;
						break;
					}
				}
				if (!$found) {
					$this->query('insert into `tiki_calendar_roles`(`calitemId`, `username`, `role`) values(?, ? ,?)', array($calitemId, $add['name'], $add['role']));
				}
			}
		}
	}
}
$calendarlib = new CalendarLib;
