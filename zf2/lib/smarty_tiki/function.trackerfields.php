<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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

	$sectionFormat = $definition->getConfiguration('sectionFormat', 'flat');

	switch ($sectionFormat) {
	case 'tab':
		$title = tr('General');
		$sections = array();

		foreach ($params['fields'] as $field) {
			if ($field['type'] == 'h') {
				$title = tr($field['name']);
			} else {
				$sections[$title][] = $field;
			}
		}

		$out = array();
		foreach ($sections as $title => $fields) {
			$out[md5($title)] = array(
				'heading' => $title,
				'fields' => $fields,
			);
		}

		$smarty->assign('sections', $out);
		return $smarty->fetch('trackerinput/layout_tab.tpl');
	case 'flat':
	default:
		$smarty->assign('fields', $params['fields']);
		return $smarty->fetch('trackerinput/layout_flat.tpl');
	}
}

