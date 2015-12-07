<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {html_select_duration} function plugin
 *
 * Type:     function<br>
 * Name:     html_select_duration<br>
 * params: prefix, default_unit(key word or value in secs), default (nb of units), default_value (duration in secs)
 * Purpose:  Prints the dropdowns for duration selection
 */
function smarty_function_html_select_duration($params, $smarty)
{
	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_function_html_options');
	$html_result = '';
	$default = array('prefix'=>'Duration_', 'default_unit'=>'week', 'default'=>'', 'default_value'=>'', 'id'=>'');
	$params = array_merge($default, $params);
	$values = array(31536000, 2628000, 604800, 86400, 3600, 60);
	$output = array(tra('Year'), tra('Month'), tra('Week'), tra('Day'), tra('Hour'), tra('Minute'));
	$defs = array('year', 'month', 'week', 'day', 'hour', 'minute');
	if (!empty($params['default_value'])) {
		foreach ($values as $selected) {
			if ($params['default_value'] >= $selected) {
				$params['default'] = round($params['default_value'] / $selected);
				break;
			}
		}
	} elseif (($key = array_search($params['default_unit'], $defs)) !== false) {
		$selected = $values[$key];
	} elseif (in_array($params['default_unit'], $values)) {
		$selected = $params['default_unit'];
	} else {
		$selected = 604800;
	}
	$id = !empty($params['id']) ? ' id="' . $params['id'] . '" ' : '';
	$html_result .= '<div class="col-sm-1">';
	$html_result .= '<input ' . $id . 'name="'.$params['prefix'].'" type="text" size="5" value="'.$params['default'].'" class="form-control"></div>';
	if (strstr($params['prefix'], '[]')) {
		$prefix = str_replace('[]', '_unit[]', $params['prefix']);
	} else {
		$prefix = $params['prefix'].'_unit';
	}
	$html_result .= '<div class="col-sm-2"><select name="'.$prefix.'" class="form-control">';

	$html_result .= smarty_function_html_options(
		array(
			'values' => $values,
			'output' => $output,
			'selected' => $selected
		),
		$smarty
	);

	$html_result .= '</select></div>';
	return $html_result;
}

function compute_select_duration($params, $prefix='Duration')
{
	return $_REQUEST[$prefix] * $_REQUEST[$prefix.'_unit'];
}

