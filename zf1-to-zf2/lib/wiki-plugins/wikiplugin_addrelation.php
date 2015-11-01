<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_addrelation_info()
{
	return array(
		'name' => tra('Add Relation'),
		'description' => tra('Provide a button to toggle a pre-specified relation'),
		'filter' => 'int',
		'format' => 'html',
		'validate' => 'all',
		'prefs' => array('wikiplugin_addrelation'),
		'introduced' => 8,
		'iconname' => 'link-external',
		'documentation' => 'PluginAddRelation',
		'params' => array(
			'qualifier' => array(
				'required' => true,
				'name' => tra('Qualifier'),
				'description' => tra('Relation qualifier. Usually a three part string separated by 2 periods.'),
				'filter' => 'attribute_type',
				'default' => array(),
				'since' => '8.0',
			),
			'source_object' => array(
				'required' => false,
				'name' => tra('Source Object'),
				'description' => tr('Object identifier as %0type:itemId%1 to start the relation from, will use the current
					object if left blank.', '<code>', '</code>'),
				'filter' => 'text',
				'default' => null,
				'since' => '8.0',
				'profile_reference' => 'type_colon_object',
			),
			'target_object' => array(
				'required' => false,
				'name' => tra('Target Object'),
				'description' => tr('Object identifier as %0type:itemId%1 to end the relation to, will use the current
					object if left blank.', '<code>', '</code>'),
				'filter' => 'text',
				'default' => null,
				'since' => '8.0',
				'profile_reference' => 'type_colon_object',
			),
			'label_add' => array(
				'required' => false,
				'name' => tra('Button Text for Add'),
				'description' => tra('Text to show on the button to add relation'),
				'filter' => 'text',
				'since' => '8.0',
				'default' => tra('Add Relation'),
			),
			'label_added' => array(
				'required' => false,
				'name' => tra('Button Text for Already Added State'),
				'description' => tra('Text to show on the button when relation is already added'),
				'filter' => 'text',
				'since' => '8.0',
				'default' => tra('Relation Added'),
			),
			'label_remove' => array(
				'required' => false,
				'name' => tra('Mouseover Button Text for Remove'),
				'description' => tra('Text to show on the button to remove relation'),
				'filter' => 'text',
				'since' => '8.0',
				'default' => tra('Remove Relation'),
			),
			'button_id' => array(
				'required' => false,
				'name' => tra('Button Id'),
				'description' => tra('A unique ID to distinguish the button from others on the page if there is more than one'),
				'filter' => 'text',
				'since' => '8.0',
				'default' => '0',
			),
			'button_class' => array(
				'required' => false,
				'name' => tra('Set Button Class'),
				'description' => tra('Class or classes for the Button'),
				'filter' => 'text',
				'since' => '8.0',
				'default' => 'btn btn-default',
			),
		),
	);
}

function wikiplugin_addrelation($data, $params)
{
	global $user;
	if (isset($params['source_object']) && false !== strpos($params['source_object'], ':')) {
		list($source_object['type'], $source_object['object']) = explode(':', $params['source_object'], 2);
	} else {
		$source_object = current_object();
	}
	if (isset($params['target_object']) && false !== strpos($params['target_object'], ':')) {
		list($target_object['type'], $target_object['object']) = explode(':', $params['target_object'], 2);
	} else {
		$target_object = current_object();
	}
	if ($source_object == $target_object) {
		return tra('Source and target object cannot be the same');
	}
	if (!isset($params['qualifier'])) {
		return WikiParser_PluginOutput::argumentError(array('qualifier'));
	} else {
		$qualifier = $params['qualifier'];
	}
	if (!empty($params['label_add'])) {
		$labeladd = $params['label_add'];
	} else {
		$labeladd = tra('Add Relation');
	}
	if (!empty($params['label_remove'])) {
		$labelremove = $params['label_remove'];
	} else {
		$labelremove = tra('Remove Relation');
	}
	if (!empty($params['label_added'])) {
		$labeladded = $params['label_added'];
	} else {
		$labeladded = tra('Relation Added');
	}
	if (!empty($params['button_id'])) {
		$id = 'wp_addrelation_' . $params['button_id'];
	} else {
		$id = 'wp_addrelation_0';
	}
	if (!empty($params['button_class'])) {
		$button_class = $params['button_class'];
	} else {
		$button_class = "btn btn-default";
	}
	$relationlib = TikiLib::lib('relation');

	if (isset($_POST[$id])) {
		if ($_POST[$id] == 'y') {
			$relationlib->add_relation($qualifier, $source_object['type'], $source_object['object'], $target_object['type'], $target_object['object']);
		} elseif ($_POST[$id] == 'n') {
			if ($relation_id = $relationlib->get_relation_id($qualifier, $source_object['type'], $source_object['object'], $target_object['type'], $target_object['object'])) {
				$relationlib->remove_relation($relation_id);
			}
		}
	}
	$relationsfromsource = $relationlib->get_relations_from($source_object['type'], $source_object['object'], $qualifier);
	$relationexists = false;
	foreach ($relationsfromsource as $r) {
		if ($r['itemId'] == $target_object['object'] && $r['type'] == $target_object['type']) {
			$relationexists = true;
			break;
		}
	}

	$smarty = TikiLib::lib('smarty');
	$smarty->assign('wp_addrelation_id', $id);
	$smarty->assign('wp_addrelation_action', $relationexists ? 'n' : 'y');
	$smarty->assign('label_add', $labeladd);
	$smarty->assign('label_added', $labeladded);
	$smarty->assign('label_remove', $labelremove);
	$smarty->assign('button_class', $button_class);
	return $smarty->fetch('wiki-plugins/wikiplugin_addrelation.tpl');
}
