<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
/* 
 * Params
 *     notonready = bool (default: false) - set to true if you want it _not_ to execute on document ready
 *     nojquery = string (default: "<!-- jq smarty plugin inactive: feature_jquery off -->") - Optional markup for when feature_jquery is off
 * 
 * data is the JQuery javascript code
 */
function wikiplugin_jq_help() {
	return tra("Insert JQuery javascript code.")."<br />~np~{JQ(nojquery='<p>You need JQuery for this!</p>')}".tra("jquery code e.g. \$(\"img\").click(function() {\n  \$(this).hide(\"slow\").show(\"fast\");\n});")."{JQ}~/np~";
}

function wikiplugin_jq_info() {
	return array(
		'name' => tra('jQuery'),
		'documentation' => tra('PluginJQ'),
		'description' => tra('Inserts JavaScript code. By default this is only executed after jQuery determines that the DOM is fully loaded.'),
		'prefs' => array( 'wikiplugin_jq' ),
		'body' => tra('JavaScript code'),
		'validate' => 'all',
		'filter' => 'none',
		'params' => array(
			'notonready' => array(
				'required' => false,
				'name' => tra('Not On Ready'),
				'description' => tra("Do not execute on document ready (execute inline)"),
			),
			'nojquery' => array(
				'required' => false,
				'name' => tra('No JavaScript'),
				'description' => tra('Optional markup for when JavaScript is off'),
			)
		)
	);
}
	
function wikiplugin_jq($data, $params) {
	global $headerlib, $prefs;
	extract($params, EXTR_SKIP);
	
	$nojquery = isset($nojquery) ? $nojquery : tr('<!-- jq smarty plugin inactive: feature_jquery off -->');
	if ($prefs['javascript_enabled'] != 'y') { return $nojquery; }
	$notonready = isset($notonready) ? $notonready : false;
	
	if (!$notonready) {		
		$headerlib->add_jq_onready($data);
	} else { 
		$headerlib->add_js($data);
	}
	return '';
}
