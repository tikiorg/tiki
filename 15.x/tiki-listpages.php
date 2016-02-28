<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'wiki page';
$section_class = 'tiki_wiki_page manage';	// This will be body class instead of $section
require_once ('tiki-setup.php');

$auto_query_args = array(
				'initial',
				'maxRecords',
				'sort_mode',
				'find',
				'lang',
				'langOrphan',
				'findfilter_orphan',
				'categId',
				'category',
				'page_orphans',
				'structure_orphans',
				'exact_match',
				'hits_link_to_all_languages',
				'create_new_pages_using_template_name'
);

if ($prefs['gmap_page_list'] == 'y') {
	$smarty->assign('gmapbuttons', true);
} else {
	$smarty->assign('gmapbuttons', false);
}

if (isset($_REQUEST['mapview'])
		&& $_REQUEST['mapview'] == 'y'
		&& !isset($_REQUEST['searchmap'])
		&& !isset($_REQUEST['searchlist'])
		|| isset($_REQUEST['searchmap'])
		&& !isset($_REQUEST['searchlist'])
) {
	$smarty->assign('mapview', true);
}

if (isset($_REQUEST['mapview'])
			&& $_REQUEST['mapview'] == 'n'
			&& !isset($_REQUEST['searchmap'])
			&& !isset($_REQUEST['searchlist'])
			|| isset($_REQUEST['searchlist'])
			&& !isset($_REQUEST['searchmap'])
) {
	$smarty->assign('mapview', false);
}

if ($prefs['feature_multilingual'] == 'y' && isset($_REQUEST['lang']) && isset($_REQUEST['term_srch'])) {
	$multilinguallib = TikiLib::lib('multilingual');
	if (isset($_REQUEST['term_srch'])) {
		$multilinguallib->storeCurrentTermSearchLanguageInSession($_REQUEST['lang']);
	}
	$smarty->assign('template_name', $_REQUEST['create_new_pages_using_template_name']);
}

if (isset($_REQUEST['hits_link_to_all_languages']) && $_REQUEST['hits_link_to_all_languages'] == 'On') {
	$all_langs = 'y';
} else {
	$all_langs = '';
}
$smarty->assign('all_langs', $all_langs);

$access->check_feature(array('feature_wiki', 'feature_listPages'));

/* mass-remove:
the checkboxes are sent as the array $_REQUEST["checked[]"], values are the wiki-PageNames,
e.g. $_REQUEST["checked"][3]="HomePage"
$_REQUEST["submit_mult"] holds the value of the "with selected do..."-option list
we look if any page's checkbox is on and if remove_pages is selected.
then we check permission to delete pages.
if so, we call histlib's method remove_all_versions for all the checked pages.
*/
if (!empty($_REQUEST['submit_mult']) && isset($_REQUEST['checked'])) {
	$action = $_REQUEST['submit_mult'];
	check_ticket('list-pages');
	Perms::bulk(array( 'type' => 'wiki page' ), 'object', $_REQUEST['checked']);

	switch ($action) {
		case 'remove_pages':
			// Now check permissions to remove the selected pages
			$access->check_permission('tiki_p_remove');
			$access->check_authenticity(tr('Are you sure you want to remove the %0 selected pages?', count($_REQUEST['checked'])));
			foreach ($_REQUEST['checked'] as $check)
				$tikilib->remove_all_versions($check);
			break;

		case 'print_pages':
			$access->check_feature('feature_wiki_multiprint');
			foreach ($_REQUEST['checked'] as $check) {
				$access->check_page_exists($check);
				// Now check permissions to access this page
				$perms = Perms::get(array( 'type' => 'wiki page', 'object' => $check ));
				if (! $perms->view ) {
					$access->display_error($check, tra('You do not have permission to view this page.'), '403');
				}
				$access->check_authenticity(tr('Are you sure you want to print the %0 selected pages?', count($_REQUEST['checked'])));
				$page_info = $tikilib->get_page_info($check);
				$page_info['parsed'] = $tikilib->parse_data($page_info['data']);
				$page_info['h'] = 1;
				$multiprint_pages[] = $page_info;
			}
			break;

		case 'export_pdf':
			$access->check_feature('feature_wiki_multiprint');
			foreach ($_REQUEST['checked'] as $check) {
				$access->check_page_exists($check);
				// Now check permissions to access this page
				$perms = Perms::get(array( 'type' => 'wiki page', 'object' => $check ));
				if (! $perms->view ) {
					$access->display_error($check, tra('You do not have permission to view this page.'), '403');
				}

				$multiprint_pages[] = $check;
			}

			header('Location: tiki-print_multi_pages.php?display=pdf&printpages=' . urlencode(serialize($multiprint_pages)));
			die;

		case 'unlock_pages':
			$access->check_feature('feature_wiki_usrlock');
			$access->check_authenticity(tr('Are you sure you want to unlock the %0 selected pages?', count($_REQUEST['checked'])));
			$wikilib = TikiLib::lib('wiki');
			foreach ($_REQUEST['checked'] as $check) {
				$info = $tikilib->get_page_info($check);
				if ($info['flag'] == 'L'
						&& (
							$globalperms->admin_wiki == 'y'
							|| ($user && ($user == $info['lockedby']) || (!$info['lockedby'] && $user == $info['user']))
						)
				) {
					$wikilib->unlock_page($check);
				}
			}
			break;

		case 'lock_pages':
			$access->check_feature('feature_wiki_usrlock');
			$access->check_authenticity(tr('Are you sure you want to lock the %0 selected pages?', count($_REQUEST['checked'])));
			$wikilib = TikiLib::lib('wiki');
			foreach ($_REQUEST['checked'] as $check) {
				$info = $tikilib->get_page_info($check);
				$perms = Perms::get(array( 'type' => 'wiki page', 'object' => $check ));
				if ( $info['flag'] != 'L' && ( $globalperms->admin_wiki == 'y' || $perms->lock ) ) {
					$wikilib->lock_page($check);
				}
			}
			break;

		case 'zip':
			if ($globalperms->admin == 'y') {
				$access->check_authenticity(tr('Are you sure you want to download a zip of the %0 selected pages?', count($_REQUEST['checked'])));
				include_once ('lib/wiki/xmllib.php');
				$xmllib = new XmlLib;
				$zipFile = 'dump/xml.zip';
				$config['debug'] = false;
				if ($xmllib->export_pages($_REQUEST['checked'], null, $zipFile, $config)) {
					if (!$config['debug']) {
						header("location: $zipFile");
						die;
					}
				} else {
					$smarty->assign('error', $xmllib->get_error());
				}
			}
			break;

		case 'title':
			if ($tiki_p_admin == 'y') {
				$access->check_authenticity(tr('Are you sure you want to modify the %0 pages?', count($_REQUEST['checked'])).' '.tr('No history will be created.'));
				foreach ($_REQUEST['checked'] as $check) {
					$info = $tikilib->get_page_info($check);
					$info['data'] = "!$check\r\n" . $info['data'];
					$table = $tikilib->table('tiki_pages');
					$table->update(array('data' => $info['data']), array('page_id' => $info['page_id']));
				}
			}
			break;
	}
}

//add tablesorter sorting and filtering
$tsOn = Table_Check::isEnabled(true);
$smarty->assign('tsOn', $tsOn);
$tsAjax = Table_Check::isAjaxCall();
$smarty->assign('tsAjax', $tsAjax);
static $iid = 0;
++$iid;
$ts_tableid = 'listpages' . $iid;
$smarty->assign('ts_tableid', $ts_tableid);

if ($tsAjax) {
	if (!empty($_REQUEST['categPath_ts']) || !empty($_REQUEST['categ_ts'])) {
		if (!empty($_REQUEST['categPath_ts'])) {
			$req = $_REQUEST['categPath_ts'];
		} else {
			$req = $_REQUEST['categ_ts'];
		}
		$pos = strrpos($req, '::');
		if ($pos !== false) {
			$catname = substr($req, $pos + 2);
		} else {
			$catname = $req;
		}
		$categlib = TikiLib::lib('categ');
		$_REQUEST['categId'] = $categlib->get_category_id($catname);
	}
}

if (!empty($multiprint_pages)) {
	$smarty->assign('print_page', 'y');
	$smarty->assign_by_ref('pages', $multiprint_pages);
	// disallow robots to index page:
	$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
	// Display the template
	$smarty->display('tiki-print_multi_pages.tpl');
} else {
	// This script can receive the threshold
	// for the information as the number of
	// days to get in the log 1,3,4,etc
	// it will default to 1 recovering information for today
	if (isset($_REQUEST['maxRecords'])) {
		$maxRecords = $_REQUEST['maxRecords'];
	} else {
		$maxRecords = $maxRecords;
	}
	if (!isset($_REQUEST['sort_mode'])) {
		$sort_mode = $prefs['wiki_list_sortorder'] . '_' . $prefs['wiki_list_sortdirection'];
	} else {
		$sort_mode = $_REQUEST['sort_mode'];
	}
	$smarty->assign_by_ref('sort_mode', $sort_mode);
	// If offset is set use it if not then use offset =0
	// use the maxRecords php variable to set the limit
	// if sortMode is not set then use lastModif_desc
	if (!isset($_REQUEST['offset'])) {
		$offset = 0;
	} else {
		$offset = $_REQUEST['offset'];
	}
	$smarty->assign_by_ref('offset', $offset);
	if (!empty($_REQUEST['find'])) {
		$find = strip_tags($_REQUEST['find']);
	} else {
		if (!empty($_REQUEST['q'])) {
			$find = strip_tags($_REQUEST['q']);
		} else {
			$find = '';
		}
	}
	$smarty->assign('find', $find);
	$filter = '';

	if ($prefs['feature_multilingual'] == 'y') {
		$smarty->assign('find_lang', '');
		if (((!isset($_REQUEST['lang']) ) || (isset($_REQUEST['lang']) && $_REQUEST['lang'] != ''))) {
			$filter = setLangFilter($filter);
		}
		$smarty->assign('find_langOrphan', '');
		if (!empty($_REQUEST['langOrphan'])) {
			$filter['langOrphan'] = $_REQUEST['langOrphan'];
			$smarty->assign('find_langOrphan', $_REQUEST['langOrphan']);
		}
	}

	if ($prefs['feature_categories'] == 'y') {
		if (!empty($_REQUEST['cat_categories'])) {
			$filter['categId'] = $_REQUEST['cat_categories'];
			if (count($_REQUEST['cat_categories']) > 1) {
				unset($_REQUEST['categId']);
			} else {
				$_REQUEST['categId'] = $_REQUEST['cat_categories'][0];
			}
		} else {
			$_REQUEST['cat_categories'] = array();
		}

		$selectedCategories = $_REQUEST['cat_categories'];
		$smarty->assign('findSelectedCategoriesNumber', count($_REQUEST['cat_categories']));

		if (!empty($_REQUEST['category'])) {
			$categlib = TikiLib::lib('categ');
			$filter['categId'] = $categlib->get_category_id($_REQUEST['category']);
			$smarty->assign('find_categId', $filter['categId']);
			$selectedCategories = array((int) $filter['categId']);
		} elseif (!empty($_REQUEST['categId'])) {
			$filter['categId'] = $_REQUEST['categId'];
			$smarty->assign('find_categId', $_REQUEST['categId']);
			$selectedCategories = array((int) $filter['categId']);
		} else {
			$smarty->assign('find_categId', '');
		}

	}

	if ((!empty($_REQUEST['page_orphans']) && $_REQUEST['page_orphans'] == 'y')
			|| (isset($_REQUEST['findfilter_orphan']) && $_REQUEST['findfilter_orphan'] == 'page_orphans')
	) {
		$listpages_orphans = true;
	}

	if ($prefs['feature_listorphanPages'] == 'y') {
		if ((!empty($_REQUEST['page_orphans']) && $_REQUEST['page_orphans'] == 'y')
				|| (isset($_REQUEST['findfilter_orphan']) && $_REQUEST['findfilter_orphan'] == 'page_orphans')
		) {
			$filter_values['orphan'] = 'page_orphans';
		}
		$filters['orphan']['page_orphans'] = tra('Orphan pages');
	}
	if ($prefs['feature_wiki_structure'] == 'y') {
		if ((!empty($_REQUEST['structure_orphans']) && $_REQUEST['structure_orphans'] == 'y')
				|| (isset($_REQUEST['findfilter_orphan']) && $_REQUEST['findfilter_orphan'] == 'structure_orphans')
		) {
			$filter['structure_orphans'] = true;
		}

		if ($prefs['feature_listorphanStructure'] == 'y') {
			if ((!empty($_REQUEST['structure_orphans']) && $_REQUEST['structure_orphans'] == 'y')
					|| (isset($_REQUEST['findfilter_orphan']) && $_REQUEST['findfilter_orphan'] == 'structure_orphans')
			) {
				$filter_values['orphan'] = 'structure_orphans';
			}
			$filters['orphan']['structure_orphans'] = tra('Pages not in a structure');
		}
	}

	if (!empty($filters)) {
		$filter_names['orphan'] = tra('Type');
		$smarty->assign_by_ref('filters', $filters);
		$smarty->assign_by_ref('filter_names', $filter_names);
		$smarty->assign_by_ref('filter_values', $filter_values);
	}

	if (isset($_REQUEST['initial'])) {
		$initial = $_REQUEST['initial'];
	} else {
		$initial = '';
	}

	$smarty->assign('initial', $initial);
	if (isset($_REQUEST['exact_match'])) {
		$exact_match = true;
		$smarty->assign('exact_match', 'y');
	} else {
		$exact_match = false;
		$smarty->assign('exact_match', 'n');
	}
	// Get a list of last changes to the Wiki database
	//  $listpages_orphans must not be initialized here because it can already have received a value from another script
	if (!isset($listpages_orphans)) {
		$listpages_orphans = false;
	}
	$listpages = $tikilib->list_pages(
		$offset,
		$maxRecords,
		$sort_mode,
		$find,
		$initial,
		$exact_match,
		false,
		true,
		$listpages_orphans,
		$filter
	);

	possibly_look_for_page_aliases($find);
	// Only show the 'Actions' column if the user can do at least one action on one of the listed pages
	$show_actions = 'n';
	$actions_perms = array('tiki_p_edit', 'tiki_p_wiki_view_history', 'tiki_p_assign_perm_wiki_page', 'tiki_p_remove');
	foreach ($actions_perms as $p) {
		foreach ($listpages['data'] as $i) {
			if ($i['perms'][$p] == 'y') {
				$show_actions = 'y';
				break 2;
			}
		}
	}

	$smarty->assign('show_actions', $show_actions);
	// If there're more records then assign next_offset
	$cant_pages = ceil($listpages['cant'] / $maxRecords);
	$smarty->assign_by_ref('cant_pages', $cant_pages);
	$smarty->assign('actual_page', 1 + ($offset / $maxRecords));
	if ($listpages['cant'] > ($offset + $maxRecords))
		$smarty->assign('next_offset', $offset + $maxRecords);
	else
		$smarty->assign('next_offset', -1);

	// If offset is > 0 then prev_offset
	if ($offset > 0)
		$smarty->assign('prev_offset', $offset - $maxRecords);
	else
		$smarty->assign('prev_offset', -1);

	if ($prefs['feature_categories'] == 'y') {
		$categlib = TikiLib::lib('categ');
		$categories = $categlib->getCategories();
		$smarty->assign('notable', 'y');
		$smarty->assign('cat_tree', $categlib->generate_cat_tree($categories, true, $selectedCategories));
		$smarty->assign_by_ref('categories', $categories);
	}

	if ($prefs['feature_multilingual'] == 'y') {
		$languages = array();
		$langLib = TikiLib::lib('language');
		$languages = $langLib->list_languages(false, 'y');
		$smarty->assign_by_ref('languages', $languages);
	}

	if ($prefs['gmap_page_list'] == 'y') {
		// Generate Google map plugin data
		global $gmapobjectarray;
		$gmapobjectarray = array();
		foreach ($listpages['data'] as $p) {
			$gmapobjectarray[] = array('type' => 'wiki page',
				'id' => $p['pageName'],
				'title' => $p['pageName'],
				'href' => 'tiki-index.php?page=' . urlencode($p['pageName']),
			);
		}
	}

	foreach ($listpages['data'] as & $p) {
		if ($userlib->object_has_one_permission($p['pageName'], 'wiki page')) {
			$p['perms_active'] = 'y';
		} else {
			$p['perms_active'] = 'n';
		}
	}

	$smarty->assign_by_ref('listpages', $listpages['data']);
	$smarty->assign_by_ref('cant', $listpages['cant']);
	ask_ticket('list-pages');
	include_once ('tiki-section_options.php');
	// disallow robots to index page:
	$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

	// Exact match and single result, go to page directly
	if ( count($listpages['data']) == 1 && !$tsAjax) {
		$result = reset($listpages['data']);
		if ( TikiLib::strtolower($find) == TikiLib::strtolower($result['pageName']) ) {
			$wikilib = TikiLib::lib('wiki');
			header('Location: ' . $wikilib->sefurl($result['pageName'], '', $all_langs));
			exit;
		}
	}

	if ($tsOn && !$tsAjax) {
		//create dropdown lists for category name and path filters
		$cnames = array();
		$cpaths = array();
		if (isset($categories) && count($categories) > 0) {
			foreach($categories as $c) {
				$cnames[] = $c['name'];
				$cpaths[] = $c['categpath'];
			}
		}
		//set language dropdown showing only languages that pages are actually in
		if (isset($languages) && count($languages) > 0) {
			$pagelangs = array_unique($tikilib->table('tiki_pages')->fetchColumn('lang', array()));
			$pagelangs = array_flip($pagelangs);
			$temp_langs = array_column($languages, 'name', 'value');
			$temp_langs = array_intersect_key($temp_langs, $pagelangs);
			if (count($temp_langs) > 0) {
				foreach ($temp_langs as $short => $long) {
					$ts_langs[$short . '|' . $long] = $long;
				}
			} else {
				$ts_langs = array();
			}
		} else {
			$ts_langs = array();
		}
		$settings = array(
			'id' => $ts_tableid,
			'total' 	=> $listpages['cant'],
			'vars'	=> array(
				'show_actions' => $show_actions,
			),
			'columns'	=> array(
				'#language'	=> array(
					'filter' => array(
						'options' => $ts_langs,
					),
				),
				'#categories'	=> array(
					'filter' => array(
						'options' => $cnames,
					),
				),
				'#catpaths'	=> array(
					'filter' => array(
						'options' => $cpaths,
					),
				),
			),
		);
		Table_Factory::build('TikiListpages', $settings);
	}

	if ($access->is_serializable_request()) {
		if (isset($_REQUEST['listonly']) && ($prefs['feature_jquery'] == 'y' && $prefs['feature_jquery_autocomplete'] == 'y')) {
			$pages = array();
			foreach ($listpages['data'] as $page) {
				if (isset($_REQUEST['nonamespace'])) {
					$pages[] = TikiLib::lib('wiki')->get_without_namespace($page['pageName']);
				} else {
					$pages[] = $page['pageName'];
				}
			}	
			$access->output_serialized($pages);
		} else {
			$pages = array();
			$wikilib = TikiLib::lib('wiki');
			foreach ($listpages['data'] as $page) {
				$pages[] = array(
						'page_id' => $page['page_id'],
						'page_name' => $page['pageName'],
						'url' => $wikilib->sefurl($page['pageName']), 'version' => $page['version'],
						'description' => $page['description'],
						'last_modif' => date('Y-m-d H:i:s', $page['lastModif']),
						'last_author' => $page['user'],
						'creator' => $page['creator'],
						'creation_date' => date('Y-m-d H:i:s', $page['created']),
						'lang' => $page['lang'],
				);
			}
			require_once 'lib/ointegratelib.php';
			$response = OIntegrate_Response::create(array('list' => $pages), '1.0');
			$response->addTemplate('smarty', 'tikiwiki', 'files/templates/listpages/smarty-tikiwiki-1.0-shortlist.txt');
			$response->schemaDocumentation = 'http://dev.tiki.org/WebserviceListpages';
			$response->send();
		}
	} else {
		// Display the template
		if ($tsAjax) {
			$smarty->display($listpages_orphans ? 'tiki-orphan_pages.tpl' : 'tiki-listpages.tpl');
		} else {
			$smarty->assign('mid', ($listpages_orphans ? 'tiki-orphan_pages.tpl' : 'tiki-listpages.tpl'));
			$smarty->display('tiki.tpl');
		}
	}
}

/**
 * @param $filter
 * @return mixed
 */
function setLangFilter($filter)
{
	global $prefs;
	$multilinguallib = TikiLib::lib('multilingual');
	$smarty = TikiLib::lib('smarty');

	$lang = $multilinguallib->currentPageSearchLanguage();
	if (isset($_REQUEST['listonly']) && $prefs['feature_jquery_autocomplete'] == 'y' && strlen($lang) > 2) {
		$lang = substr($lang, 0, 2);		// for autocomplete - use only language filter, not culture as well
	}
	// Without this condition, default listing is empty and language filter shows any language
	if ($lang == 'en-us') {
		$lang = 'en';
	}
	$filter['lang'] = $lang;
	$smarty->assign('find_lang', $lang);
	return $filter;
}

/**
 * @param $query
 */
function possibly_look_for_page_aliases($query)
{
	global $prefs;
	$smarty = TikiLib::lib('smarty');

	$lang = NULL;
	if (isset($_REQUEST['lang'])) {
		$lang = $_REQUEST['lang'];
	}

	if ($prefs['feature_wiki_pagealias'] == 'y' && $query) {
		$semanticlib = TikiLib::lib('semantic');
		$aliases = $semanticlib->getAliasContaining($query, false, $lang);
		$smarty->assign('aliases', $aliases);
	} else {
		$smarty->assign('aliases', null);
	}

	if (!empty($aliases) > 0) {
		$smarty->assign('aliases_were_found', 'y');
	} else {
		$smarty->assign('aliases_were_found', 'n');
	}

	$alias_found = 'n';
	if (!empty($aliases)) {
		foreach ($aliases as $an_alias_info) {
			if ($an_alias_info['toPage'] == $query) {
				$alias_found = 'y';
			}
		}
	}
	$smarty->assign('alias_found', $alias_found);

	set_category_for_new_page_creation();
}

function set_category_for_new_page_creation()
{
	global $prefs;
	$smarty = TikiLib::lib('smarty');

	$create_page_with_categId = '';
	if (isset($_REQUEST['create_page_with_search_category'])) {
		if ($prefs['feature_categories'] == 'y' && !empty($_REQUEST['categId'])) {
			$create_page_with_categId = $_REQUEST['categId'];
		}
	}
	$smarty->assign('create_page_with_categId', $create_page_with_categId);
}
