<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.rating.php 53803 2015-02-06 00:42:50Z jyhem $

function smarty_function_multilike( $params, $smarty )
{
	global $prefs, $user;

	$relationlib = TikiLib::lib("relation");

	$multivalues = get_multivalues_from_pref($params['relation_prefix']);

	$item = array();
	foreach ($multivalues as $mv) {
		if ($mv['relation_prefix'] == $params['relation_prefix']) {
			$config = $mv;
			break;
		}
	}
	if (empty($config)) {
		return tr("Multivalue configuration not found");
	}

	$totalCount = 0;
	$totalPoints = 0;
	$buttons = array();
	foreach ($config['labels'] as $key=>$label) {
		$button = array();
		$button['index'] = $key;
		if (isset($config['values'])) {
			$button['value'] = $config['values'][$key];
		}
		$button['label'] = $label;
		$button['relation'] = $params['relation_prefix'].".".$button['index'];

		//get existing stats
		$button['count'] = $relationlib->get_relation_count($button['relation'], $params['type'], $params['object']);
		$totalCount += $button['count'];
		if ($button['value']) {
			$button['points'] = $button['count'] * $button['value'];
			$totalPoints += $button['points'];
		}

		// set whether already selected
		if ($relationlib->get_relation_id( $button['relation'], "user", $user, $params['type'], $params['object'] )) {
			$button['selected'] = 1;
		} else {
			$button['selected'] = 0;
		}
		$buttons[] = $button;
	}

	if(!empty($params['onlyShowTotalPoints'])) {
		return $totalPoints;
	}

	if(!empty($params['onlyShowTotalLikes'])) {
		return $totalCount;
	}

	if(!empty($params['showOptionTotals'])) {
		$smarty->assign("show_option_totals", true);
	}

	if(!empty($params['showPoints']) && strtolower($params['showPoints']) != 'n') {
		$smarty->assign("show_points", true);
	}

	if(!empty($params['showLikes']) && strtolower($params['showLikes']) == 'n') {
		$smarty->assign("show_likes", false);
	} else {
		$smarty->assign("show_likes", true);
	}

	if(!empty($params['choiceLabel'])) {
		$smarty->assign("choice_label", $params['choiceLabel']);
	} else {
		$smarty->assign("choice_label", "I found this:");
	}

	if(!empty($params['orientation']) && strtolower($params['orientation']) == 'vertical') {
		$smarty->assign("orientation", 'vertical');
	} else {
		$smarty->assign("orientation", 'horizontal');
	}

	$smarty->assign("buttons", $buttons);
	$smarty->assign("type", $params['type']);
	$smarty->assign("object", $params['object']);
	$smarty->assign("totalCount", $totalCount);
	$smarty->assign("totalPoints", $totalPoints);
	$smarty->assign("relation_prefix", $params['relation_prefix']);
	$smarty->assign("multilike_many", $config['allow_multi']);

	$smarty->assign("uses_values", isset($config['values']));

	$headerlib = TikiLib::lib('header');
	$headerlib->add_jsfile("lib/jquery_tiki/multilike.js");

	if (empty($params['template'])) {
		return $smarty->fetch('multilike.tpl');
	} else {
		return $smarty->fetch($params['template']);
	}
}

/**
 * @param $mv
 * @return array
 */
function get_multivalues_from_pref() {
	global $prefs;
	$data = (explode("\n\n",trim($prefs['user_multilike_config'])));
	$configurations = array();
	foreach ($data as $config) {
		preg_match_all("/(\S*)\s*=\s*(.*)/", $config, $temp_arr);
		$config = array_combine($temp_arr[1],$temp_arr[2]);
		if ($config['values']) {
			$config['values'] = array_map('trim', explode(',', $config['values']));
		}
		$config['labels'] = array_map('trim', explode(',', $config['labels']));
		$configurations[] = $config;
	}
	return $configurations;
}
