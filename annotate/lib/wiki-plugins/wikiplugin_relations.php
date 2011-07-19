<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_relations_info()
{
	return array(
		'name' => tra('Relations'),
		'description' => tra('Displays the relations between the current object or a specified one and the rest of Tiki.'),
		'filter' => 'int',
		'format' => 'html',
		'prefs' => array('wikiplugin_relations'),
		'introduced' => 8,
		'documentation' => 'PluginRelations',
		'params' => array(
			'qualifiers' => array(
				'required' => true,
				'name' => tra('Qualifiers'),
				'description' => tra('Comma-separated list of relation qualifiers.'),
				'separator' => ',',
				'filter' => 'attribute_type',
				'default' => array(),
				'since' => '8.0',
			),
			'object' => array(
				'required' => false,
				'name' => tra('Object'),
				'description' => tra('Object identifier as type:itemId'),
				'filter' => 'text',
				'default' => null,
				'since' => '8.0',
			),
			'singlelist' => array(
				'required' => false,
				'name' => tr('Single List'),
				'description' => tr('Render all qualifiers into a single list without displaying the qualifier name.'),
				'filter' => 'int',
				'since' => '8.0',
				'default' => 0,
				'options' => array(
					array('text' => tr('No'), 'value' => 0),
					array('text' => tr('Yes'), 'value' => 1),
				),
			),
		),
	);
}

function wikiplugin_relations($data, $params)
{
	$object = current_object();

	if (isset($params['object']) && false !== strpos($params['object'], ':')) {
		list($object['type'], $object['object']) = explode(':', $params['object'], 2);
	}

	if (!isset($params['qualifiers'])) {
		return WikiParser_PluginOutput::argumentError(array('qualifiers'));
	}

	$singlelist = false;
	if (isset($params['singlelist']) && $params['singlelist']) {
		$singlelist = true;
	}

	$data = array();

	$relationlib = TikiLib::lib('relation');
	foreach ($params['qualifiers'] as $qualifier) {
		$name = $singlelist ? 'singlelist' : tra($qualifier);

		$found = $relationlib->get_relations_from($object['type'], $object['object'], $qualifier);
		
		foreach ($found as $relation) {
			$type = $relation['type'];
			$id = $relation['itemId'];
			$data[$name]["$type:$id"] = array('type' => $type, 'object' => $id);
		}
	}

	$smarty = TikiLib::lib('smarty');
	$smarty->assign('wp_relations', $data);
	$smarty->assign('wp_singlelist', $singlelist);
	return $smarty->fetch('wiki-plugins/wikiplugin_relations.tpl');
}

