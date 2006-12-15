<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class CalendarLib extends TikiLib {
	function CalendarLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to CalendarLib constructor");
		}
		$this->db = $db;
	}
 
	function list_calendars($offset = 0, $maxRecords = -1, $sort_mode = 'name_asc', $find = '') {
		$mid = '';
		$res = array();
		$bindvars = array();
		if ($find) {
			$mid = "where `name` like ?";
			$bindvars[] = '%'.$find.'%';
		}
		$query = "select * from `tiki_calendars` $mid order by ".$this->convert_sortmode($sort_mode);
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$query_cant = "select count(*) from `tiki_calendars` $mid";
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

	// give out an array with Ids viewable by $user
	function list_user_calIds() {
		global $user;
		if ($user) {
			global $userlib;
			$groups = $userlib->get_user_groups($user);
			// need to add something
			$query = "select `calendarId` from `tiki_calendars` where `user`=?";
			$bindvars=array($user);
		} else {
			$query = "select `calendarId` from `tiki_calendars`";
			$bindvars=array();
		}
		$result = $this->query($query,$bindvars);
		$ret = array();
		while ($r = $result->fetchRow()) {
			$res[] = $r['calendarId'];
		}
		return $res;
	}

	function set_calendar($calendarId, $user, $name, $description, $customflags=array(),$options=array()) {
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
			$query = "insert into `tiki_calendars` (`name`,`user`,`description`,`created`,`lastmodif`,`" . implode("`,`", array_keys($customflags)). "`) ";
			$query.= "values (?,?,?,?,?," . implode(",", array_fill(0,count($customflags),"?")). ")";
			$bindvars=array($name,$user,$description,$now,$now);
			foreach ($customflags as $k => $v) $bindvars[]=$v;
			$result = $this->query($query,$bindvars);
			$calendarId = $this->GetOne("select `calendarId` from `tiki_calendars` where `created`=?",array($now));
		}
		$this->query('delete from `tiki_calendar_options` where `calendarId`=?',array((int)$calendarId));
		if (count($options)) {
			foreach ($options as $name=>$value) {
				$name = preg_replace('/[^-_a-zA-Z0-9]/','',$name);
				$this->query('insert into `tiki_calendar_options` (`calendarId`,`optionName`,`value`) values (?,?,?)',array((int)$calendarId,$name,$value));
			}
		}
		return $calendarId;
	}

	function get_calendar($calendarId) {
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
		if (count($allItemsFromCalendar)) {
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
		// now remove the calendar itself:
		$query = "delete from `tiki_calendars` where `calendarId`=?";
		$this->query($query,array($calendarId));
	}

	/* tsart ans tstop are in user time - the data base is in server time */
	function list_raw_items($calIds, $user, $tstart, $tstop, $offset, $maxRecords, $sort_mode='start_asc', $find='') {
		global $user;
		$dc = $this->get_date_converter($user);

		if (sizeOf($calIds) == 0) {
		    return array();
		}

		$tstart = $dc->getServerDateFromDisplayDate($tstart);/* user time -> server time */;
		$tstop = $dc->getServerDateFromDisplayDate($tstop);
		
		$where = array();
		$bindvars=array();
		$time = new timer;
		$time->start();
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
		$query .= "from `tiki_calendar_items` as i left join `tiki_calendars` as c on i.`calendarId`=c.`calendarId` where ($cond)  order by ".$this->convert_sortmode("$sort_mode");
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $this->get_item($res["calitemId"]);
		}
		return $ret;
	}

	function list_items($calIds, $user, $tstart, $tstop, $offset, $maxRecords, $sort_mode='start_asc', $find='') {
		global $user, $tiki_p_change_events;
		$ret = array();
		$list = $this->list_raw_items($calIds, $user, $tstart, $tstop, $offset, $maxRecords, $sort_mode, $find);
		foreach ($list as $res) {
			$mloop = date("m", $res['start']);
			$dloop = date("d", $res['start']);
			$yloop = date("Y", $res['start']);
			$dstart = mktime(0, 0, 0, $mloop, $dloop, $yloop);
			$dend = mktime(0, 0, 0, date("m", $res['end']), date("d", $res['end']), date("Y", $res['end']));
			$tstart = date("Hi", $res["start"]);
			$tend = date("Hi", $res["end"]);
			for ($i = $dstart; $i <= $dend; $i = mktime(0, 0, 0, $mloop, ++$dloop, $yloop)) {
				/* $head is in user time */
				if ($dstart == $dend) {
					$head = date("H:i", $res["start"]). " - " . date("H:i", $res["end"]);
				} elseif ($i == $dstart) {
					$head = date("H:i", $res["start"]). " ...";
				} elseif ($i == $dend) {
					$head = " ... " . date("H:i", $res["end"]);
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
					"nl" => $res["nlId"],
					"prio" => $res["priority"],
					"location" => $res["locationName"],
					"category" => $res["categoryName"],
					"name" => $res["name"],
					"head" => $head,
					"parsedDescription" => $this->parse_data($res["description"]),
					"description" => str_replace("\n|\r", "", $res["description"]),
					"calendarId" => $res['calendarId']
				);
			}
		}
		return $ret;
	}

	function get_item($calitemId) {
		global $user;
		$query = "select i.`calitemId` as `calitemId`, i.`calendarId` as `calendarId`, i.`user` as `user`, i.`start` as `start`, i.`end` as `end`, t.`name` as `calname`, ";
		$query.= "i.`locationId` as `locationId`, l.`name` as `locationName`, i.`categoryId` as `categoryId`, c.`name` as `categoryName`, i.`priority` as `priority`, i.`nlId` as `nlId`, ";
		$query.= "i.`status` as `status`, i.`url` as `url`, i.`lang` as `lang`, i.`name` as `name`, i.`description` as `description`, i.`created` as `created`, i.`lastmodif` as `lastModif`, ";
		$query.= "t.`customlocations` as `customlocations`, t.`customcategories` as `customcategories`, t.`customlanguages` as `customlanguages`, t.`custompriorities` as `custompriorities`, ";
		$query.= "t.`customsubscription` as `customsubscription`, ";
		$query.= "t.`customparticipants` as `customparticipants`  from `tiki_calendar_items` as i left join `tiki_calendar_locations` as l on i.`locationId`=l.`callocId` ";
		$query.= "left join `tiki_calendar_categories` as c on i.`categoryId`=c.`calcatId` left join `tiki_calendars` as t on i.`calendarId`=t.`calendarId` where `calitemId`=?";
		$result = $this->query($query,array((int)$calitemId));
		$res = $result->fetchRow();
		$query = "select `username`, `role` from `tiki_calendar_roles` where `calitemId`=? order by `role`";
		$rezult = $this->query($query,array((int)$calitemId));
		$ppl = array();
		$org = array();

		while ($rez = $rezult->fetchRow()) {
			if ($rez["role"] == '6') {
				$org[] = $rez["username"];
			} elseif ($rez["username"]) {
				$ppl[] = $rez["username"] . ":" . $rez["role"];
			}
		}

		$res["participants"] = implode(',', $ppl);
		$res["organizers"] = implode(',', $org);
		
		$dc = $this->get_date_converter($user);
		$res["start"] = $dc->getDisplayDateFromServerDate($res["start"]); /* user time */
		$res["end"] = $dc->getDisplayDateFromServerDate($res["end"]);

		$res['time_start'] = $res['start'] % 86400;
		$res['date_start'] = (int)$res['start'] - (int)$res['time_start'];

		$res['time_end'] = $res['end'] % 86400;
		$res['date_end'] = (int)$res['end'] - (int)$res['time_end'];
		
		$res['duration'] = $res['end'] - $res['start'];
		$res['parsed'] = $this->parse_data($res['description']);
		return $res;
	}

	function set_item($user, $calitemId, $data) {
		global $user;
		if (!isset($data['calendarId'])) {
			return false;
		}
		$caldata = $this->get_calendar($data['calendarId']);

		$dc = $this->get_date_converter($user);
		$data['start'] = $dc->getServerDateFromDisplayDate($data['start']);/* user time -> server time */
		$data['end'] = $dc->getServerDateFromDisplayDate($data['end']);
		
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
				$orgs = split(',', $data["organizers"]);
				foreach ($orgs as $o) {
					$roles['6'][] = trim($o);
				}
			}
			if ($data["participants"]) {
				$parts = split(',', $data["participants"]);
				foreach ($parts as $pa) {
					$p = split('\:', trim($pa));
					if (isset($p[0])and isset($p[1])) {
						$roles["$p[1]"][] = trim($p[0]);
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

		if ($calitemId) {
			$query = "update `tiki_calendar_items` set `calendarId`=?,`user`=?,`start`=?,`end`=? ,`locationId`=? ,`categoryId`=?,`priority`=?,`status`=?,`url`=?,";
			$query.= "`nlId`=?,`lang`=?,`name`=?,`description`=?,`lastmodif`=? where `calitemId`=?";
			$bindvars=array((int)$data["calendarId"],$user,(int)$data["start"],(int)$data["end"],(int)$data["locationId"],(int)$data["categoryId"],(int)$data["priority"],
			                $data["status"],$data["url"],$data["nlId"],$data["lang"],$data["name"],$data["description"],(int)time(),(int)$calitemId);
			$result = $this->query($query,$bindvars);
		} else {
			$now=time();
			$query = "insert into `tiki_calendar_items` (`calendarId`, `user`, `start`, `end`, `locationId`, `categoryId`,  ";
			$query.= " `priority`, `status`, `url`, `nlId`, `lang`, `name`, `description`, `created`, `lastmodif`) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$bindvars=array((int)$data["calendarId"],$user,(int)$data["start"],(int)$data["end"],(int)$data["locationId"],(int)$data["categoryId"],$data["priority"],$data["status"],$data["url"],$data["nlId"],$data["lang"],$data["name"],$data["description"],(int)$now,(int)$now);
			$result = $this->query($query,$bindvars);
			$calitemId = $this->GetOne("select `calitemId` from `tiki_calendar_items` where `calendarId`=? and `created`=?",array($data["calendarId"],$now));
		}

		if ($calitemId) {
			$query = "delete from `tiki_calendar_roles` where `calitemId`=?";
			$this->query($query,array($calitemId));
		}

		foreach ($roles as $lvl => $ro) {
			foreach ($ro as $r) {
				$query = "insert into `tiki_calendar_roles` (`calitemId`,`username`,`role`) values (?,?,'?')";
				$this->query($query,array($calitemId,$r,$lvl));
			}
		}
		return $calitemId;
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
			$result = $this->query($query,array($calendarId));
			while ($rez = $result->fetchRow()) {
				$res[] = $rez;
			}
		}
		return $res;
	}

	function list_categories($calendarId) {
		$res = array();
		if ($calendarId > 0) {
			$query = "select `calcatId` as `categoryId`, `name` from `tiki_calendar_categories` where `calendarId`=? order by `name`";
			$result = $this->query($query,array($calendarId));
			while ($rez = $result->fetchRow()) {
				$res[] = $rez;
			}
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
				
		$query = "select `start`, `name`, `calitemId`, `calendarId`, `user`, `lastModif` from `tiki_calendar_items` ".$cond."order by ".$this->convert_sortmode('lastModif_desc');
	
		$result = $this->query($query,$bindvars,$maxrows,0);
		
		$ret = array();
		
		while ($res = $result->fetchRow()) {
		    $ret[] = $res;
		}
		
		return $ret;
	}
	
	function importCSV($fname, $calendarId) {
		global $user;
		$fhandle = fopen($fname, "r");
		$fields = fgetcsv($fhandle, 1000);
		if ($fields === false) {
			$smarty->assign('msg', tra("The file is not a CSV file or has not a correct syntax"));
			$smarty->display("error.tpl");
			die;
		}

		while (!feof($fhandle)) {
			$data = fgetcsv($fhandle, 1000);
			$d = array("calendarId"=>$calendarId, "calitemId"=>"0", "name"=>"", "description" =>"", "locationId"=>"", 
					"organizers"=>"", "participants"=>"","status"=>"0","priority"=>"5","categoryId"=>"0","newloc"=>"0","newcat"=>"","nlId"=>"","lang"=>"");
			foreach ($fields as $field) {
				$d[strtolower($field)] = $data[array_search($field, $fields)];
			}
			if (isset($d["subject"]) && empty($d["name"]))
				$d["name"] = $d["subject"];
			if (isset($d["start date"]) && isset($d["start time"]))
				$d["start"] = strtotime($d["start time"], strtotime($d["start date"]));
			if (isset($d["end date"]) && isset($d["end time"]))
				$d["end"] = strtotime($d["end time"], strtotime($d["end date"]));
// TODO do a replace if name, calendarId, start, end exists
			$this->set_item($user, 0, $d);
		}
		fclose ($fhandle);
		return true;
	}

	function upcoming_events($maxrows = -1, $calendarId = 0, $maxDays = -1, $order = 'start_asc') {
		$cond = '';
		$bindvars = array();
		if(is_array($calendarId) && count($calendarId) > 0) {
			$cond = $cond."and (0=1";
			foreach($calendarId as $id) {
				$cond = $cond." or `calendarId` = ? ";
			}
			$cond = $cond.")";
			$bindvars += $calendarId;
		} elseif (!is_array($calendarId) and $calendarId > 0) {
			$cond = $cond." and `calendarId` = ? ";
			$bindvars += array($calendarId);
		}
		$cond .= " and `end` >= (unix_timestamp(now()))";
		if($maxDays > 0)
		{
			$maxSeconds = ($maxDays * 24 * 60 * 60);
			$cond .= " and `end` <= (unix_timestamp(now())) +".$maxSeconds;
		}
		$query = "select `start`, `end`, `name`, `description`, `calitemId`, `calendarId`, `user`, `lastModif` from `tiki_calendar_items` where 1=1 ".$cond." order by ".$this->convert_sortmode($order);

		$result = $this->query($query,$bindvars,$maxrows,0);
			
		$ret = array();
			
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
	
		return $ret;
	}
}
global $dbTiki;
$calendarlib = new CalendarLib($dbTiki);

?>
