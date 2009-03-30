<?php
// $Id: $

//this script may only be included - so its better to die if called directly.
global $access; $access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

/**
 * \brief Smarty {jq} block handler
 *
 * Creates JQuery javascript if enabled
 * Defaults to execute on DOM ready
 * 
 * Usage:
 *    {jq [notonready=false|true], [nojquery='Optional markup for when feature_jquery is off']}$jq("#exampleId").hide(){/jq}
 *
 */

function smarty_block_jq($params, $content, &$smarty) {
	global $headerlib, $prefs;
	
	if (empty($content)) { return ''; }
	
	extract($params);
	$nojquery = isset($nojquery) ? $nojquery : tr('<!-- jq smarty plugin inactive: feature_jquery off -->');
	if ($prefs['feature_jquery'] != 'y') { return $nojquery; }
	$notonready = isset($notonready) ? $notonready : false;
	
	if (!$notonready) {		
		$headerlib->add_jq_onready($content);
	} else {	// 
		$headerlib->add_js($content);
	}
	return '';
}
?>