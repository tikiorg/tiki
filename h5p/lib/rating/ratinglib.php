<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

	function get_vote_comment_author( $comment_author, $type, $objectId )
	{
		return $this->get_user_vote($comment_author, $type, $objectId);
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

		if (!empty($user)) {
			$this->query(
				'DELETE FROM `tiki_user_votings` WHERE `user` = ? AND `id` = ?',
				array($user, $token)
			);
		}

		$this->query(
			'INSERT INTO `tiki_user_votings` ( `user`, `ip`, `id`, `optionId`, `time` ) VALUES( ?, ?, ?, ?, ? )',
			array($user, $ip, $token, $score, $time)
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
		$options = $this->get_options($type, $objectId, false, $hasLabel);

        if($hasLabel){
            return array_key_exists($value, $options);
        }
		    return in_array($value, $options);

	}

	function get_options( $type, $objectId, $skipOverride = false, &$hasLabels = false )
	{
		$pref = 'rating_default_options';
        $expectedArray = true;
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
                $expectedArray = false;
				break;
		}

		global $tikilib,
               $prefs;

		$override = $this->get_override($type, $objectId);

		if (!empty($override) && $skipOverride == false) {

            $override = array_filter($override, "is_numeric");
			return $override;
		}

        $value = $prefs[$pref];

        if ( is_string($value) && strpos($value, '=') !== false ){
            $hasLabels = true;
            $parser = new WikiLingo\Utilities\Parameters\Parser();
            $parsedPref = $parser->parse($value);
            return $parsedPref;
        }

		$result = $tikilib->get_preference($pref, range(1, 5), ($expectedArray && is_array($value)));

        if ($expectedArray == true && !is_array($result)) {
            $result = explode(',', $value);
        }
        $result = array_filter($result, "is_numeric");
        return $result;
	}

	function set_override($type, $objectId, $value)
	{
		global $attributelib;
		$options = $this->override_array($type);

		$attributelib->set_attribute($type, $objectId, $type.".rating.override", $options[$value - 1]);
	}

	function get_override($type, $objectId)
	{
		$attributelib = TikiLib::lib('attribute');
		$attrs = $attributelib->get_attributes($type, $objectId);
		end($attrs);
		$key = key($attrs);
		if (empty($attrs) || empty($attrs[$key])) return;

		$attr = explode(',', $attrs[$type . '.rating.override']);
		return $attr;
	}


	function override_array($type, $maintainArray = false, $sort = false)
	{
		global $prefs;

		$array = array();
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


		$sortedPref = $prefs[$pref];
		asort($sortedPref);

		foreach ($sortedPref as $i => $option) {
			$options[$i] = $option;
			//Ensure there are at least 2 to choose from
			if (count($options) > 1) {
				$value = $options;

				if ($sort == false) ksort($value);

				if ($maintainArray == false) {
					$value = implode($value, ',');
				}

				$array[] = $value;
			}
		}

		return $array;
	}

	function votings($threadId, $type = 'comment', $normalize = false)
	{
		global $prefs;

		switch($type) {
			case 'wiki page': $type = 'wiki';
		}

		$user_votings = $this->fetchAll(
			"SELECT *
			FROM tiki_user_votings tuv1
			WHERE id=? AND time = (
				SELECT max(time)
				FROM tiki_user_votings tuv2
				WHERE tuv2.user = tuv1.user AND tuv1.id = tuv2.id
			)
			ORDER BY time DESC", array($type.$threadId)
		);

		$votings = array();
		$voteCount = count($user_votings);
		$percent = ( $voteCount > 0 ? 100 / $voteCount : 0 );
        $hasLabels = false;

		foreach ($user_votings as $user_voting) {
			if (!isset($votings[$user_voting['optionId']])) $votings[$user_voting['optionId']] = 0;

			$votings[$user_voting['optionId']]++;
		}

		$voteOptionsOverride = $this->get_options($type, $threadId, false, $hasLabels);
		ksort($voteOptionsOverride);
		$voteOptionsGeneral = $this->get_options($type, $threadId, true);
		ksort($voteOptionsGeneral);

        if ($hasLabels){
            ksort($voteOptionsOverride);
            $overrideMin = key($voteOptionsOverride);
            end($voteOptionsOverride);
            $overrideMax = key($voteOptionsOverride);
        }
        else
        {
            ksort($voteOptionsOverride);
            $overrideMin = reset($voteOptionsOverride);
            $overrideMax = end($voteOptionsOverride);
        }

		//$generalMin = (int)$voteOptionsGeneral[0];
		//$generalMax = (int)$voteOptionsGeneral[count($voteOptionsGeneral) - 1];

		$normalized = 0;
		foreach ($votings as $value => &$votes) {
			$normalized += ($overrideMin /  $overrideMax) * $value;

			$votes = array(
				"votes" => $votes,
				"percent" => round($percent * $votes),
			);
		}

		ksort($votings);

		if ($normalize == true) {
			return $normalized;
		}

		return $votings;
	}

	function get_user_vote( $user, $type, $objectId )
	{
		$result = $this->fetchAll(
			'SELECT `optionId` FROM `tiki_user_votings` WHERE `user` = ? AND `id` = ? ORDER BY `time` DESC',
			array($user, $this->get_token($type, $objectId)),
			1
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
		$sets = $this->get_options_smiles_id_sets();

		$backgroundsSets = array();

		foreach ($sets as $set) {
			$backgrounds = array();
			foreach ($set as $imageId) {
				$backgrounds[] = 'img/rating_smiles/' . $imageId . '.png';
			}
			$backgroundsSets[] = $backgrounds;
		}

		return $backgroundsSets;
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
			10 => '#9be549',
			11 => '#90d047',
		);
	}

	function get_options_smiles_id_sets()
	{
		return array(
            2 => array( 1=>1, 2=>11),
			3 => array( 0=>0, 1=>1, 2=>11),
			4 => array( 0=>0, 1=>1, 2=>6, 3=>11),
			5 => array( 0=>0, 1=>1, 2=>4, 3=>8, 4=>11),
			6 => array( 0=>0, 1=>1, 2=>4, 3=>6, 4=>8, 5=>11),
			7 => array( 0=>0, 1=>1, 2=>3, 3=>5, 4=>7,  5=>9, 6=>11),
			8 => array( 0=>0, 1=>1, 2=>2, 3=>3, 4=>6,  5=>9, 6=>10, 7=>11),
			9 => array( 0=>0,  1=>1, 2=>2, 3=>3, 4=>5,  5=>7,  6=>9, 7=>10, 8=>11),
			10 => array(0=>0, 1=>1, 2=>2, 3=>3, 4=>4,  5=>6,  6=>8,  7=>9, 8=>10, 9=>11),
			11 => array(0=>0, 1=>1, 2=>2, 3=>3, 4=>4,  5=>5,  6=>7,  7=>8,  8=>9, 9=>10, 10=>11),
			12 => array(0=>0, 1=>1, 2=>2, 3=>3, 4=>4,  5=>5,  6=>6,  7=>7,  8=>8,  9=>9, 10=>10, 11=>11),
		);
	}

	function get_options_smiles($type, $objectId = 0, $sort = false)
	{
		$options = $this->get_options($type, $objectId, false, $hasLabels);
		$colors = $this->get_options_smiles_colors();

		$optionsAsKeysSorted = array();


		foreach ($options as $key => &$option) {
			$optionsAsKeysSorted[$key] = array();
		}

		ksort($optionsAsKeysSorted);

		$sets = $this->get_options_smiles_id_sets();
		$set = $sets[count($options)];

		foreach ($optionsAsKeysSorted as $key => &$option) {
			$option = array(
				'img' => 'img/rating_smiles/' . $set[$key] . '.png',
				'color' => $colors[$set[$key]]
			);
		}

		if ($sort == false) {
			$result = array();
			foreach ($options as $key => &$option) {
				$result[$key] = $optionsAsKeysSorted[$key];
			}
		} else {
			$result = $optionsAsKeysSorted;
		}

		return $result;
	}

	private function internal_refresh_list( $max )
	{
		$configurations = $this->get_initialized_configurations();
		$runner = $this->get_runner();

		$ratingconfiglib = TikiLib::lib('ratingconfig');
		$list = $ratingconfiglib->get_expired_object_list($max);

		foreach ( $list as $object ) {
			$this->internal_refresh_rating($object['type'], $object['object'], $runner, $configurations);
		}
	}

	private function internal_refresh_rating( $type, $object, $runner, $configurations )
	{
		$ratingconfiglib = TikiLib::lib('ratingconfig');
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
		return new Math_Formula_Runner(
			array(
				'Math_Formula_Function_' => '',
				'Tiki_Formula_Function_' => '',
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

