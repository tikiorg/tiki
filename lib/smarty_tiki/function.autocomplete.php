<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* {autocomplete element=$element type=$type }
 * do the same than assign but accept a varaible as var name
 */
function smarty_function_autocomplete($params, &$smarty) {
	global $prefs, $headerlib;

	if ($prefs['javascript_enabled'] !== 'y' or $prefs['feature_jquery_autocomplete'] !== 'y') return '';

	if ( empty($params) || empty($params['element']) || empty($params['type']) ) return '';

	$content = '$("' . $params['element'] . '").tiki("autocomplete", "'. $params['type'] .'");';
	$headerlib->add_jq_onready($content);
}
