<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
			}
			if (isset($_REQUEST['no_bl']) && $_REQUEST['no_bl'] === 'y') {
				$url .= '&amp;no_bl=y';
			}
		}
		if ($prefs['page_n_times_in_a_structure'] == 'y') {
			$url .= '&amp;page_ref_id='.$params['page_ref_id'];
		}
		return $url;
	}
}
