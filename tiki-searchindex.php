<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = array(
  array( 'staticKeyFilters' => array(
    'date' => 'digits',
    'maxRecords' => 'digits',
    'highlight' => 'text',
    'where' => 'word',
    'searchLang' => 'word',
    'words' =>'text',
    'boolean' =>'word',
    )
  )
);

$section = 'search';
require_once ('tiki-setup.php');
require_once 'lib/search/searchlib-unified.php';
$smarty->assign('headtitle', tra('Search'));

$access->check_feature('feature_search');
$access->check_permission('tiki_p_search');

//ini_set('display_errors', true);
//error_reporting(E_ALL);

if (empty($_REQUEST['filter']) && !empty($_REQUEST['find'])) {
	$_REQUEST['filter']['content'] = $_REQUEST['find'];
	if (!empty($_REQUEST['where']))
		$_REQUEST['filter']['type'] = $_REQUEST['where'];
}
$filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : array();

if (count($filter)) {
	if (isset($_REQUEST['save_query'])) {
		$_SESSION['quick_search'][(int) $_REQUEST['save_query']] = $_REQUEST;
	}

	$query = $unifiedsearchlib->buildQuery($filter);

	$query->setRange(isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0, $prefs['maxRecords']);

	$results = $query->search($unifiedsearchlib->getIndex());

	$dataSource = $unifiedsearchlib->getDataSource('formatting');

	if ($access->is_serializable_request(true)) {
		$results = $dataSource->getInformation($results, array('title', 'modification_date', 'url'));

		require_once 'lib/smarty_tiki/function.object_link.php';
		foreach ($results as &$res) {
			$res['link'] = smarty_function_object_link(array(
				'type' => $res['object_type'],
				'id' => $res['object_id'],
				'title' => $res['title'],
			), $smarty);
		}
		$access->output_serialized($results, array(
			'feedTitle' => tr('%0: Results for "%1"', $prefs['sitetitle'], $request['filter']['content']),
			'feedDescription' => tr('Search Results'),
			'entryTitleKey' => 'title',
			'entryUrlKey' => 'url',
			'entryModificationKey' => 'modification_date',
			'entryObjectDescriptors' => array('object_type', 'object_id'),
		));
		exit;
	} else {
		$plugin = new Search_Formatter_Plugin_SmartyTemplate(realpath('templates/searchresults-plain.tpl'));
		$plugin->setData(array(
			'prefs' => $prefs,
		));
		$plugin->setFields(array(
			'title' => null,
			'url' => null,
			'modification_date' => null,
			'highlight' => null,
		));

		$formatter = new Search_Formatter($plugin);
		$formatter->setDataSource($dataSource);

		$wiki = $formatter->format($results);

		$smarty->assign('results', $tikilib->parse_data($wiki, array(
			'is_html' => true,
		)));
	}
}

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-searchindex.tpl');
$smarty->display("tiki.tpl");
