<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_listpages_info()
{
	return array(
		'name' => tra('List Pages'),
		'documentation' => 'PluginListpages',
		'description' => tra('List pages based on various criteria'),
		'prefs' => array('wikiplugin_listpages', 'feature_listPages'),
		'iconname' => 'copy',
		'introduced' => 2,
		'params' => array(
			'offset' => array(
				'required' => false,
				'name' => tra('Result Offset'),
				'description' => tra('Result number at which the listing should start.'),
				'since' => '2.0',
				'filter' => 'digits',
				'default' => 0,
			),
			'max' => array(
				'required' => false,
				'name' => tra('Max'),
				'description' => tra('Limit number of items displayed in the list. Default is to display all.'),
				'since' => '2.0',
				'filter' => 'int',
				'default' => -1,
			),
			'initial' => array(
				'required' => false,
				'name' => tra('Initial'),
				'description' => tra('Initial page to show'),
				'since' => '2.0',
				'default' => '',
			),
			'showNameOnly' => array(
				'required' => false,
				'name' => tra('Show Name Only'),
				'description' => tra('Show only the page names'),
				'since' => '2.0',
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'categId' => array(
				'required' => false,
				'name' => tra('Category Filter'),
				'description' => tra('Filter categories by Id numbers. Use different separators to filter as follows:').'<br />'
					. '<code>:</code> - ' . tr('Page is in any of the specified categories. Example:') . ' <code>1:2:3</code><br />'
					. '<code>+</code> - ' . tr('Page must be in all of the specified categories. Example:') . ' <code>1+2+3</code><br />'
					. '<code>-</code> - ' .tr('Page is in the first specified category and not in any of the others. Example:')
						. ' <code>1-2-3</code><br />',
				'since' => '2.0',
				'filter' => 'text',
				'accepted' => tra('Valid category ID or list separated by :, + or -'),
				'default' => '',
				'profile_reference' => 'category',
			),
			'structHead' => array(
				'required' => false,
				'name' => tra('Structure Head'),
				'description' => tra('Filter by structure head'),
				'since' => '2.0',
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showPageAlias' => array(
				'required' => false,
				'name' => tra('Show Page Alias'),
				'description' => tra('Show page alias in the list'),
				'since' => '2.0',
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'includetag' => array(
				'required' => false,
				'name' => tra('Include Tag'),
				'description' => tr('Only pages with specific tag (separate tags using %0)', '<code>;</code>'),
				'since' => '10.3',
				'advanced' => true,
			),
			'excludetag' => array(
				'required' => false,
				'name' => tra('Exclude Tag'),
				'description' => tr('Only pages with specific tag excluded (separate tags using %0)', '<code>;</code>'),
				'since' => '10.3',
				'advanced' => true,
			),
			'showNumberOfPages' => array(
				'required' => false,
				'name' => tra('Show Number of Pages'),
				'description' => tra('Show the number of pages matching criteria'),
				'since' => '10.3',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				),
				'advanced' => true,
			),
			'find' => array(
				'required' => false,
				'name' => tra('Find'),
				'description' => tra('Only pages with names similar to the text entered for this parameter will be listed'),
				'since' => '2.0',
			),
			'lang' => array(
				'required' => false,
				'name' => tra('Language'),
				'description' => tra('Two-letter language code to filter pages listed.'),
				'since' => '3.0',
				'filter' => 'alpha',
			),
			'langOrphan' => array(
				'required' => false,
				'name' => tra('Orphan Language'),
				'description' => tra('Two-letter language code to filter pages listed. Only pages not available in the
					provided language will be listed.'),
				'since' => '3.0',
				'filter' => 'alpha',
			),
			'translations' => array(
				'required' => false,
				'name' => tra('Load Translations'),
				'description' => tra('User- or pipe-separated list of two-letter language codes for additional languages
					to display. If the language parameter is not defined, the first element of this list will be used
					as the primary filter.'),
				'since' => '3.0',
			),
			'translationOrphan' => array(
				'required' => false,
				'name' => tra('No translation'),
				'description' => tra('User- or pipe-separated list of two-letter language codes for additional languages
					to display. List pages with no language or with a missing translation in one of the language'),
				'since' => '7.0',
			),
			'exact_match' => array(
				'required' => false,
				'name' => tra('Exact Match'),
				'description' => tra('Page name and text entered for the filter parameter must match exactly to be listed'),
				'since' => '2.0',
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'only_orphan_pages' => array(
				'required' => false,
				'name' => tra('Only Orphan Pages'),
				'description' => tra('Only list orphan pages'),
				'since' => '2.0',
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'for_list_pages' => array(
				'required' => false,
				'name' => tra('For List Pages'),
				'description' => '',
				'since' => '2.0',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'sort' => array(
				'required' => false,
				'name' => tra('Sort'),
				'description' => tra('Sort ascending or descending on any field in the tiki_pages table. Syntax is
					field name followed by _asc or _desc. Two examples:')
					. ' <code>lastModif_desc</code> <code>pageName_asc</code>',
				'since' => '2.0',
				'filter' => 'text',
				'default' => 'pageName_asc',
			),
			'start' => array(
				'required' => false,
				'name' => tra('Start'),
				'description' => tra('When only a portion of the page should be included, specify the marker from which
					inclusion should start.'),
				'since' => '5.0',
				'default' => '',
			),
			'end' => array(
				'required' => false,
				'name' => tra('Stop'),
				'description' => tra('When only a portion of the page should be included, specify the marker at which
					inclusion should end.'),
				'since' => '5.0',
				'default' => '',
			),
			'length' => array(
				'required' => false,
				'name' => tra('Length'),
				'description' => tra('Number of characters to display'),
				'since' => '5.0',
				'filter' => 'int',
				'default' => '',
			),
			'showCheckbox' => array(
				'required' => false,
				'name' => tra('Checkboxes'),
				'description' => 'Option to show checkboxes',
				'since' => '7.0',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			)
		)
	);
}

function wikiplugin_listpages($data, $params)
{
	global $prefs, $tiki_p_view;
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');

	if ( isset($prefs) ) {
		// Handle 1.10.x prefs
		$feature_listPages = $prefs['feature_listPages'];
		$feature_wiki = $prefs['feature_wiki'];
	} else {
		// Handle 1.9.x prefs
		global $feature_listPages, $feature_wiki;
	}

	if ( $feature_wiki != 'y' || $feature_listPages != 'y' || $tiki_p_view != 'y') {
		// the feature is disabled or the user can't read wiki pages
		return '';
	}
	$default = array(
		'offset'=>0,
		'max'=>-1,
		'sort'=>'pageName_asc',
		'find'=>'',
		'start'=>'',
		'end'=>'',
		'length'=>-1,
		'translations'=>null,
		'translationOrphan'=>null,
		'showCheckbox' => 'y',
		'showNumberOfPages' => 'n',
		'for_list_pages' => 'y',
	);
	$params = array_merge($default, $params);
	extract($params, EXTR_SKIP);
	$filter = array();
	if (!isset($initial)) {
		if (isset($_REQUEST['initial'])) {
			$initial = $_REQUEST['initial'];
		} else {
			$initial = '';
		}
	}
	if (!empty($categId)) {
		if (strstr($categId, ':')) {
			$filter['categId'] = explode(':', $categId);
		} elseif (strstr($categId, '+')) {
			$filter['andCategId'] = explode('+', $categId);
		} elseif (strstr($categId, '-')) {
			$categories = explode('-', $categId);
			$filter['categId'] = array_shift($categories);
			$filter['notCategId'] = $categories;
		} else {
			$filter['categId'] = $categId;
		}
	}
	if (!empty($structHead) && $structHead == 'y') {
		$filter['structHead'] = $structHead;
	}
	if (!empty($translations) && $prefs['feature_multilingual'] == 'y') {
		$multilinguallib = TikiLib::lib('multilingual');
		if ($translations == 'user') {
			$translations = $multilinguallib->preferredLangs();
		} else {
			$translations = explode('|', $translations);
		}
	}
	if (!empty($translationOrphan)) {
		$filter['translationOrphan'] = explode('|', $translationOrphan);
	}
	if (!empty($langOrphan)) {
		$filter['langOrphan'] = $langOrphan;
	}
	if (!empty($lang)) {
		$filter['lang'] = $lang;
	} elseif (is_array($translations)) {
		$lang = $filter['lang'] = reset($translations);
	}
	$exact_match = ( isset($exact_match) && $exact_match == 'y' );
	$only_name = ( isset($showNameOnly) && $showNameOnly == 'y' );
	$only_orphan_pages = ( isset($only_orphan_pages) && $only_orphan_pages == 'y' );
	$for_list_pages = ( isset($for_list_pages) && $for_list_pages == 'y' );
	$only_cant = false;
	$listpages = $tikilib->list_pages($offset, $max, $sort, $find, $initial, $exact_match, $only_name, $for_list_pages, $only_orphan_pages, $filter, $only_cant);

	if (!empty($includetag) || !empty($excludetag)) {
		if (preg_match('/;/', $includetag)) {
			$aIncludetag = explode(';', $includetag);
		} else {
			$aIncludetag[] = $includetag;
		}
		if (preg_match('/;/', $excludetag)) {
			$aExcludetag = explode(';', $excludetag);
		} else {
			$aExcludetag[] = $excludetag;
		}
		$freetaglib = TikiLib::lib('freetag');
		$i = 0;

		foreach ( $listpages['data'] as $page ) {
			$bToRemove = true;
			$aListTags = $freetaglib->get_tags_on_object($page['pageName'], 'wiki page');
			if (!empty($aListTags['cant'])) {
				foreach ($aListTags['data'] as $aListTag) {
					if (in_array($aListTag['tag'], $aExcludetag) && !empty($aExcludetag[0])) {
						unset($listpages['data'][$i]);
						break;
					}
					if (in_array($aListTag['tag'], $aIncludetag) === true && !empty($aIncludetag[0])) {
						$bToRemove = false;
					}
				}
			} elseif (!empty($aIncludetag[0])) {
				unset($listpages['data'][$i]);
			}
			if ($bToRemove && !empty($aIncludetag[0])) {
				unset($listpages['data'][$i]);
			}
			$i++;
		}
		sort($listpages['data']);
		unset($aIncludetag);
		unset($aExcludetag);
	}

	if ( is_array($translations) ) {
		$used = array();
		foreach ( $listpages['data'] as &$page ) {
			$pages = $multilinguallib->getTranslations('wiki page', $page['page_id']);

			$page['translations'] = array();
			foreach ( $pages as $trad )
				if ( $trad['lang'] != $lang && in_array($trad['lang'], $translations) ) {
					$page['translations'][ $trad['lang'] ] = $trad['objName'];
					$used[$trad['lang']] = $trad['langName'];
				}
		}

		$smarty->assign('wplp_used', $used);
	}

	$smarty->assign_by_ref('listpages', $listpages['data']);
	$smarty->assign_by_ref('checkboxes_on', $showCheckbox);
	$smarty->assign_by_ref('showNumberOfPages', $showNumberOfPages);
	if (!empty($showPageAlias) && $showPageAlias == 'y')
		$smarty->assign_by_ref('showPageAlias', $showPageAlias);
	if (isset($showNameOnly) && $showNameOnly == 'y') {
		$ret = $smarty->fetch('wiki-plugins/wikiplugin_listpagenames.tpl');
	} else {
		if (!empty($start) || !empty($end) || $length > 0) {
			foreach ($listpages['data'] as $i=>$page) {
				$listpages['data'][$i]['snippet'] = $tikilib->get_snippet($page['data'], $page['outputType'], ! empty($page['is_html']), '', $length, $start, $end);
			}
		}
        $smarty->assign('current_url', $_SERVER["PHP_SELF"]);
        $ret = $smarty->fetch('tiki-listpages_content.tpl');
	}

	return '~np~'.$ret.'~/np~';
}
