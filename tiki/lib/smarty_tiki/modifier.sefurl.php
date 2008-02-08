<?php
// CVS: $Id: modifier.sefurl.php,v 1.1.2.1 2008-02-08 23:13:18 sylvieg Exp $

// Translate only if feature_multilingual is on

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_modifier_sefurl($source, $type='wiki') {
	global $prefs, $wikilib;
	switch($type){
	case 'wiki page':
	case 'wiki':
		return $wikilib->sefurl($source);
	}
	return $source;
}
?>
