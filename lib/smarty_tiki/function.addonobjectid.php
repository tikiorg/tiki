<?php

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_function_addonobjectid($params, $smarty)
{
	extract($params, EXTR_SKIP);
	if (empty($params['reference']) && empty($params['ref'])) {
		return '';
	}

	if (!empty($params['ref'])) {
		$ref = $params['ref'];
	} else {
		$ref = $params['reference'];
	}

	if (!empty($params['profile'])) {
		$profile = $params['profile'];
	} else {
		$profile = '';
	}

	$package = $smarty->getTemplateVars('tikiaddon_package');

	$utilities = new TikiAddons_Utilities();

	return $utilities->getObjectId($package, $ref, $profile);
}
