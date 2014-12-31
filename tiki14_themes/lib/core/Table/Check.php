<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
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
 * This is a public class for checking necessary preferences or tablesorter status
 *
 * @package Tiki
 * @subpackage Table
 */
class Table_Check
{
	/**
	 * Checks to see if necessary preferences are set to allow tablesorter to be used either with or without ajax
	 *
	 * @param bool $ajax    if set to true will check that appropriate preference is set to be able to use ajax
	 * @return bool
	 */
	static public function isEnabled($ajax = false)
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

	/**
	 * Checks to see whether the file has been accessed through a tablesorter ajax call
	 * @return bool
	 */
	static public function isAjaxCall()
	{
		if (isset($_GET['tsAjax']) && $_GET['tsAjax'] === 'y') {
			return true;
		} else {
			return false;
		}
	}
}