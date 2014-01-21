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
 * Class Table_Factory
 * This is the public class called to apply jQuery Tablesorter plugin functionality to tables in Tiki.
 * Also used by other classes to direct which class to call
 *
 * @package Tiki
 * @subpackage Table
 */
class Table_Factory
{
	/**
	 * This is the public function called to apply jQuery Tablesorter to a table.
	 *
	 * @param        $name			must correspond to a file in Table/Settings
	 * @param null   $settings		optional user-defined settings array which will override defaults
	 * 									- can be partial, ie only part of the array defined
	 * @param string $type			used along with $name to tell this function which class to call
	 *
	 * @return bool|Table_Manager
	 */
	static public function build($name, $settings = null, $type = 'manager')
	{
		global $prefs;
		if ($prefs['disableJavascript'] == 'n' && $prefs['feature_jquery_tablesorter'] == 'y') {
			switch ($type) {
				case 'manager':
					return new Table_Manager(Table_Factory::build($name, $settings, 'table')->s);
					break;
				case 'table':
					$class = 'Table_Settings_' . ucfirst($name);
					return new $class($settings);
					break;
				case 'code':
					$class = 'Table_Code_' . ucfirst($name);
					if (is_array($settings)) {
						return new $class($settings);
					} else {
						return false;
					}
					break;
				case 'plugin':
					return new Table_Plugin;
			}
		} else {
			return false;
		}
	}
}