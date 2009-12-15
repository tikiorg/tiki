<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
global $tikilib, $smarty;

// tiki-setup has already set the $language variable
//Create a list of languages
$languages = array();
$languages = $tikilib->list_languages(false, 'y');
if (isset($module_params["mode"]) && ($module_params["mode"] == 'flags' || $module_params["mode"] == 'words')) {
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

