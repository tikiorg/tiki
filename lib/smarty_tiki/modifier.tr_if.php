<?php
// CVS: $Id: modifier.tr_if.php,v 1.2 2007-02-08 13:51:22 sylvieg Exp $

// Translate only if feature_multilingual is on

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_modifier_tr_if($source) {
	global $feature_multilingual,$language;
	if ($feature_multilingual == 'y' && $language != 'en') {
		include_once('lib/init/tra.php');
		return tra($source);
	} else {
		return $source;
	}
}
?>
