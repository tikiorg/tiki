<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_trackerfields($params, $smarty)
{
	if (! isset($params['fields']) || ! is_array($params['fields'])) {
		return tr('Invalid fields provided.');
	}

	if (! isset($params['trackerId']) || ! $definition = Tracker_Definition::get($params['trackerId'])) {
		return tr('Missing or invalid tracker reference.');
	}

	if (! isset($params['mode'])) {
		$params['mode'] = 'edit';
	}

	$smarty->loadPlugin('smarty_function_trackeroutput');
	$smarty->loadPlugin('smarty_function_trackerinput');

	$sectionFormat = $definition->getConfiguration('sectionFormat', 'flat');

	if (! empty($params['format'])) {
		$sectionFormat = $params['format'];
	}

	$smarty->assign('tracker_info', $definition->getInformation());
	$smarty->assign('status_types', TikiLib::lib('trk')->status_types());

	$title = tr('General');
	$sections = [];
	$auto = ['input' => [], 'output' => [], 'inline' => []];

	foreach ($params['fields'] as $field) {
		if ($field['type'] == 'h') {
			$title = tr($field['name']);
		} else {
			$sections[$title][] = $field;
		}
		$permName = $field['permName'];
		$auto['input'][$permName] = new Tiki_Render_Lazy(function () use ($field, $smarty) {
			return smarty_function_trackerinput([
				'field' => $field,
				'showlinks' => 'n',
				'list_mode' => 'n',
			], $smarty);
		});
		$auto['output'][$permName] = new Tiki_Render_Lazy(function () use ($field, $smarty) {
			return smarty_function_trackeroutput([
				'field' => $field,
				'showlinks' => 'n',
				'list_mode' => 'n',
			], $smarty);
		});
		if (isset($params['itemId'])) {
			$auto['inline'][$permName] = new Tiki_Render_Lazy(function () use ($field, $smarty, $params) {
				return smarty_function_trackeroutput([
					'field' => $field,
					'showlinks' => 'n',
					'list_mode' => 'n',
					'editable' => 'inline',
					'itemId' => $params['itemId'],
				], $smarty);
			});
		}
	}

	$out = array();
	foreach ($sections as $title => $fields) {
		$out[md5($title)] = array(
			'heading' => $title,
			'fields' => $fields,
		);
	}

	if ($params['mode'] == 'view') {
		$auto['default'] = $auto['output'];
	} else {
		$auto['default'] = $auto['input'];
	}

	$smarty->assign('sections', array_values($out));
	$smarty->assign('fields', $params['fields']);
	$smarty->assign('auto', $auto);

	$trklib = TikiLib::lib('trk');
	$template = $trklib->getSectionModeTemplate($sectionFormat, $params['mode']);

	return $smarty->fetch($template);
}

