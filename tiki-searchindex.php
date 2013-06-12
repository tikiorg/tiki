<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = array(
  array( 'staticKeyFilters' => array(
    'date' => 'digits',
    'maxRecords' => 'digits',
    'highlight' => 'text',
    'where' => 'text',
    'find' => 'text',
    'searchLang' => 'word',
    'words' =>'text',
    'boolean' =>'word',
    )
  )
);

$section = 'search';
require_once ('tiki-setup.php');
require_once 'lib/search/searchlib-unified.php';
$access->check_feature('feature_search');
$access->check_permission('tiki_p_search');
//get_strings tra("Searchindex")
//ini_set('display_errors', true);
//error_reporting(E_ALL);

foreach (array('find', 'highlight', 'where') as $possibleKey) {
	if (empty($_REQUEST['filter']) && !empty($_REQUEST[$possibleKey])) {
		$_REQUEST['filter']['content'] = $_REQUEST[$possibleKey];
	}
}
$filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : array();
$facets = array();

if (count($filter)) {
	if (isset($_REQUEST['save_query'])) {
		$_SESSION['quick_search'][(int) $_REQUEST['save_query']] = $_REQUEST;
	}
	$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
	$maxRecords = empty($_REQUEST['maxRecords'])?$prefs['maxRecords']: $_REQUEST['maxRecords'];

	if ($access->is_serializable_request(true)) {
		$jitRequest->replaceFilter('fields', 'word');
		$fetchFields = array_merge(array('title', 'modification_date', 'url'), $jitRequest->asArray('fields', ','));;

		$results = tiki_searchindex_get_results($filter, $offset, $maxRecords);
		$dataSource = $unifiedsearchlib->getDataSource('formatting');
		$results = $dataSource->getInformation($results, $fetchFields);

		require_once 'lib/smarty_tiki/function.object_link.php';
		foreach ($results as &$res) {
			$res['link'] = smarty_function_object_link(
				array(
					'type' => $res['object_type'],
					'id' => $res['object_id'],
					'title' => $res['title'],
				),
				$smarty
			);
		}
		$access->output_serialized(
			$results,
			array(
				'feedTitle' => tr('%0: Results for "%1"', $prefs['sitetitle'], $request['filter']['content']),
				'feedDescription' => tr('Search Results'),
				'entryTitleKey' => 'title',
				'entryUrlKey' => 'url',
				'entryModificationKey' => 'modification_date',
				'entryObjectDescriptors' => array('object_type', 'object_id'),
			)
		);
		exit;
	} else {
		$cachelib = TikiLib::lib('cache');
		$cacheType = 'search';
		$cacheName = $user.'/'.$offset.'/'.$maxRecords.'/'.serialize($filter);
		$isCached = false;
		if (!empty($prefs['unified_user_cache']) && $cachelib->isCached($cacheName, $cacheType)) {
			list($date, $html) = $cachelib->getSerialized($cacheName, $cacheType);
			if ($date > $tikilib->now - $prefs['unified_user_cache'] * 60) {
				$isCached = true;
			}
		}
		if (!$isCached) {
			$results = tiki_searchindex_get_results($filter, $offset, $maxRecords);
			$facets = array_map(function ($facet) {
				return $facet->getName();
			}, $results->getFacets());
			$dataSource = $unifiedsearchlib->getDataSource('formatting');

			$plugin = new Search_Formatter_Plugin_SmartyTemplate(realpath('templates/searchresults-plain.tpl'));
			$plugin->setData(
				array(
					'prefs' => $prefs,
				)
			);
			$fields = array(
				'title' => null,
				'url' => null,
				'modification_date' => null,
				'highlight' => null,
			);
			if ($prefs['feature_search_show_visit_count'] === 'y') {
				$fields['visits'] = null;
			}
			$plugin->setFields($fields);

			$formatter = new Search_Formatter($plugin);
			$formatter->setDataSource($dataSource);

			$wiki = $formatter->format($results);
			$html = $tikilib->parse_data(
				$wiki,
				array(
					'is_html' => true,
				)
			);
			if (!empty($prefs['unified_user_cache'])) {
				$cachelib->cacheItem($cacheName, serialize(array($tikilib->now, $html)), $cacheType);
			}
		}
		$smarty->assign('results', $html);
	}
}

$smarty->assign('filter', $filter);
$smarty->assign('facets', $facets);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

if ($prefs['search_use_facets'] == 'y') {
	$smarty->display("tiki-searchfacets.tpl");
} else {
	$smarty->display("tiki-searchindex.tpl");
}

/**
 * @param $filter
 * @param $offset
 * @param $maxRecords
 * @return mixed
 */
function tiki_searchindex_get_results($filter, $offset, $maxRecords)
{
	global $prefs;
	
	$unifiedsearchlib = TikiLib::lib('unifiedsearch');
	$query = $unifiedsearchlib->buildQuery($filter);
	$query->setRange($offset, $maxRecords);

	if (isset($_REQUEST['sort_mode']) && $order = Search_Query_Order::parse($_REQUEST['sort_mode'])) {
		$query->setOrder($order);
	}

	if ($prefs['feature_search_stats'] == 'y') {
		$stats = TikiLib::lib('searchstats');
		foreach ($query->getTerms() as $term) {
			$stats->register_term_hit($term);
		}
	}

	$query->requestFacet(new Search_Query_Facet_Term('categories'));
	$query->requestFacet(new Search_Query_Facet_Term('language'));
	$query->requestFacet(new Search_Query_Facet_Term('object_type'));

	return $query->search($unifiedsearchlib->getIndex());
}

