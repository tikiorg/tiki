<?php
/**
 * wsController.php - TikiWiki CMS/GroupWare
 *
 * This file will manage all actions can perform the user in 
 * tiki-admin-include-workspaces.php
 * @package   	lib
 * @author	Aldo B.G (axold) <axold07@gmail.com>
 * @license	http://www.opensource.org/licenses/lgpl-2.1.php
 */

//Controlling access
require_once 'tiki-setup.php';
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

//Rest of imports
require_once 'wslib.php';

/**
 * wsGuiParser
 *
 * @subpackage	workspaces
 * @version	0.1
 */
class ws_gui_controller  
{
    public function check_if_new_to_ws()
    {
	global $prefs, $tikilib, $wslib;
	if ($prefs['ws_container'] == null)
	{ 
	    $currentTime = (string) time();
	    $hash = md5($currentTime);
	    $id = $wslib->init_ws($hash);
	    $tikilib->set_preference('ws_container', $id);
	    $tikilib->set_preference('ws_container_name', $hash);
	    return true;
	}
	else
	    return false;
    }

    public function list_ws_resources()
    {
	global $wslib, $smarty;
	
	$catree = $wslib->list_all_ws(0,-1, 'name_asc','','',0);
	$smarty->assign('catree', $catree);
    }
}
