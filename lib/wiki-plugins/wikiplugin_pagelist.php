<?php
 
function wikiplugin_pagelist_info() {
	return array(
		'name' => tra('Page List'),
		'description' => tra('List pages part of a named list.'),
		'prefs' => array( 'wikiplugin_pagelist', 'feature_pagelist', 'feature_multilingual' ),
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
			'start' => array(
				'required' => false,
				'name' => tra('Offset'),
				'description' => tra('Pagination start element.'),
				'filter' => 'digits',
			),
			'filter' => array(
				'required' => false,
				'name' => tra('Filter'),
				'description' => tra('Filter to apply to the page list.'),
				'filter' => 'text',
			),
			'style' => array(
				'required' => false,
				'name' => tra('Style'),
				'description' => tra('?'),
				'filter' => 'word',
			),
		),
	);
}

function wikiplugin_pagelist($data, $params) {
	global $pagelistlib, $locale, $prefs;

	if ( !isset($pagelistlib) || !is_object($pagelistlib) )
		include_once("lib/pagelist/pagelistlib.php");

	extract ($params, EXTR_SKIP);

	$pll = $pagelistlib;
	if ( !$list || !$pll->listTypeExists($list) )
		return '<b>' . tra('list parameter missing or doesn\'t exist') . '</b><br />';

	if ( !$lang ) $lang = $locale;
	if ( !$offset ) $offset = -1;
	if ( !$limit ) $limit = -1;
	if ( !$sort ) $sort = "priority_asc";
	if ( !$style ) $style = "table";
	if ( !$filter ) $filter = "";
	if ( !$hide ) $hide = "";
	if ( !$showText ) $showText = 0;
	if ( !$scoreLimit) $scoreLimit = 0;  

	$pages = $pll->getl10nList($list, $lang, $offset, $limit, $sort, $filter, $scoreLimit);
	$pages['header'] = array( "page_name" => tra("Title"), "score" => tra("Page Hits"), "status" => tra("Status") );

	if ( !count($pages) )
		return "";

	$listInfo = $pll->getListType($list);
	$listInfo['hides'] = explode("|", $hide);
	$listInfo['anchor'] = preg_replace('/\s+/', '_', $listInfo['title']); 
	if ( $title ) $listInfo['title'] = $title;
	if ( $description ) $listInfo['description'] = $description;

	if ( !$showText ) {
		unset($listInfo['title']);
		unset($listInfo['description']);
	}

	return listFormatHandler($pages, $listInfo, $style);
}

function listFormatHandler($pages, $listInfo, $style="table") {
	$listHandlers = array( "table", "ol", "ul" );
	$handlerPrefix = "listFormatHandler";

	if ( !in_array($style, $listHandlers) )
		$style = "table";

	if ( $style != "table" ) {
		$listInfo['type'] = $style;
		$style = "list";

		if (isset($pages['header'])) unset($pages['header']);
	}

	$handler = $handlerPrefix . ucfirst($style);
	return call_user_func_array($handler, array($pages, $listInfo));
}
  
function listFormatHandlerTable($pages, $listInfo) {
	if ( !count($pages) )
		return "";    

	$hides = $listInfo['hides'];
	$actionLinks = getActionLinks();

	$html = '<div class="pagelist-wrapper">';    
	$html .= getListInfoHTML($listInfo);  
	$html .= '<table class="pagelist-table">';

	if ( isset($pages['header']) ) {
		$html .= '<tr class="pagelist-heading odd">';     
		foreach( $pages['header'] as $column => $heading )
			if ( !in_array($column, $hides))       
				$html .= '<td class="pagelist-' . $column .'">' . ucfirst($heading) . '</td>';
		$html .= "</tr>";
		unset($pages['header']);
	}
	$trClass = "even";  

	foreach ( $pages as $page ) {
		$page['display_status'] = formatStatusForDisplay($page['status']);

		$html .= '<tr class="' . $trClass . '">';
		$html .= '<td class="pagelist-page"><a href="tiki-index.php?page=' . urlencode($page['local_page_name']) . '" class="wiki">' . $page['local_page_name'] . '</a></td>';

		if ( !in_array('score', $hides) )
			$html .= '<td>' . tra(sprintf("%01.2f", $page['score'])) . tra('%') . '</td>';

		if ( !in_array('status', $hides)) {
			$html .= '<td class="pagelist-status pagelist-' . str_replace(" ", "-", $page['status']) . '">';
			$html .= '<a href="'. $actionLinks[$page['status']] . urlencode($page['local_page_name']) .'" class="wiki">' . tra($page['display_status']) . '</a></td>';
		}

		$html .= "</tr>";
		$trClass = ($trClass == "odd") ? "even" : "odd";
	}
	$html .="</table>";
	$html .="</div>";

	return $html;
}

function listFormatHandlerList($pages, $listInfo) {
	if ( !count($pages) )
		return "";    

	$hides = $listInfo['hides'];
	$actionLinks = getActionLinks();

	$html = '<div class="pagelist-wrapper">';    
	$html .= getListInfoHTML($listInfo);
	$html .= '<' . $listInfo['type'] . ' class="pagelist-list pagelist-' . $listInfo['type'] . '">';

	foreach ( $pages as $page ) {
		$page['display_status'] = formatStatusForDisplay($page['status']);    

		$html .= '<li><a href="tiki-index.php?page=' . urlencode($page['local_page_name']) . '" class="wiki">' . $page['local_page_name'] . '</a>';

		if ( !in_array('score', $hides) )
			$html .= ' (' . sprintf("%01.2f", $page['score']) . tra('% of page hits') .') ';

		if ( !in_array('status', $hides)) {
			$html .= ' -- <a href="'. $actionLinks[$page['status']] . urlencode($page['local_page_name']) .'" class="wiki">' . tra($page['display_status']) . '</a>';
		}

		$html .= "</li>";
	}
	$html .= '</' . $listInfo['type'] . '>';
	$html .="</div>";

	return $html;
}

function getListInfoHTML($listInfo) {
	$html = "";  

	if ( isset($listInfo['title']) ) $html .= '<h2 class="pagelist-title" id="' . $listInfo['anchor'] . '">' . tra($listInfo['title']) . '</h2>';
	if ( isset($listInfo['description']) ) $html .= '<div class="pagelist-description">'. tra($listInfo['description']) . '</div>';

	return $html;
}

function getActionLinks() {
	global $prefs;

	return array( 
		'translated' => 'tiki-index.php?page=',
		'needs review' => 'tiki-index.php?page=' . $prefs['wikiapproval_prefix'],
		'needs translation' => 'tiki-edit_translation.php?page=',
		'needs updating' => 'tiki-index.php?page=' . $prefs['wikiapproval_prefix'],
		'draft' => 'tiki-index.php?page=',
		'unknown' => ""
	);
}

function formatStatusForDisplay($status) {
	$status = ucfirst($status);
	if ( $status == 'Draft') $status = 'Needs review';

	return $status;
}
