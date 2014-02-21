<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
 * Class Table_Check
 * This is the public class for checking whether necessary preferences are set to use tablesorter
 *
 * @package Tiki
 * @subpackage Table
 */
class Table_Check
{
	static public function perms($ajax = false)
	{
		global $prefs;
		if ($prefs['disableJavascript'] == 'n' && $prefs['feature_jquery_tablesorter'] == 'y') {
			if ($ajax === true) {
				if ($prefs['feature_ajax'] == 'y') {
					return true;
				} else {
					return false;
				}
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
}