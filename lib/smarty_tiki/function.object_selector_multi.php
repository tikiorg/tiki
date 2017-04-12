<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
function smarty_function_object_selector_multi( $params, $smarty )
{
	global $prefs;
	static $uniqid = 0;

	$arguments = [
		'name' => null,
		'class' => null,
		'id' => null,
		'value' => null,
		'filter' => [],
		'title' => null,
		'simplename' => null,
		'simpleid' => null,
		'simpleclass' => null,
		'simplevalue' => null,
		'separator' => null,
		'threshold' => null,
		'parent' => null,
		'parentkey' => null,
		'format' => null,
		'placeholder' => tr('Title'),
	];

	// Handle reserved parameters
	foreach (array_keys($arguments) as $var) {
		if (isset($params["_$var"])) {
			$arguments[$var] = $params["_$var"];
		}
		unset($params["_$var"]);
	}

	if ($prefs['feature_search'] !== 'y') {
		if ($arguments['simplename'] && isset($arguments['simplevalue'])) {
			if ($params['type'] === 'trackerfield' && $arguments['separator'] === ',') {
				$help = tra('Comma-separated list of field IDs');
			} else {
				$help = tr('%0 list separated with "%1"', ucfirst($params['type']), $arguments['separator']);
			}
			return "<input type='text' name='{$arguments['simplename']}' value='{$arguments['simplevalue']}' size='50'>" .
					"<div class='help-block'>" . $help . "</div>";
		} else {
			return tra('Object selector requires Unified Index to be enabled.');
		}
	}

	if (empty($arguments['id'])) {
		$arguments['id'] = 'object_selector_multi_' . ++$uniqid;
	}
	if (empty($arguments['simpleid'])) {
		$arguments['simpleid'] = 'object_selector_multi_' . ++$uniqid;
	}

	if ($arguments['filter']) {
		$arguments['filter'] = array_merge($arguments['filter'], $params);
	} else {
		$arguments['filter'] = $params;
	}

	$selector = TikiLib::lib('objectselector');

	if ($arguments['simplevalue'] && ! empty($arguments['filter']['type']) && $arguments['separator']) {
		$arguments['current_selection'] = $selector->readMultipleSimple($arguments['filter']['type'], $arguments['simplevalue'], $arguments['separator']);
	} else {
		$arguments['current_selection'] = $selector->readMultiple($arguments['value']);
	}

	if ($arguments['simplename']) {
		$arguments['class'] .= ' hidden';
	} else {
		$arguments['simpleclass'] .= ' hidden';
	}

	$arguments['current_selection_simple'] = array_map(function ($item) {
		return $item['id'];
	}, $arguments['current_selection']);

	$arguments['filter'] = json_encode($arguments['filter']);

	$smarty->assign(
		'object_selector_multi',
		$arguments
	);

	return $smarty->fetch('object_selector_multi.tpl');
}

