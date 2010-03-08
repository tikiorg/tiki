<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.html_select_time.php 25202 2010-02-14 18:16:23Z changi67 $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
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
 * params: prefix, default_unit, default
 * Purpose:  Prints the dropdowns for duration selection
 */
function smarty_function_html_select_duration($params, &$smarty)
{
	global $smarty;
	require_once $smarty->_get_plugin_filepath('function','html_options');
	$html_result = '';
	$default = array('prefix'=>'Duration_', 'default_unit'=>'week', 'default'=>'');
	$params = array_merge($default, $params);
	$values = array(31536000, 2628000, 604800, 86400, 3600, 60);
	$output = array(tra('Year'), tra('Month'), tra('Week'), tra('Day'), tra('Hour'), tra('Minute'));
	$defs = array('year', 'month', 'week', 'day', 'hour', 'minute');
	if (($key = array_search($params['default_unit'], $defs)) !== false) {
		$selected = $values[$key];
	} elseif (in_array($params['default_unit'], $values)) {
		$selected = $params['default_unit'];
	} else {
		$selected = 604800;
	}
	$html_result .= '<input name="'.$params['prefix'].'" type="text" size="5" value="'.$params['default'].'" />';
	$html_result .= '<select name="'.$params['prefix'].'_unit">';
	$html_result .= smarty_function_html_options(array(
		'values' => $values,
		'output' => $output,
		'selected' => $selected), $smarty);
	$html_result .= '</select>';
    return $html_result;
}
