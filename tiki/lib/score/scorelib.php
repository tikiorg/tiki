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
	$query = "select * from tiki_score where event='$event'";
	$result = $this->query($query);
	return $result->fetchRow(DB_FETCHMODE_ASSOC);
    }

    // User's general classification on site
    function user_position($user) {
	$score = $this->getOne("select score from users_users where login='$user'");
	return $this->getOne("select count(*)+1 from users_users where score > $score and login <> 'admin'");
    }


    // Number of users that go on ranking
    function count_users() {
	return $this->getOne("select count(*) from users_users where score>0 and login<>'admin'");
    }

    // All event types, for administration
    function get_all_events() {
	$query = "select * from tiki_score order by category, ord";
	$result = $this->query($query);
	$ranking = array();
	while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
	    $ranking[] = $res;
	}
	return $ranking;
    }

    // Read information from admin and updates event's punctuation
    function update_events($events) {
	foreach ($events as $event_name => $event) {
	    $query = sprintf("update tiki_score set score=%f, expiration=%f where event='%s'", $event['score'], $event['expiration'], addslashes($event_name));
	    $this->query($query);
	}
    }

}

global $dbTiki;
$scorelib = new ScoreLib($dbTiki);

?>
