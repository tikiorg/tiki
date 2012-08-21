<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class RatingLib extends TikiDb_Bridge
{
	private $configurations;

	/**
	 * Record a vote for the current user or anonymous visitor.
	 */
	function record_vote( $type, $objectId, $score, $time = null )
	{
		$target = $this->get_current_user();
		return $this->record_user_vote($target, $type, $objectId, $score, $time);
	}

	/**
	 * Obtain the last vote on the item by the user or anonymous visitor.
	 */
	function get_vote( $type, $objectId )
	{
		$target = $this->get_current_user();
		return $this->get_user_vote($target, $type, $objectId);
	}

	function convert_rating_sort( & $sort_mode, $type, $objectKey )
	{
		if ( preg_match('/^adv_rating_(\d+)_(asc|desc)$/', $sort_mode, $parts) ) {
			$sort_mode = 'adv_rating_' . $parts[2];
			return ' LEFT JOIN (SELECT `object` as `adv_rating_obj`, `value` as `adv_rating` FROM `tiki_rating_obtained` WHERE `type` = ' . $this->qstr($type) . ' AND `ratingConfigId` = ' . intval($parts[1]) . ') `adv_rating` ON `adv_rating`.`adv_rating_obj` = ' . $objectKey . ' ';
		}
	}

	function obtain_ratings($type, $itemId, $recalculate = false)
	{
		if ($type == 'wiki page') {
			$itemId = TikiLib::lib('tiki')->get_page_id_from_name($itemId);
		}

		if ($recalculate) {
			$this->refresh_rating($type, $itemId);
		}

		$query = "SELECT ratingConfigId, value FROM tiki_rating_obtained WHERE type = ? AND object = ?";
		return $this->fetchMap($query, array($type, $itemId));
	}

	/**
	 * Collect the aggregate score of an item based on various arguments.
	 *
	 * @param $type string The object type
	 * @param $objectId int|string The object identifier
	 * @param $aggregate string The aggregate function to use (sum or avg)
	 * @param $params array Various other arguments to affect the result. All options
	 *                      are valid for both avg and sum. If no parameters are provided,
	 *                      aggregate will be performed on the entire history, for all visitors
	 *                      without limitations on voting frequency. Valid parameters are:
	 *                      - range : Number of seconds to look back for
	 *                      - ignore : 'anonymous' is the only valid value. 
	 *                                 Will make sure only registered users are considered.
	 *                      - keep : Only consider one vote per user. 'oldest' or 'latest'.
	 *                      - revote : If the user is allowed to vote multiple times, contains the
	 *                                 amount of seconds between votes. Requires keep parameter.
	 */
	function collect( $type, $objectId, $aggregate, array $params = array() )
	{
		if ( $aggregate != 'avg' && $aggregate != 'sum' ) {
			return false;
		}

		$token = $this->get_token($type, $objectId);
		$joins = array( '`tiki_user_votings` `uv`' );
		$where = array( '( `id` = ? )' );
		$bindvars = array( $token );

		if ( isset( $params['range'] ) ) {
			$where[] = '( `time` > ? )';
			$bindvars[] = time() - abs($params['range']);
		}

		if ( isset( $params['ignore'] ) && $params['ignore'] == 'anonymous' ) {
			$where[] = '( `user` NOT LIKE ? )';
			$bindvars[] = "anonymous\0%";
		}

		if ( isset( $params['keep'] ) ) {
			if ( $params['keep'] == 'latest' ) {
				$connect = 'MAX';
			} elseif ( $params['keep'] == 'oldest' ) {
				$connect = 'MIN';
			}

			if ( $connect ) {
				$extra = '';
				if ( isset( $params['revote'] ) ) {
					$revote = max(1, abs($params['revote']));
					$extra = " , FLOOR( ( UNIX_TIMESTAMP() - `time` ) / $revote )";
				}
				$joins[] = '
					INNER JOIN ( SELECT ' . $connect . '(`time`) `t`, `user` `u` FROM `tiki_user_votings` WHERE ' . implode(' AND ', $where) . ' GROUP BY `user` ' . $extra . ' ) `j`
						ON `j`.`u` = `uv`.`user` AND `j`.`t` = `uv`.`time`';
				$bindvars = array_merge($bindvars, $bindvars);
			}
		}


		$query = 'SELECT '. $aggregate . '(`uv`.`optionId`) FROM ' . implode(' ', $joins) . ' WHERE ' . implode(' AND ', $where);

		return (double) $this->getOne($query, $bindvars);
	}

	function get_token( $type, $objectId )
	{
		switch( $type ) {
		case 'article':
			return "article$objectId";
		case 'comment':
			return "comment$objectId";
		case 'wiki page':
			if ( is_numeric($objectId) ) {
				return "wiki$objectId";
			}

    		break;
		case 'test':
			return "test.$objectId";
		}

		return null;
	}

	function record_user_vote( $user, $type, $objectId, $score, $time = null )
	{
		global $tikilib, $prefs;

		if ( ! $this->is_valid($type, $score, $objectId) ) {
			return false;
		}

		if ( is_null($time) ) {
			$time = time();
		}

		$ip = $tikilib->get_ip_address();
		$token = $this->get_token($type, $objectId);

		if ( is_null($token) ) {
			return false;
		}

		$this->query(
						'INSERT INTO `tiki_user_votings` ( `user`, `ip`, `id`, `optionId`, `time` ) VALUES( ?, ?, ?, ?, ? )',
						array( $user, $ip, $token, $score, $time )
		);

		if ( $prefs['rating_advanced'] == 'y' ) {
			if ( $prefs['rating_recalculation'] == 'vote' ) {
				$this->refresh_rating($type, $objectId);
			} elseif ( $prefs['rating_recalculation'] == 'randomvote' ) {
				$this->attempt_refresh();
			}
		}

		return true;
	}

	function record_anonymous_vote( $sessionId, $type, $objectId, $score, $time = null )
	{
		return $this->record_user_vote($this->session_to_user($sessionId), $type, $objectId, $score, $time);
	}

	function is_valid( $type, $value, $objectId )
	{
		$options = $this->get_options($type, $objectId);

		return in_array($value, $options);
	}

	function get_options( $type, $objectId )
	{
		$pref = 'rating_default_options';

		switch( $type ) {
			case 'wiki page':
				$pref = 'wiki_simple_ratings_options';
	            break;
			case 'article':
				$pref = 'article_user_rating_options';
	            break;
			case 'comment':
				$pref = 'wiki_comments_simple_ratings_options';
				break;
			case 'forum':
				$pref = 'wiki_comments_simple_ratings_options';
				break;
		}

		global $tikilib;

		$override = $this->get_override($type, $objectId);

		if (!empty($override)) {
			return $override;
		}

		return $tikilib->get_preference($pref, range(1, 5), true);
	}

	function set_override($type, $objectId, $value)
	{
		global $attributelib;
		$options = $this->override_array($type);

		$attributelib->set_attribute($type, $objectId, $type.".rating.override", $options[$value]);
	}

	function get_override($type, $objectId)
	{
		global $attributelib;
		require_once('lib/attributes/attributelib.php');
		$attrs = $attributelib->get_attributes($type, $objectId);
		end($attrs);
		$key = key($attrs);
		if (empty($attrs) || empty($attrs[$key])) return;

		$attr = explode(',', $attrs[$type . '.rating.override']);
		return $attr;
	}

	function override_array($type, $maintainArray = false)
	{
		global $prefs;

		$array = array();
		$vals = array();
		$options = array();

		switch( $type ) {
			case 'wiki page':
				$pref = 'wiki_simple_ratings_options';
				break;
			case 'article':
				$pref = 'article_user_rating_options';
				break;
			case 'comment':
				$pref = 'wiki_comments_simple_ratings_options';
				break;
			case 'forum':
				$pref = 'wiki_comments_simple_ratings_options';
				break;
		}

		foreach($prefs[$pref] as $option) {
			$options[] = $option;
			//Ensure there are at least 2 to choose from
			if (count($options) > 1) {
				if ($maintainArray == false) {
					$vals[] = $value = implode($options, ',');
				} else {
					$vals[] = $value = $options;
				}
				$array[] = $value;
			}
		}

		return $array;
	}

	function deliberation_votings($threadId)
	{
		$user_votings = $this->fetchAll(
			"SELECT *
			FROM tiki_user_votings tuv1
			WHERE id=? AND time = (
				SELECT max(time)
				FROM tiki_user_votings tuv2
				WHERE tuv2.user = tuv1.user AND tuv1.id = tuv2.id
			)
			GROUP BY user
			ORDER BY time DESC", array('comment'.$threadId));

		$votings = array();
		$percent = 100 / count($user_votings);

		foreach($user_votings as $user_voting) {
			if (!isset($votings[$user_voting['optionId']])) $votings[$user_voting['optionId']] = 0;

			$votings[$user_voting['optionId']]++;
		}

		foreach($votings as &$voting) {
			$voting = array(
				"voting" => $voting,
				"percent" => round($percent * $voting)
			);
		}

		ksort($votings);

		return $votings;
	}

	function get_user_vote( $user, $type, $objectId )
	{
		$result = $this->fetchAll(
						'SELECT `optionId` FROM `tiki_user_votings` WHERE `user` = ? AND `id` = ? ORDER BY `time` DESC',
						array( $user, $this->get_token($type, $objectId) ), 1
		);

		if ( count($result) == 1 ) {
			return (float) $result[0]['optionId'];
		}
	}

	function get_anonymous_vote( $sessionId, $type, $objectId )
	{
		return $this->get_user_vote($this->session_to_user($sessionId), $type, $objectId);
	}

	private function session_to_user( $sessionId )
	{
		return "anonymous\0$sessionId";
	}

	private function get_current_user()
	{
		global $user;

		if ( $user ) {
			return $user;
		} else {
			return $this->session_to_user(session_id());
		}
	}

	function test_formula( $formula, $available = false )
	{
		try {
			$runner = $this->get_runner();
			$runner->setFormula($formula);
			$variables = $runner->inspect();

			if ( $available ) {
				$extra = array_diff($variables, $available);
				if ( count($extra) > 0 ) {
					return tr('Unknown variables referenced: %0', implode(', ', $extra));
				}
			}
		} catch( Math_Formula_Exception $e ) {
			return $e->getMessage();
		}
	}

	function refresh_rating( $type, $object )
	{
		$configurations = $this->get_initialized_configurations();
		$runner = $this->get_runner();

		$this->internal_refresh_rating($type, $object, $runner, $configurations);
	}

	function attempt_refresh( $all = false )
	{
		global $prefs;
		if ( 1 == rand(1, $prefs['rating_recalculation_odd']) ) {
			$this->internal_refresh_list($prefs['rating_recalculation_count']);
		}
	}

	function refresh_all()
	{
		$this->internal_refresh_list(-1);
	}

	function get_options_smiles_backgrounds($type)
	{
		$overrides = $this->override_array($type, true);
		$backgrounds = array();
		foreach($overrides as $override) {
			$backgrounds[] = 'img/rating_smiles/bg_' . count($override) . '.png';
		}
		return $backgrounds;
	}

	function get_options_smiles_colors()
	{
		return array(
			0 => '#d2d2d2',
			1 => '#ce4744',
			2 => '#e84642',
			3 => '#f26842',
			4 => '#f58642',
			5 => '#f6a141',
			6 => '#fcc441',
			7 => '#e5cd42',
			8 => '#cbd244',
			9 => '#b3db47',
			10 => '9be549',
			11 => '#90d047',
		);
	}

	function get_options_smiles($type, $objectId = 0, $imageCount = 11)
	{
		$options = $this->get_options($type, $objectId);
		$colors = $this->get_options_smiles_colors();

		$optionsAsKeysSorted = array();
		if (in_array('0',$options) == true) {
			$sets = array(
				2 => array(1,11),
				3 => array(0,1,11),
				4 => array(0,1,6,11),
				5 => array(0,1,3,6,11),
				6 => array(0,1,3,6,8,11),
				7 => array(0,1,3,6,8,10,11),
				8 => array(0,1,2,3,4,6,8,10,11),
				9 => array(0,1,2,3,4,6,8,9,10,11),
				10 => array(0,1,2,3,4,5,6,7,8,10,11),
				11 => array(0,1,2,3,4,5,6,7,8,9,10,11),
			);
		} else {
			$sets = array(
				2 => array(1,11),
				3 => array(1,6,11),
				4 => array(1,3,6,11),
				5 => array(1,3,6,8,11),
				6 => array(1,3,6,8,10,11),
				7 => array(1,2,3,4,6,8,10,11),
				8 => array(1,2,3,4,6,8,9,10,11),
				9 => array(1,2,3,4,5,6,7,8,10,11),
				10 => array(1,2,3,4,5,6,7,8,9,10,11),
			);
		}

		foreach($options as $option) {
			$optionsAsKeysSorted[$option] = array();
		}

		ksort($optionsAsKeysSorted);
		$set = $sets[count($optionsAsKeysSorted)];

		foreach($optionsAsKeysSorted as $key => &$option) {
			$option = array(
				'img' => 'img/rating_smiles/' . $set[$key - 1] . '.png',
				'color' => $colors[$set[$key - 1]]
			);
		}

		return $optionsAsKeysSorted;
	}

	private function internal_refresh_list( $max )
	{
		$configurations = $this->get_initialized_configurations();
		$runner = $this->get_runner();
		
		$list = $ratingconfiglib->get_expired_object_list($max);

		foreach ( $list as $object ) {
			$this->internal_refresh_rating($object['type'], $object['object'], $runner, $configurations);
		}
	}

	private function internal_refresh_rating( $type, $object, $runner, $configurations )
	{
		global $ratingconfiglib; require_once 'lib/rating/configlib.php';
		$runner->setVariables(array('type' => $type, 'object-id' => $object));

		foreach ( $configurations as $config ) {
			try {
				$runner->setFormula($config['formula']);
				$result = $runner->evaluate();

				$ratingconfiglib->record_value($config, $type, $object, $result);
			} catch( Math_Formula_Exception $e ) {
				// Some errors are expected for type-specific configurations.
				// Skip safely. Sufficient validation is made on save to make sure
				// other errors will not happen.
			}
		}
	}

	private function get_runner()
	{
		require_once 'Math/Formula/Runner.php';
		return new Math_Formula_Runner(
						array(
							'Math_Formula_Function_' => 'lib/core/Math/Formula/Function',
							'Tiki_Formula_Function_' => dirname(__FILE__) . '/formula',
						)
		);
	}

	private function get_initialized_configurations()
	{
		if ($this->configurations) {
			return $this->configurations;
		}

		$ratingconfiglib = TikiLib::lib('ratingconfig');

		$parser = new Math_Formula_Parser;
		$configurations = array();
		foreach ( $ratingconfiglib->get_configurations() as $config ) {
			$config['formula'] = $parser->parse($config['formula']);
			$configurations[] = $config;
		}

		return $this->configurations = $configurations;
	}
}

global $ratinglib; $ratinglib = new RatingLib;

