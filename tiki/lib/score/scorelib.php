<?php
// This is for users to earn points in the community
// It's been implemented before and now it's being coded in v1.9.
// This code is provided here for you to check this implementation
// and make comments, please see
// http://tikiwiki.org/tiki-index.php?page=ScoringSystemIdea

class ScoreLib extends TikiLib {

	function ScoreLib($db) {
		if(!$db) {
			die("Invalid db object passed to ScoreLib constructor");
		}
		$this->db = $db;
	}

	// All information about an event type
	function get_event($event) {
		$query = "select * from `tiki_score` where `event`=?";
		$result = $this->query($query,array($event));
		return $result->fetchRow();
	}

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
		$query = "select * from `tiki_score` order by `category`, `ord`";
		$result = $this->query($query,array());
		$ranking = array();
		while ($res = $result->fetchRow()) {
	    $ranking[] = $res;
		}
		return $ranking;
	}

	// Read information from admin and updates event's punctuation
	function update_events($events) {
		foreach ($events as $event_name => $event) {
	    $query = "update `tiki_score` set `score`=?, `expiration`=? where `event`=?";
	    $this->query($query,array((int) $event['score'], $event['expiration'], $event_name));
		}
	}

}

global $dbTiki;
$scorelib = new ScoreLib($dbTiki);

?>
