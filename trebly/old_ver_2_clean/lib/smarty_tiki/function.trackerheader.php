<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.help.php 25202 2010-02-14 18:16:23Z changi67 $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/* params
   level: level of tag <h>
   title: title of the <h>
   toggle: o for open, c for close
   inTable: table class in a table otherwise will insert div
*/
function smarty_function_trackerheader($params, &$smarty)
{
	global $prefs;
	global $headerlib; include_once('lib/headerlib.php');
	$output = $js = '';
	static $trackerheaderStack = array();
	static $iTrackerHeader = 0;
	$last = count($trackerheaderStack);
	$default = array('level'=>2, 'inTable'=>'');
	$params = array_merge($default, $params);
	extract($params, EXTR_SKIP);

	if (!empty($inTable)) {
		$output .= '</table>';
	}
	while (! empty($last) && $level <= $trackerheaderStack[$last -1]) { // need to close block
		$output .= "</div>";
		array_pop($trackerheaderStack);
		--$last;
	}
	if (!empty($title)) { // new header
		array_push($trackerheaderStack, $level);
		$output .= "<!--PUSH".count($trackerheaderStack)." -->";
		$id = "trackerHeader_$iTrackerHeader";
		$div_id = "block_$id";
		$output .= "<h$level id=\"$id\"";
		if ($prefs['javascript_enabled'] == 'y' && ($toggle == 'o' || $toggle == 'c')) {
			$output .= ' class="'.($toggle == 'c'?'trackerHeaderClose':'trackerHeaderOpen').'"';
		}
		$output .= '>';
		$output .= "$title";
		$output .= "</h$level>";
		if ($prefs['javascript_enabled'] == 'y' && ($toggle == 'o' || $toggle == 'c')) {
			$js = "\$('#$id').click(function(event){";
			$js .= "\$('#$div_id').toggle();";
			$js .= "\$('#$id').toggleClass('trackerHeaderClose');";
			$js .= "\$('#$id').toggleClass('trackerHeaderOpen');";
			$js .= "});";
			$headerlib->add_jq_onready($js);
			if ($toggle == 'c') {
				$headerlib->add_jq_onready("\$('#$div_id').hide();");
			}			
		}
		$output .= '<';
		$output .= (isset($inTable) && $inTable == 'y')?'tbody': 'div';
		$output .= " id=\"$div_id\">";
		++$iTrackerHeader;
	} else {
		$last = 0;
		$trackerheaderStack = array();
	}
	if (!empty($inTable)) {
		$output .= "<table class=\"$inTable\">";
	}
	return $output;
}
