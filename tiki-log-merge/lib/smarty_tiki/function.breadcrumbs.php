<?php
// $Header Exp: $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_breadcrumbs($params, &$smarty)
{
    extract($params);
	
    if (empty($crumbs)) {
        $smarty->trigger_error("assign: missing 'crumbs' parameter");
        return;
    }
    if (empty($loc)) {
        $smarty->trigger_error("assign: missing 'loc' parameter");
        return;
    }
    $text_to_display = '';
    switch ($type) {
		case 'invertfull':
			$text_to_display = breadcrumb_buildHeadTitle(array_reverse($crumbs));
            break;
        case 'fulltrail':
			$text_to_display = breadcrumb_buildHeadTitle($crumbs);
            break;
        case 'pagetitle':
			$text_to_display = breadcrumb_getTitle($crumbs, $loc);
            break;
        case 'desc':
			$text_to_display = breadcrumb_getDescription($crumbs, $loc);
            break;
        case 'trail':
        default:
			$text_to_display = breadcrumb_buildTrail($crumbs,$loc);
            break;
    }
    if(!empty($machine_translate)) {
    	require_once('lib/core/lib/Multilingual/MachineTranslation/GoogleTranslateWrapper.php');
		$translator = new Multilingual_MachineTranslation_GoogleTranslateWrapper($source_lang,$target_lang);
		$text_to_display = $translator->translateText($text_to_display);	
    }
    print($text_to_display);
    
}
