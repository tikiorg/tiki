<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
	'storeAs' => 'int',
    )
  )
);

$section = 'search';
require_once ('tiki-setup.php');
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
$postfilter = isset($_REQUEST['postfilter']) ? $_REQUEST['postfilter'] : array();
$facets = array();

if (count($filter) || count($postfilter)) {
	if (isset($_REQUEST['save_query'])) {
		$_SESSION['quick_search'][(int) $_REQUEST['save_query']] = $_REQUEST;
	}
	$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
	$maxRecords = empty($_REQUEST['maxRecords'])?$prefs['maxRecords']: $_REQUEST['maxRecords'];

	if ($access->is_serializable_request(true)) {
		$jitRequest->replaceFilter('fields', 'word');
		$fetchFields = array_merge(array('title', 'modification_date', 'url'), $jitRequest->asArray('fields', ','));;

		$results = tiki_searchindex_get_results($filter, $postfilter, $offset, $maxRecords);

		$smarty->loadPlugin('smarty_function_object_link');
		$smarty->loadPlugin('smarty_modifier_sefurl');
		foreach ($results as &$res) {
			foreach ($fetchFields as $f) {
				if (isset($res[$f])) {
					$res[$f]; // Dynamic load if applicable
				}
			}
			$res['link'] = smarty_function_object_link(
				array(
					'type' => $res['object_type'],
					'id' => $res['object_id'],
					'title' => $res['title'],
				),
				$smarty
			);
			$res = array_filter(
				$res,
				function ($v) {
					return !is_null($v);
				}
			);	// strip out null values
		}
		$access->output_serialized(
			$results,
			array(
				'feedTitle' => tr('%0: Results for "%1"', $prefs['sitetitle'], isset($filter['content']) ? $filter['content'] : ''),
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
			$results = tiki_searchindex_get_results($filter, $postfilter, $offset, $maxRecords);
			$facets = array_map(
				function ($facet) {
					return $facet->getName();
				}, $results->getFacets()
			);

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
$smarty->assign('postfilter', $postfilter);
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
function tiki_searchindex_get_results($filter, $postfilter, $offset, $maxRecords)
{
	global $prefs;

	$unifiedsearchlib = TikiLib::lib('unifiedsearch');

	$query = new Search_Query;
	$unifiedsearchlib->initQueryBase($query);
	$query = $unifiedsearchlib->buildQuery($filter, $query);
	$query->filterContent('y', 'searchable');

	if (count($postfilter)) {
		$unifiedsearchlib->buildQuery($postfilter, $query->getPostFilter());
	}

	if (isset($_REQUEST['sort_mode']) && $order = Search_Query_Order::parse($_REQUEST['sort_mode'])) {
		$query->setOrder($order);
	}

	if ($prefs['storedsearch_enabled'] == 'y' && ! empty($_POST['storeAs'])) {
		$storedsearch = TikiLib::lib('storedsearch');
		$storedsearch->storeUserQuery($_POST['storeAs'], $query);
		TikiLib::lib('smarty')->assign('display_msg', tr('Your query was stored.'));
	}

	$unifiedsearchlib->initQueryPermissions($query);

	$query->setRange($offset, $maxRecords);

	if ($prefs['feature_search_stats'] == 'y') {
		$stats = TikiLib::lib('searchstats');
		foreach ($query->getTerms() as $term) {
			$stats->register_term_hit($term);
		}
	}

	if ($prefs['search_use_facets'] == 'y') {
		$provider = $unifiedsearchlib->getFacetProvider();

		foreach ($provider->getFacets() as $facet) {
			$query->requestFacet($facet);
		}
	}

	try {
		if ($prefs['federated_enabled'] == 'y' && ! empty($filter['content'])) {
			$fed = TikiLib::lib('federatedsearch');
			$fed->augmentSimpleQuery($query, $filter['content']);
		}

		$resultset = $query->search($unifiedsearchlib->getIndex());

		return $resultset;
	} catch (Search_Elastic_TransportException $e) {
		TikiLib::lib('errorreport')->report('Search functionality currently unavailable.');
	} catch (Exception $e) {
		TikiLib::lib('errorreport')->report($e->getMessage());
	}

	return new Search_ResultSet(array(), 0, 0, -1);
}

