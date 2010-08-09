<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
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
 * \brief Smarty {jq} block handler
 *
 * Creates JQuery javascript if enabled
 * Defaults to execute on DOM ready
 * The content script is automatically escaped with {literal}{/literal} unless the tag is already in there.
 * To "unescape" back to smarty synax use {{ to start, and }} to stop. See examples below.
 * 
 * Usage:
 *    {jq [notonready=false|true], [nojquery='Optional markup for when feature_jquery is off']}
 *        $("#exampleId").hide()
 *    {/jq}
 * 
 * Examples:
 * 
 *  Simple, no escaping - result wrapped in {literal}{/literal}
 *    {jq}$(#exampleId").click(function() { alert("Clicked!"); });{/jq}
 * 
 *  Smarty markup between {{ and }} - result parsed and wrapped in literals
 *    {jq}$(#exampleId").show({{if $animation_fast eq 'y'}"fast"{else}"slow"{/if}}){/jq}
 * 
 *  Escaped already - not re-parsed, not wrapped in literals
 *    {jq}{literal}$(#exampleId").show({/literal}{if $animation_fast eq 'y'}"fast"{else}"slow"{/if}){/jq}
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
