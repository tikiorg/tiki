<?php
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once ('wslib.php');

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
 * @author	Aldo B.G (axold) <axold07@gmail.com>
 */

/**
 * wsGuiParser
 *
 * @category	TikiWiki
 * @package	lib/workspaces
 * @version	$Id
 */
class wsGuiController  
{
    function checkIfNewToWS()
    {
	global $prefs, $tikilib;
	if ($prefs['new_to_ws'] == 'y')
	{
	    //do{
		$currentTime = (string) time();
		$hash = md5($currentTime);
	    //}while (($id = Workspacelib::init_ws($hash)) == -1);
		//$tikilib->set_preference('new_to_ws', 'n');
		$tikilib->set_preference('ws_container', $id);
		wslib::init_ws();
	}
    }
}

