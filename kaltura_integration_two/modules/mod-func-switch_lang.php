<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_switch_lang_info() {
	return array(
		'name' => tra('Switch Language'),
		'description' => tra('Displays a language picker to change the language of the site.'),
		'prefs' => array( 'feature_multilingual' ),
		'params' => array(
			'mode' => array(
				'name' => tra('Display mode'),
				'description' => tra('Changes how the list of languages is displayed. Possible values are droplist, flags and words. Defaults to droplist.'),
				'filter' => 'alpha',
			),
			'prefix' => array(
				'name' => tra('Prefix'),
				'description' => tra('Changes the default displayed prefix from \"Site Language:\"'),
				'filter' => 'striptags',
			),
		),
	);
}

function module_switch_lang( $mod_reference, $module_params ) {
	global $tikilib, $smarty, $prefs;

	// tiki-setup has already set the $language variable
	//Create a list of languages
	$languages = array();
	$languages = $tikilib->list_languages(false, 'y');
	$mode = isset($module_params["mode"]) ? $module_params["mode"] : "droplist";
	$smarty->assign('mode', $mode);
	if ($mode == 'flags' || $mode == 'words') {
		include('lang/flagmapping.php');
		global $pageRenderer;
		//$trads = $multilinguallib->getTranslations('wiki page', $page_id, $page, $prefs['language']);
		
		for ($i = 0; $i < count($languages); $i++) {
			if (isset($flagmapping[$languages[$i]['value']])) {
				$languages[$i]['flag'] = $flagmapping[$languages[$i]['value']][0];
			}
			if (isset($pageRenderer) && count($pageRenderer->trads) > 0) {
				$languages[$i]['class'] = ' unavailable';
				for ($t = 0; $t < count($pageRenderer->trads); $t++) {
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
		//sort($languages);
	}
	$smarty->assign_by_ref('languages', $languages);
	$smarty->clear_assign('tpl_module_title'); // TPL overrides default title
}

