<?php

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_block_addonview($params, $content, $smarty, &$repeat)
{
	if ( $repeat ) return;
	
	extract($params, EXTR_SKIP);

	if (empty($params['package']) || empty($params['view'])) {
		return tra("Please specify the name of the package and the view.");
	}

	$parts = explode('/', $params['package']);
	$path = TIKI_PATH . '/addons/' . $parts[0] . '_' . $parts[1] . '/views/' . $params['view'] . '.php';

	if (!file_exists($path)) {
		return tra("Error: Unable to locate view file for the package.");
	}

	require_once($path);

	$functionname = "tikiaddon\\" . $parts[0] . "\\" . $parts[1] . "\\" . $params['view'];

	if (!function_exists($functionname)) {
		return tra("Error: Unable to locate function name for the view.");
	}

	$prefname = 'ta_' . $parts[0] . '_' . $parts[1] . '_on';
	$folder = $parts[0] . '_' . $parts[1];
	if (!isset($GLOBALS['prefs'][$prefname]) || $GLOBALS['prefs'][$prefname] != 'y') {
		return tra('Addon is not activated: ') . $folder;
	}
	
	if ($params['assign']) {
		$smarty->assign($params['assign'], $functionname($content, $params, $smarty));
	} else {
		return $functionname($content, $params, $smarty);
	}
}
