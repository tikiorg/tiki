<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This is for users to earn points in the community
// It's been implemented before and now it's being coded in v1.9.
// This code is provided here for you to check this implementation
// and make comments, please see
// http://tiki.org/tiki-index.php?page=ScoringSystemIdea

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class ScoreLib extends TikiLib
{

	// User's general classification on site
	function user_position($user) {
		$score = $this->getOne("select `score` from `users_users` where `login`=?",array($user));
		return $this->getOne("select count(*)+1 from `users_users` where `score` > ? and `login` <> ?",array((int)$score,'admin'));
	}


	// Number of users that go on ranking
	function count_users() {
		return $this->getOne("select count(*) from `users_users` where `score`>0 and `login`<>'admin'",array());
	}

	// All event types, for administration
	function get_all_events() {
		global $prefs;

		$query = "select * from `tiki_score`";
		$result = $this->query($query,array());
		$index = array();
		while ($res = $result->fetchRow()) {
		    $index[$res['event']] = $res;
		}

		// load $events
		require('lib/score/events.php');

		$event_list = array();
		foreach ($events as $event_data) {
		    $features = preg_split('/(\s|,)+/',$event_data[0]);
		    $show = true;
		    foreach ($features as $feature) {
			if (!empty($feature)) {
			    if ($prefs[$feature] != 'y') {
				$show = false;
			    }
			}
		    }
		    if ($show) {
			$event = array('category'    => $event_data[1],
				       'event'       => $event_data[2],
				       'description' => $event_data[3],
				       'score'       => $event_data[4],
				       'expiration'  => $event_data[5]);

			$event_name = $event_data[2];
			if (isset($index[$event_name])) {
			    $event['score']       = $index[$event_name]['score'];
			    $event['expiration']  = $index[$event_name]['expiration'];
			}

			$event_list[] = $event;
		    }
		}

		return $event_list;
	}

	// Read information from admin and updates event's punctuation
	function update_events($events) {
	    foreach ($events as $event_name => $event) {
		$query = "delete from `tiki_score` where `event`=?";
		$this->query($query, array($event_name));

		$query = "insert into `tiki_score` (`event`,`score`,`expiration`) values (?,?,?)";
		$this->query($query,array($event_name, (int) $event['score'], $event['expiration']));
	    }
	}

}
$scorelib = new ScoreLib;
