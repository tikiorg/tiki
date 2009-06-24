<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_ajax_href($params, $content, &$smarty, $repeat) {
    global $prefs, $user;
    if ( $repeat ) return;

		if ( !empty($params['_onclick']) ) {
			$onclick = $params['_onclick'];
		} else {
			$onclick = '';
    }
    $url = $content;
    $template = $params['template'];
    $htmlelement = $params['htmlelement'];
    $func = isset($params['function']) ? $params['function']: 'window.scrollTo(0,0);loadComponent';	// preserve previous behaviour
    $last_user = htmlspecialchars($user);

    if ( $prefs['feature_ajax'] != 'y' || $prefs['javascript_enabled'] == 'n' ) {
	return " href=\"$url\" ";
    } else {
	$max_tikitabs = 50; // Same value as in header.tpl, <body> tag onload's param
	if (empty($params['_anchor'])) {
		$anchor = "#main";
	} else {
		$anchor = '#'.$params['_anchor'];
	}
	return " href=\"$anchor\" onclick=\"$onclick ;$func('$url','$template','$htmlelement',$max_tikitabs,'$last_user');\" ";
    }
}
