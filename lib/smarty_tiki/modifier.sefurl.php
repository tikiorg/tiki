<?php
// CVS: $Id: modifier.sefurl.php,v 1.1.2.2 2008-02-16 22:40:31 sylvieg Exp $

// Translate only if feature_multilingual is on

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_modifier_sefurl($source, $type='wiki') {
	global $prefs, $wikilib;
	include_once('lib/wiki/wikilib.php');
	switch($type){
	case 'wiki page':
	case 'wiki':
		return $wikilib->sefurl($source);
	case 'blog':
	        if ($prefs['feature_sefurl'] == 'y') {
	            return "blog" . $source;
	        } else {
	            return 'tiki-view_blog.php?blogId='.$source;
		}
	}
	return $source;
}
?>
