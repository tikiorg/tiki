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

class MiniCalLib extends TikiLib
{
	// Returns an array where each member of the array has:
	// start: unix timestamp of the interval start time
	// end  : unix timestamp of the interval end time
	// events : array of events for the slot listing:
	// title, description and duration
	function minical_events_by_slot($user, $start, $end, $interval)
	{
		// since interval is in hour convert it to seconds
		//$interval = $interval * 60 * 60;
		$slots = [];

		while ($start <= $end) {
			$aux = [];

			$aux['start'] = $start;
			$end_p = $start + $interval;
			$aux['end'] = $end_p;
			$query = "select * from `tiki_minical_events` where `user`=? and `start`>=? and `start`<? order by " . $this->convertSortMode("start_asc");
			//print($query);print("<br />");
			$result = $this->query($query, [$user,(int)$start,(int)$end_p]);
			$events = [];

			while ($res = $result->fetchRow()) {
				$res['end'] = $res['start'] + $res['duration'];
				$res2 = [];
				if ($res['topicId']) {
					$query2 = "select `topicId`,`isIcon`,`path`,`name` from `tiki_minical_topics` where `topicId`=?";
					$result = $this->query($query2, [(int)$res['topicId']]);
					$res2 = $result->fetchRow();
				}
				$res['topic'] = $res2;
				$events[] = $res;
			}
			$aux['events'] = $events;
			$slots[] = $aux;
			$start += $interval;
		}
		return $slots;
	}

	function minical_upload_topic($user, $topicname, $name, $type, $size, $data, $path)
	{
		if (strlen($data) == 0) {
			$isIcon = 'y';
		} else {
			$isIcon = 'n';
		}
		$query = "insert into `tiki_minical_topics`(`user`,`name`,`filename`,`filetype`,`filesize`,`data`,`isIcon`,`path`) values(?,?,?,?,?,?,?,?)";
		$this->query($query, [$user,$topicname,$name,$type,(int)$size,$data,$isIcon,$path]);
	}

	function minical_list_topics($user, $offset, $maxRecords, $sort_mode, $find)
	{
		$bindvars = [$user];
		if ($find) {
			$mid = " and (`name` like ? or `filename` like ?)";
			$bindvars[] = "%$find%";
			$bindvars[] = "%$find%";
		} else {
			$mid = "";
		}

		$query = "select `isIcon`,`path`,`name`,`topicId` from `tiki_minical_topics` where `user`=? $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_minical_topics` where `user`=? $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = [];
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		$retval = [];
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function minical_get_topic($user, $topicId)
	{
		$query = "select * from `tiki_minical_topics` where `user`=? and `topicId`=?";
		$result = $this->query($query, [$user,(int)$topicId]);
		$res = $result->fetchRow();
		return $res;
	}

	function minical_list_events($user, $offset, $maxRecords, $sort_mode, $find)
	{
		$bindvars = [$user];
		if ($find) {
			$mid = " and (`title` like ? or `description` like ?)";
			$bindvars[] = "%$find%";
			$bindvars[] = "%$find%";
		} else {
			$mid = "";
		}

		$query = "select * from `tiki_minical_events` where `user`=? $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_minical_events` where `user`=? $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = [];

		while ($res = $result->fetchRow()) {
			$res2 = [];
			if ($res['topicId']) {
				$query2 = "select `topicId`,`isIcon`,`path`,`name` from `tiki_minical_topics` where `topicId`=?";
				$result2 = $this->query($query2, [(int)$res['topicId']]);
				$res2 = $result2->fetchRow();
			}
			$res['topic'] = $res2;
			$ret[] = $res;
		}

		$retval = [];
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function minical_list_events_from_date($user, $offset, $maxRecords, $sort_mode, $find, $pdate)
	{
		$bindvars = [(int)$pdate,$user];
		if ($find) {
			$mid = " and (`title` like ? or `description` like ?)";
			$bindvars[] = "%$find%";
			$bindvars[] = "%$find%";
		} else {
			$mid = "";
		}
		$query = "select * from `tiki_minical_events` where `start`>? and `user`=? $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_minical_events` where `start`>? and `user`=? $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = [];
		while ($res = $result->fetchRow()) {
			$res2 = [];
			if ($res['topicId']) {
				$query2 = "select `topicId`,`isIcon`,`path`,`name` from `tiki_minical_topics` where `topicId`=?";
				$result2 = $this->query($query2, [(int)$res['topicId']]);
				$res2 = $result2->fetchRow();
			}
			$res['topic'] = $res2;
			$ret[] = $res;
		}
		$retval = [];
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function minical_get_event($user, $eventId)
	{
		$query = "select * from `tiki_minical_events` where `user`=? and `eventId`=?";
		$result = $this->query($query, [$user,(int)$eventId]);
		$res = $result->fetchRow();
		return $res;
	}

	function minical_remove_topic($user, $topicId)
	{
		$query = "delete from `tiki_minical_topics` where `user`=? and `topicId`=?";
		$this->query($query, [$user,(int)$topicId]);
	}

	function minical_event_reminded($user, $eventId)
	{
		$query = "update `tiki_minical_events` set `reminded`=? where `user`=? and `eventId`=?";
		$this->query($query, ["y",$user,(int)$eventId]);
	}

	function minical_replace_event($user, $eventId, $title, $description, $start, $duration, $topicId)
	{
		if ($eventId) {
			$query = "update `tiki_minical_events` set `topicId`=?,`end`=?,`title`=?,`description`=?,`start`=?,`duration`=?,`reminded`=?  where `user`=? and `eventId`=?";
			$this->query($query, [(int)$topicId,$start + $duration,$title,$description,(int)$start,(int)$duration,"n",$user,(int)$eventId]);
			return $eventId;
		} else {
			$query = "insert into `tiki_minical_events`(`user`,`title`,`description`,`start`,`duration`,`end`,`topicId`,`reminded`) values(?,?,?,?,?,?,?,?)";
			$this->query($query, [$user,$title,$description,(int)$start,(int)$duration,$start + $duration,(int)$topicId,"n"]);
			$Id = $this->getOne("select max(`eventId`) from `tiki_minical_events` where `user`=? and `start`=?", [$user,(int)$start]);
			return $Id;
		}
	}

	function minical_remove_event($user, $eventId)
	{
		$query = "delete from `tiki_minical_events` where `user`=? and `eventId`=?";
		$this->query($query, [$user,(int)$eventId]);
	}

	function minical_get_events_to_remind($user, $rem)
	{
		// Search for events that are not reminded and will start
		// in less than $rem
		$limit = $this->now + $rem;
		$query = "select * from `tiki_minical_events` where `user`=? and `reminded`<>? and `start`<=? and `start`>?";
		$result = $this->query($query, [$user,'y',(int)$limit,(int)$this->now]);
		$ret = [];
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		return $ret;
	}

	function minical_remove_old($user, $pdate)
	{
		$query = "delete from `tiki_minical_events` where `user`=? and `start`<?";
		$this->query($query, [$user,(int)$pdate]);
	}
}
$minicallib = new MiniCalLib;
