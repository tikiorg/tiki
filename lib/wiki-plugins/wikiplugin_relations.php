<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_relations_info()
{
	return array(
		'name' => tra('Relations'),
		'description' => tra('Display the relation of an object to the rest of the site'),
		'filter' => 'int',
		'format' => 'html',
		'prefs' => array('wikiplugin_relations'),
		'introduced' => 8,
		'iconname' => 'move',
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
				'description' => tr('Object identifier as %0type:itemId%1', '<code>', '</code>'),
				'filter' => 'text',
				'default' => null,
				'since' => '8.0',
				'profile_reference' => 'type_colon_object',
			),
			'singlelist' => array(
				'required' => false,
				'name' => tra('Single List'),
				'description' => tra('Render all qualifiers into a single list without displaying the qualifier name.'),
				'filter' => 'int',
				'since' => '8.0',
				'default' => 0,
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('No'), 'value' => 0),
					array('text' => tra('Yes'), 'value' => 1),
				),
			),
			'emptymsg' => array(
				'required' => false,
				'name' => tra('Empty Message'),
				'description' => tra('Message to give if result is empty and no relations are found.'),
				'filter' => 'text',
				'since' => '15.0',
				'default' => "No relations found.",
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

	$emptymsg = "No relations found.";
	if (isset($params['emptymsg']) && $params['emptymsg']) {
		$emptymsg = $params['emptymsg'];
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
	$smarty->assign('wp_emptymsg', $emptymsg);
	return $smarty->fetch('wiki-plugins/wikiplugin_relations.tpl');
}

