<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_switch_lang_info()
{
	return array(
		'name' => tra('Switch Language'),
		'description' => tra('Displays a language picker to change the language of the site.'),
		'prefs' => array('feature_multilingual', 'change_language'),
		'params' => array(
			'mode' => array(
				'name' => tra('Display mode'),
				'description' => tra('Changes how the list of languages is displayed. Possible values are droplist, flags and words. Defaults to droplist.'),
				'filter' => 'alpha',
			),
		),
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_switch_lang($mod_reference, $module_params)
{
	global $prefs;
	$smarty = TikiLib::lib('smarty');
	$tikilib = TikiLib::lib('tiki');

	// tiki-setup has already set the $language variable
	//Create a list of languages
	$languages = array();
	$langLib = TikiLib::lib('language');
	$languages = $langLib->list_languages(false, 'y');
	$mode = isset($module_params["mode"]) ? $module_params["mode"] : "droplist";
	$smarty->assign('mode', $mode);
	if ($mode == 'flags' || $mode == 'words' || $mode == 'abrv') {
		include('lang/flagmapping.php');
		global $pageRenderer;
		//$trads = $multilinguallib->getTranslations('wiki page', $page_id, $page, $prefs['language']);
		
		for ($i = 0, $icount_languages = count($languages); $i < $icount_languages; $i++) {
			if (isset($flagmapping[$languages[$i]['value']])) {
				$languages[$i]['flag'] = $flagmapping[$languages[$i]['value']][0];
			} else {
				$languages[$i]['flag'] = '';
			}
			if (isset($pageRenderer) && count($pageRenderer->trads) > 0) {
				$languages[$i]['class'] = ' unavailable';
				for ($t = 0, $tcount_pageR = count($pageRenderer->trads); $t < $tcount_pageR; $t++) {
					if ($pageRenderer->trads[$t]['lang'] == $languages[$i]['value']) {
						$languages[$i]['class'] = ' available';
					}
				}
			} else {
				$languages[$i]['class'] = '';
			}
			if ($languages[$i]['value'] == $prefs['language']) {
				$languages[$i]['class'] .= ' highlight';
			}
		}
	}
	$smarty->assign_by_ref('languages', $languages);
}

