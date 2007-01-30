<?php
// $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/trackeritemfield/smarty_tiki/block.itemfield.php,v 1.1 2007-01-30 14:25:43 sylvieg Exp $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
function smarty_block_itemfield($params, $content, &$smarty, &$repeat) {
	include_once('lib/wiki-plugins/wikiplugin_trackeritemfield.php');
	if (!$repeat) // only on closing tag
		if (($res = wikiplugin_trackeritemfield($content, $params))!== false)
			echo $res;
}
?>
