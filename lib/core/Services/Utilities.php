<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * Class Services_Utilities
 */
class Services_Utilities
{
	/**
	 * Provide referer url if javascript not enabled.
	 * 
	 * @return bool|string
	 */
	static function noJsPath ()
	{
		global $prefs;
		if ($prefs['javascript_enabled'] !== 'y') {
			global $base_url;
			$referer = substr($_SERVER['HTTP_REFERER'], strlen($base_url));
		} else {
			$referer = false;
		}
		return $referer;
	}
}
