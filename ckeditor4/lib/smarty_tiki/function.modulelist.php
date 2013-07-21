<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_modulelist($params, $smarty)
{
	$moduleZones = $smarty->getTemplateVars('module_zones');

	global $prefs;
	if (empty($params['zone'])) {
		return tr("Missing %0 parameter", 'zone');
	}

	$zone = $params['zone'];

	$class = 'content clearfix modules';
	if (! empty($params['class'])) {
		$class = $params['class'];
	}

	$id = $zone . '_modules';
	if (! empty($params['id'])) {
		$id = $params['id'];
	}

	$dir = '';
	if (isset($params['bidi']) && $params['bidi'] == 'y' && $prefs['feature_bidi'] == 'y') {
		$dir = ' dir="rtl"';
	}

	$content = '';
	$key = $zone . '_modules';
	if (isset($moduleZones[$key]) && is_array($moduleZones[$key])) {
		$content = implode(
			'',
			array_map(
				function ($module) {
					return $module['data'];
				},
				$moduleZones[$key]
			)
		);
	}
	if ($prefs['mobile_feature'] === 'y' && $prefs['mobile_mode'] === 'y') {
		$mobile = ' data-role="collapsible-set" data-theme="' . $prefs['mobile_theme_modules'] . '"';
	} else {
		$mobile = '';
	}
	return <<<OUT
<div class="$class" id="$id"$dir$mobile>
	$content
</div>
OUT;
}
