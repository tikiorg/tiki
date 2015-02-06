<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

class TikiHelpLib
{
	/*
	function TikiHelpLib()
	{

	}
	*/
/* end of class */
}


/**
 *  Returns a single html-formatted crumb
 *  @param crumb a breadcrumb instance, or
 *  @param url, desc:  a doc page and associated (already translated) help description
 */
/* static */
function help_doclink($params)
{
	global $prefs;

	extract($params);
	// Param = zone
		$ret = '';
	if (empty($url) && empty($desc) && empty($crumb)) {
		return;
	}

	if (!empty($crumb)) {
		$url = $crumb->helpUrl;
		$desc = $crumb->helpDescription;
	}

	if ($prefs['feature_help'] == 'y' and $url) {
		if (!isset($desc)) {
			$smarty = TikiLib::lib('smarty');
			$smarty->loadPlugin('smarty_function_icon');
			$desc = tra('Help link');

			$ret = '<a title="' . $url . '|' . htmlentities($desc, ENT_COMPAT, 'UTF-8') . '" href="'
						. $prefs['helpurl'] . $url . '" target="tikihelp" class="tikihelp btn btn-link">'
						. smarty_function_icon(array('name' => 'help'), $smarty)
						. '</a>';
		}
	}

	return $ret;
}
