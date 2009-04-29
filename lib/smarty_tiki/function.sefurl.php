<?php
// CVS: $Id: modifier.sefurl.php,v 1.1.2.2 2008-02-16 22:40:31 sylvieg Exp $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
if (!function_exists('smarty_function_sefurl')) {
	function smarty_function_sefurl($params, &$smarty) {
		global $prefs;
		global $wikilib; include_once('lib/wiki/wikilib.php');
	
		// structure only yet
		if (isset($params['structure'])) {
			if ($prefs['feature_sefurl'] != 'y' || (isset($params['sefurl']) && $params['sefurl'] == 'n')) {
				$url = 'tiki-index.php?page='.urlencode($params['page']).'&amp;structure='.urlencode($params['structure']);
			} else {
				$url = $wikilib->sefurl($params['page']);
				$url .= '&amp;structure='.urlencode($params['structure']);
				//$url .= '&amp;page_ref_id='.$params['page_ref_id'];
			}
		}
		return $url;
	}
}
