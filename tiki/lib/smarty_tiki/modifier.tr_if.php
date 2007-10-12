<?php
// CVS: $Id: modifier.tr_if.php,v 1.3 2007-10-12 07:55:47 nyloth Exp $

// Translate only if feature_multilingual is on

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_modifier_tr_if($source) {
	global $prefs;
	if ($prefs['feature_multilingual'] == 'y' && $prefs['language'] != 'en') {
		include_once('lib/init/tra.php');
		return tra($source);
	} else {
		return $source;
	}
}
?>
