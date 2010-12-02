<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
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

$filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : array();

$query = $unifiedsearchlib->buildQuery($filter);

$query->setRange(isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0, $prefs['maxRecords']);

$results = $query->search($unifiedsearchlib->getIndex());

$plugin = new Search_Formatter_Plugin_SmartyTemplate(realpath('templates/searchresults-plain.tpl'));
$plugin->setData(array(
	'prefs' => $prefs,
));
$plugin->setFields(array(
	'title' => null,
	'modification_date' => null,
));

$formatter = new Search_Formatter($plugin);
$formatter->setDataSource($unifiedsearchlib->getDataSource());

$wiki = $formatter->format($results);

$smarty->assign('results', $tikilib->parse_data($wiki));

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-searchindex.tpl');
$smarty->display("tiki.tpl");
