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

    // Checks if an event should be scored and grants points
    // to proper user
    // $multiplier is for rating events, in which the score will
    // be multiplied by other user's rating.
    function score_event($user, $event_type, $id = '', $multiplier=false) {
	if ($user == 'admin') { return; }
	
	$event = $this->get_event($event_type);
	if (!$event || !$event['score']) {
	    return;
	}

	$score = $event['score'];
	if ($multiplier) {
	    $score *= $multiplier;
	}

	if ($id || $event['expiration']) {
	    $expire = $event['expiration'];
	    $event_id = $event_type . '_' . $id;

	    $query = "select * from tiki_users_score where user='$user' and event_id='$event_id' and (not $expire || expire > now())";
	    if ($this->getOne($query)) {
		return;
	    }

	    $query = "replace into tiki_users_score (user, event_id, score, expire) values ('$user', '$event_id', $score, now() + interval $expire minute)";
	    $this->query($query);
	}

	$query = "update users_users set score = score + $score where login='$user'";
	$event['id'] = $id; // just for debug

	$this->query($query);
	return;	
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


    // List users by best scoring
    function ranking($limit = 10, $start = 0) {
	if (!$start) {
	    $start = "0";
	}
	// admin doesn't go on ranking
	$query = "select userId, login, score from users_users where login <> 'admin' order by score desc limit $start, $limit";

	$result = $this->query($query);
	$ranking = array();

	while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
	    $res['position'] = ++$start;
	    $ranking[] = $res;
	}
	return $ranking;
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

    function get_star($score) {
	$star = '';

	$star_colors = array(0 => 'grey',
			     100 => 'blue',
			     500 => 'green',
			     1000 => 'yellow',
			     2500 => 'orange',
			     5000 => 'red',
			     10000 => 'purple');
	
	foreach ($star_colors as $boundary => $color) {
	    if ($score >= $boundary) {
		$star = 'star_'.$color.'.gif';
	    }
	}
                                                                                                                                            
	if (!empty($star)) {
	    $alt = sprintf(tra("%d points"), $score);
	    $star = "<img src=\"images/$star\" alt=\"$alt\">&nbsp;";
	}

	return $star;
    }
}

global $dbTiki;
$scorelib = new ScoreLib($dbTiki);

?>
