<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
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

	// Handle reserved parameters
	foreach (array('name', 'class', 'id', 'value', 'filter') as $var) {
		$$var = '';
		if (isset($params["_$var"])) {
			$$var = $params["_$var"];
		}
		unset($params["_$var"]);
	}

	if (empty($id)) {
		$id = 'object_selector_' . ++$uniqid;
	}

	if ($filter) {
		$params = array_merge($filter, $params);
	}

	$smarty->assign('object_selector', array(
		'filter' => json_encode($params),
		'id' => $id,
		'name' => $name,
		'class' => $class,
		'value' => $value,
	));

	return $smarty->fetch('object_selector.tpl');
}

