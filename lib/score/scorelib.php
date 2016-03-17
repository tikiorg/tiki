<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 *
 */
class ScoreLib extends TikiLib
{
	const CACHE_KEY = 'score_events';

	function touch()
	{
		TikiLib::lib('cache')->invalidate(self::CACHE_KEY);
	}
	// User's general classification on site
    /**
     * @param $user
     * @return mixed
     */
    public function user_position($user)
	{
		global $prefs;
		$score_expiry_days = $prefs['feature_score_expday'];

		$score = $this->get_user_score($user);

		if (empty($score_expiry_days)) {
			// score does not expire
			$query = "select count(*)+1 from `tiki_object_scores` tos
				where `recipientObjectType`='user'
				and `recipientObjectId`<> ?
				and `pointsBalance` > ?
				and tos.`id` = (select max(id) from `tiki_object_scores` where `recipientObjectId` = tos.`recipientObjectId` and `recipientObjectType`='user' group by `recipientObjectId`)
				group by `recipientObjectId`";

			$position = $this->getOne($query, array($user, $score));
		} else {
			// score expires
			$query = "select count(*)+1 from `tiki_object_scores` tos
				where `recipientObjectType`='user'
				and `recipientObjectId`<> ?
				and `pointsBalance` - ifnull((select `pointsBalance` from `tiki_object_scores`
					where `recipientObjectId`=tos.`recipientObjectId`
					and `recipientObjectType`='user'
					and `date` < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL ? DAY))
					order by id desc limit 1), 0) > ?
				and tos.`id` = (select max(id) from `tiki_object_scores` where `recipientObjectId` = tos.`recipientObjectId` and `recipientObjectType`='user' group by `recipientObjectId`)
				group by `recipientObjectId`";

			$position = $this->getOne($query, array($user, $score_expiry_days, $score));
		}

		return $position;
	}

	// User's score on site
	// allows getting score of a single user
    /**
     * @param $user
     * @return mixed
     */
    public function get_user_score($user, $dayLimit = 0)
	{
		global $prefs;
		$score_expiry_days = $prefs['feature_score_expday'];
		if (!empty($dayLimit) && $dayLimit < $score_expiry_days) {
			//if the day limit is set, change the expiry to the day limit.
			$score_expiry_days = $dayLimit;
		}
		$query = "select `pointsBalance` from `tiki_object_scores` where `recipientObjectId`=? and `recipientObjectType`='user' order by id desc";
		$total_score = $this->getOne($query, array($user));
		//if points don't expire, return total score; otherwise
		if (empty($score_expiry_days)) {
			return $total_score;
		} else {
			$query = "select `pointsBalance` from `tiki_object_scores`
					where `recipientObjectId`=? and `recipientObjectType`='user' and
					`date` < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL ? DAY))
					order by id desc";
			$score_at_expiry = $this->getOne($query, array($user, $score_expiry_days));
			if (empty($score_at_expiry)) {
				$score_at_expiry = 0;
			}
			//subtract the score at expiry from the total score to get valid score
			$score = $total_score - $score_at_expiry;
			return $score;
		}
	}

	// Number of users that go on ranking
    /**
     * @return mixed
     */
    public function count_users()
	{
		global $prefs;
		$score_expiry_days = $prefs['feature_score_expday'];

		if (empty($score_expiry_days)) {
			// score does not expire
			$query = "select count(*) from `tiki_object_scores` tos
				where `recipientObjectType`='user'
				and `pointsBalance` > 0
				and tos.`id` = (select max(id) from `tiki_object_scores` where `recipientObjectId` = tos.`recipientObjectId` and `recipientObjectType`='user' group by `recipientObjectId`)
				group by `recipientObjectId`";

			$count = $this->getOne($query, array());
		} else {
			// score expires
			$query = "select count(*) from `tiki_object_scores` tos
				where `recipientObjectType`='user'
				and `pointsBalance` - ifnull((select `pointsBalance` from `tiki_object_scores`
					where `recipientObjectId`=tos.`recipientObjectId`
					and `recipientObjectType`='user'
					and `date` < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL ? DAY))
					order by id desc limit 1), 0) > 0
				and tos.`id` = (select max(id) from `tiki_object_scores` where `recipientObjectId` = tos.`recipientObjectId` and `recipientObjectType`='user' group by `recipientObjectId`)
				group by `recipientObjectId`";

			$count = $this->getOne($query, array($score_expiry_days));
		}

		return $count;
	}

	// All event types, for administration
    /**
     * @return array
     */
    public function get_all_events()
	{
		$query = "SELECT * FROM `tiki_score` WHERE data IS NOT NULL";
		$result = $this->query($query, array());
		$event_list = array();
		while ($res = $result->fetchRow()) {
			$res['scores'] = json_decode($res['data']);
			foreach ($res['scores'] as $key => $score) {
				$res['scores'][$key]->validObjectIds = implode(",", $score->validObjectIds);
			}

			$event_list[] = $res;
		}
		return $event_list;
	}

	// Read information from admin and updates event's punctuation
    /**
     * @param $events
     */
    public function update_events($events)
	{
		//clear old scores before re-inserting
		$query = "delete from `tiki_score`";
		$this->query($query);

		foreach ($events as $event_name => $event_data) {
			$reversalEvent = $event_data['reversalEvent'];
			unset($event_data['reversalEvent']);

			foreach ($event_data as $key => $rules) {
				$tempArr = explode(',', $rules['validObjectIds']);
				$event_data[$key]['validObjectIds'] = array_map('trim',$tempArr);
			}

			$event_data = json_encode($event_data);

			$query = "insert into `tiki_score` (`event`,`reversalEvent`,`data`) values (?,?,?)";
			$this->query($query, array($event_name, $reversalEvent, $event_data));
		}
		$this->touch();
		return;
	}

	/**
	 * Function to get available event types
	 */
	function getEventTypes()
	{
		$graph = TikiLib::events()->getEventGraph();
		sort($graph['nodes']);
		return $graph['nodes'];
	}

	/**
	 * Bind events from the scoring system
	 * @param $manager
	 */
	function bindEvents($manager)
	{
		try {

			$list = $this->getScoreEvents();
			$eventsList = $list['events'];
			$reversalEventsList = $list['reversalEvents'];

			foreach ($reversalEventsList as $eventType) {
				$manager->bind($eventType, Tiki_Event_Lib::defer('score', 'reversePoints'));
			}
			foreach ($eventsList as $eventType) {
				$manager->bind($eventType, Tiki_Event_Lib::defer('score', 'assignPoints'));
			}
		} catch (TikiDb_Exception $e) {
			// Prevent failures from locking-out users
		}
	}

	/**
	 * This is the function called when a bound event is triggered. This stores the scoring transaction to the db
	 * and increases the score
	 * @param array $args
	 * @param string $eventType
	 * @throws Exception
	 */
	function assignPoints($args=array(), $eventType="") {
		$rules = $this->getScoreEventRules($eventType);
		$date = TikiLib::lib('tiki')->now;
		//for each rule associated with the event, set up the scor
		foreach ($rules as $rule) {
			// if the object is invalid, do nothing.
			if (! $this->objectIsValid($args,$rule)) {
				continue;
			}
			$recipient = $this->evaluateExpression($rule->recipient, $args, "eval");
			$recipientType = $this->evaluateExpression($rule->recipientType, $args);
			$points = $this->evaluateExpression($rule->score, $args);
			if (!$recipient || !$points) {
				continue;
			}
			if ($rule->expiration > 0 && !$this->hasWaitedMinTime($args,$rule, $recipientType, $recipient)) {
				continue;
			}
			//if user is anonymous, store a unique identifier in a cookie and set it as the user.
			if (empty($args['user'])) {
				$uniqueVal = getCookie('anonUserScoreId');
				if (empty($uniqueVal)) {
					$uniqueVal = getenv('HTTP_CLIENT_IP') . time() . rand();
					$uniqueVal = md5($uniqueVal);
					setCookieSection('anonUserScoreId', "anon".$uniqueVal);
				}
				$args['user'] = $uniqueVal;
			}
			$pbalance = $this->getPointsBalance($recipientType,$recipient);
			$data = [
				'triggerObjectType' => $args['type'],
				'triggerObjectId' => $args['object'],
				'triggerUser' => $args['user'],
				'triggerEvent' => $eventType,
				'ruleId' => $rule->ruleId,
				'recipientObjectType' => $recipientType,
				'recipientObjectId' => $recipient,
				'pointsAssigned' => $points,
				'pointsBalance' => $pbalance + $points,
				'date' => $date,
			];

			$id = $this->table()->insert($data);
		}
	}

	/**
	 * This is the reversal function. If a reversal event is triggered, then check if there is an associated
	 * score and reverse it.
	 * @param $args
	 * @param $eventType
	 * @throws Exception
	 */
	function reversePoints($args, $eventType) {
		$query = "SELECT event FROM `tiki_score` WHERE reversalEvent=?";
		//if you find an original event, reverse it.
		if ($originalEvent = $this->getOne($query, [$eventType])) {
			//fetch all the scoring entries that were put in the last time
			$date = $this->table()->fetchOne(
				'date',
				array('triggerObjectType' => $args['type'],
					'triggerObjectId' => $args['object'],
					'triggerUser' => $args['user'],
					'triggerEvent' => $originalEvent
				),
				array("id" => "desc")
			);
			$result = $this->table()->fetchAll(
				array('id', 'ruleId', 'pointsAssigned', 'recipientObjectType', 'recipientObjectId', 'reversalOf'),
				array('triggerObjectType' => $args['type'],
					'triggerObjectId' => $args['object'],
					'triggerUser' => $args['user'],
					'triggerEvent' => $originalEvent,
					'date' => $date,
				)
			);

			$date = TikiLib::lib('tiki')->now;
			foreach($result as $row) {
				// if the most recent transaction was a reversal, exit as to not reverse again
				if ($row['reversalOf'] > 0) {
					continue;
				}
				$pbalance = $this->getPointsBalance($row['recipientObjectType'],$row['recipientObjectId']);
				$data = [
					'triggerObjectType' => $args['type'],
					'triggerObjectId' => $args['object'],
					'triggerUser' => $args['user'],
					'triggerEvent' => $eventType,
					'ruleId' => $row['ruleId'],
					'recipientObjectType' => $row['recipientObjectType'],
					'recipientObjectId' => $row['recipientObjectId'],
					'pointsAssigned' => -$row['pointsAssigned'],
					'pointsBalance' => $pbalance - $row['pointsAssigned'],
					'reversalOf' => $row['id'],
					'date' => $date,
				];
				$id = $this->table()->insert($data);
			}
		}
		return;
	}

	function table($tableName, $autoIncrement = true)
	{
		return TikiDb::get()->table('tiki_object_scores');
	}

	/**
	 * This fetches all the events in the score table to bind all of them
	 * @return array
	 * @throws Exception
	 */
	private function getScoreEvents()
	{
		$cachelib = TikiLib::lib('cache');
		if (! $result = $cachelib->getSerialized(self::CACHE_KEY)) {
			$query = "SELECT * FROM `tiki_score` WHERE data IS NOT NULL";
			$result = $this->query($query, array());
			$event_list = array();
			$event_reversal_list = array();

			while ($res = $result->fetchRow()) {
				$event_list[] = $res['event'];
				if ($res['reversalEvent']){
					$event_reversal_list[] = $res['reversalEvent'];
				}
			}
			$result = array('events' => $event_list,
				'reversalEvents' => $event_reversal_list
			);
			$cachelib->cacheItem(self::CACHE_KEY, serialize($result));
		}

		return $result;
	}

	/**
	 * This gets all the rules associated with a given event.
	 * @param $eventType
	 * @return mixed
	 */
	private function getScoreEventRules($eventType)
	{
		$query = "SELECT data FROM `tiki_score` WHERE event=? and data IS NOT NULL";
		$result = $this->query($query, [$eventType]);

		$rules = json_decode($result->fetchRow()['data']);

		return $rules;
	}

	/**
	 * This retrieves the score of a given object.
	 * @param $recipientType
	 * @param $recipient
	 * @return bool|mixed
	 */
	function getPointsBalance($recipientType,$recipient) {
		$query = "SELECT pointsBalance FROM `tiki_object_scores` WHERE recipientObjectType=? and recipientObjectId=? order by id desc";
		$result = $this->getOne($query, [$recipientType,$recipient]);

		return $result;
	}

	/**
	 * This is only called and checked if you are assigning points. It is not done on reversals.
	 *
	 * @param $args
	 * @param $rule
	 * @return bool
	 */
	function objectIsValid($args,$rule) {
		if (empty($rule->validObjectIds) || empty($rule->validObjectIds[0])) {
			return true;
		}
		if (in_array($args['object'], $rule->validObjectIds) || in_array($args['type'].":".$args['object'], $rule->validObjectIds)) {
			return true;
		}
		return false;
	}

	/**
	 * This is only called and checked if you are assigning points. It is not done on reversals.
	 *
	 * @param $args
	 * @param $rule
	 * @return bool
	 */
	function hasWaitedMinTime($args, $rule, $recipientType, $recipient) {
		$query = "SELECT date FROM `tiki_object_scores`
					WHERE triggerObjectType=? and triggerObjectId=? and ruleId=? and recipientObjectType=?
					and recipientObjectId=? and reversalOf is null
					order by id desc";
		$date = $this->getOne($query, [$args['type'],$args['object'], $rule->ruleId, $recipientType, $recipient]);
		$currentTime = time();
		$expiration = $date + $rule->expiration;
		if ($expiration > $currentTime) {
			return false;
		}
		return true;
	}

	/**
	 * This is called to evaluate a given expression.
	 * @param $expr
	 * @param $args
	 * @param string $default
	 * @return bool|float|void
	 */
	function evaluateExpression($expr, $args, $default="str"){
		if (0 !== strpos($expr, "(")) {
			$expr = "($default $expr)";
		}
		$runner = new Math_Formula_Runner(
			array(
				'Math_Formula_Function_' => '',
				'Tiki_Formula_Function_' => '',
			)
		);
		try {
			$runner->setVariables($args);
			$runner->setFormula($expr);
			return $runner->evaluate();
		} catch( Math_Formula_Exception $e ) {
			return;
		}
	}
}
