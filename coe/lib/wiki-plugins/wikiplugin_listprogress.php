<?php

function wikiplugin_listprogress_info() {
	return array(
		'name' => tra('List Progress'),
		'description' => tra('Indicates the internationalization progress of the page list.'),
		'prefs' => array( 'feature_pagelist', 'wikiplugin_listprogress', 'feature_multilingual' ),
		'params' => array(
			'list' => array(
				'required' => true,
				'name' => tra('List Name'),
				'description' => tra('Name of the list as configured in the page list admin panel.'),
				'filer' => 'text',
			),
			'limit' => array(
				'required' => false,
				'name' => tra('Limit'),
				'description' => tra('Maximum amount of results to display.'),
				'filter' => 'digits',
			),
			'filter' => array(
				'required' => false,
				'name' => tra('Filter'),
				'description' => tra('Filter to apply to the page list.'),
				'filter' => 'text',
			),
			'scoreLimit' => array(
				'required' => false,
				'name' => tra('Score Limit'),
				'description' => tra('Value used as the denominator for the score.'),
				'filter' => 'digits',
			),
		),
	);
}

function wikiplugin_listProgress($data, $params) {
	global $pagelistlib, $locale, $prefs;

	if ( !isset($pagelistlib) || !is_object($pagelistlib) )
		include_once("lib/pagelist/pagelistlib.php");

	extract ($params, EXTR_SKIP);

	$pll = $pagelistlib;
	if ( !$list || !$pll->listTypeExists($list) )
		return "<b>'list' parameter missing or doesn't exist</b><br />";

	if ( !$lang ) $lang = $locale;
	if ( !$limit ) $limit = -1;
	if ( !$sort ) $sort = "score_desc";
	if ( !$style ) $style = "percentage";
	if ( !$scoreLimit ) $scoreLimit = 0;
	if ( !$weight) $weight = 1;
	if ( !$anchor || $anchor != 'none' ) $anchor = 1;
	if ( !$showText ) $showText = 0;  
	if ( !$filter || !in_array($filter, array("translated", "needs review", "needs translation", "needs updating", /*"draft",*/ "unknown") ) ) 
		$filter = "translated";  

	$progress = $pll->getl10nProgress($list, $lang, $limit, $sort);
	$listInfo = $pll->getListType($list);

	if ( $title ) $listInfo['title'] = $title;
	if ( $description ) $listInfo['description'] = $description;
	if ( $anchor != 'none' ) $listInfo['anchor'] = preg_replace('/\s+/', '_', $listInfo['title']); 

	if ( !$showText ) {
		unset($listInfo['title']);
		unset($listInfo['description']);
	}

	if ( $progress['total'] == 0 )
		return "";

	if ( $scoreLimit ) {
		$progress['percentage'] = min(100, ( $progress['scoreTotal'][$filter] / $scoreLimit ) * 100);  
	} else {
		$progress['percentage'] = min(100, ( ( $progress[$filter] / $progress['total'] ) / $weight ) * 100);
	}

	$displayStyles = array( 'percentage' =>  intval($progress['percentage']) . '% ' . tra('complete'),
			'named percentage' => intval($progress['percentage']) . '% ' . tra($filter),
			'ratio' => max(0, $progress[$filter]) . '/' . $progress['total'],
			'named ratio' => max(0, $progress[$filter]) . '/' . $progress['total'] . ' ' . tra('articles') . ' ' . tra($filter)                        
			);  

	$html = '<div class="listprogess-wrapper">';

	if ( isset($listInfo['title']) ) $html .= '<h2 class="pagelist-title"><a href="#' . $listInfo['anchor'] . '">' . tra($listInfo['title']) . '</a></h2>';

	$html .= '<div class="listprogress-progress">';
	$html .= '<span id="listprogress-bar"><em style="left: ' . ($progress['percentage'] * 2.53) . 'px;"></em></span>';
	$html .= '</div>';
	$html .= '<span class="listprogress-status">' . $displayStyles[$style] . '</span>';

	if ( isset($listInfo['description']) ) $html .= '<div class="pagelist-description">'. tra($listInfo['description']) . '</div>';

	$html .= '</div>';

	return $html; 
}
