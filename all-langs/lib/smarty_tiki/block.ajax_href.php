<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_ajax_href($params, $content, &$smarty, $repeat) {
    global $prefs;
    if ( $repeat ) return;

    $url = $content;
    $template = $params['template'];
    $htmlelement = $params['htmlelement'];

    if ( $prefs['feature_ajax'] != 'y' || $prefs['javascript_enabled'] == 'n' ) {
	return " href=\"$url\" ";
    } else {
	$max_tikitabs = 5; // Same value as in header.tpl, <body> tag onload's param
	return " href=\"#\" onclick=\"loadComponent('$url','$template','$htmlelement',$max_tikitabs);return false;\" ";
    }
}



?>
