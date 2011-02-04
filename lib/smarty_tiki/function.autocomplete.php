<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* {autocomplete element=$element type=$type }
 * Attach jQuery autocomplete to element/s
 * 
 * Params:
 * 
 *		element: Required (jQuery selector, and match multiple elements)
 *		type:    Required (defined in tiki-jquery.js -> $.fn.tiki
 *				 currently: pagename|groupname|username|usersandcontacts|userrealname|tag|icon|trackername)
 *		options: Optional further options for autocomplete fn
 *				 see http://docs.jquery.com/Plugins/Autocomplete/autocomplete#url_or_dataoptions
 *				 N.B. Will be wrapped in {} chars here to avoid smarty delimiter difficulties
 * 
 */
function smarty_function_autocomplete($params, &$smarty) {
	global $prefs, $headerlib;

	if ($prefs['javascript_enabled'] !== 'y' or $prefs['feature_jquery_autocomplete'] !== 'y') return '';

	if ( empty($params) || empty($params['element']) || empty($params['type']) ) return '';
	
	if (!empty($params['options'])) {
		$options = ',{' . $params['options'] . '}';
	} else {
		$options = '';
	}

	$content = '$("' . $params['element'] . '").tiki("autocomplete", "'. $params['type'] .'"' . $options . ');';
	$headerlib->add_jq_onready($content);
}
