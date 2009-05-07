<?php
/* $Id $
 * Params
 *     notonready = bool (default: false) - set to true if you want it _not_ to execute on document ready
 *     nojquery = string (default: "<!-- jq smarty plugin inactive: feature_jquery off -->") - Optional markup for when feature_jquery is off
 * 
 * data is the JQuery javascript code
 * 
 */
function wikiplugin_jq_help() {
	return tra("Insert JQuery javascript code.")."<br />~np~{JQ(nojquery='<p>You need JQuery for this!</p>')}".tra("jquery code e.g. \$jq(\"img\").click(function() {\n  \$jq(this).hide(\"slow\").show(\"fast\");\n});")."{JQ}~/np~";
}

function wikiplugin_jq_info() {
	return array(
		'name' => tra('JQuery'),
		'documentation' => 'PluginJQ',
		'description' => tra('Insert JQuery javascript code. Requires feature_jquery').tra(' (experimental - may change in future versions)'),
		'prefs' => array( 'feature_jquery', 'wikiplugin_jq' ),
		'body' => tra('JQuery Code'),
		'validate' => 'all',
		'params' => array(
			'notonready' => array(
				'required' => false,
				'name' => tra('NotOnReady'),
				'description' => tra("Do not execute on document ready (execute inline)"),
			),
			'nojquery' => array(
				'required' => false,
				'name' => tra('NoJQuery'),
				'description' => tra('Optional markup for when feature_jquery is off'),
			)
		)
	);
}
	
function wikiplugin_jq($data, $params) {
	global $headerlib, $prefs;
	extract($params, EXTR_SKIP);
	
	$nojquery = isset($nojquery) ? $nojquery : tr('<!-- jq smarty plugin inactive: feature_jquery off -->');
	if ($prefs['feature_jquery'] != 'y') { return $nojquery; }
	$notonready = isset($notonready) ? $notonready : false;
	
	if (!$notonready) {		
		$headerlib->add_jq_onready($data);
	} else { 
		$headerlib->add_js($data);
	}
	return '';
}
?>
