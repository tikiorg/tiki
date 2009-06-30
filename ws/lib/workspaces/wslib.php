<?php
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * TikiWiki CMS/GroupWare
 *
 * LICENSE
 *
 * This source file is subject to the LGPL license that is bundled
 * with this package in the file license.txt. If your distribuition 
 * doesn't have the license file, please go to http://license.com to see 
 * the complete license of the software.
 *
 * @category   	workspaces
 * @package   	lib
 * @author	Benjamin Palacios (mangapower) <mangapowerx@gmail.com>
 */

include_once 'lib/categories/categlib.php';

/**
 * wslib
 *
 * @category	TikiWiki
 * @package	lib/workspaces
 * @version	$Id
 */
class wslib extends CategLib 
{
    function __construct()
    {
	global $dbTiki;
	parent::CategLib($dbTiki);
    }

    public function init_ws($name)
    {
	return parent::add_category(0, $name, 'Workspaces Container');
    }

    public function add_ws($name, $parentWS)
    {
	global $prefs;
	$wsContainerId = (int) $prefs['ws_container'];
	return parent::add_category($wsContainerId, $name, $parentWS);
    }

    public function exist_ws_child($name, $parentWS)
    {
	global $prefs;
	$query = "select `categId` from `tiki_categories` where `name`=? and `description`=? and `parentId`=?";
	$wsContainerId = (int) $prefs['ws_container'];
	$bindvars = array($name, $parentWS, $wsContainerId);
	return $this->getOne($query, $bindvars);
    }
}
