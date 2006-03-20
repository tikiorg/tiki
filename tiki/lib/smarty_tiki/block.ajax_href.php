<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_ajax_href($params, $content, &$smarty)
{
    global $feature_ajax;

    $url = $content;
    $template = $params['template'];
    $htmlelement = $params['htmlelement'];

    if ($feature_ajax != 'y') {
	return " href=\"$url\" ";
    } else {
	return " style=\"cursor: pointer;\" onclick=\"loadComponent('$url','$template','$htmlelement');\" ";
    }
}

/* vim: set expandtab: */

?>
