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

/**
 * Variable arguments to be sent as filters for the object list. Filters match the unified search
 * field filters.
 *
 * Reserved parameters:
 *  - _id for the field ID
 *  - _class for the field classes
 *  - _name for the field name
 *  - _value for the current value (type:objectId)
 *  - _filter is the same as all other arguements, expecting an array
 *
 * The component will build a drop list for the object selector if the results fit in a reasonable amount
 * of space or will use autocomplete on the object title otherwise.
 */
function smarty_function_object_selector( $params, $smarty )
{
	static $uniqid = 0;

	$arguments = [
		'name' => null,
		'class' => null,
		'id' => null,
		'value' => null,
		'filter' => [],
		'title' => null,
	];

	// Handle reserved parameters
	foreach (array('name', 'class', 'id', 'value', 'filter') as $var) {
		if (isset($params["_$var"])) {
			$arguments[$var] = $params["_$var"];
		}
		unset($params["_$var"]);
	}

	if (empty($arguments['id'])) {
		$arguments['id'] = 'object_selector_' . ++$uniqid;
	}

	if ($arguments['filter']) {
		$arguments['filter'] = array_merge($arguments['filter'], $params);
	} else {
		$arguments['filter'] = $params;
	}

	$arguments['filter'] = json_encode($arguments['filter']);

	if ($arguments['value']) {
		list($type, $object) = explode(':', $arguments['value'], 2);
		$arguments['title'] = TikiLib::lib('object')->get_title($type, $object);
	}

	$smarty->assign(
		'object_selector',
		$arguments
	);

	return $smarty->fetch('object_selector.tpl');
}

