<?php
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once 'wslib.php';

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
    public function check_if_new_to_ws()
    {
	global $prefs, $tikilib;
	if ($prefs['ws_container'] == null)
	{ 
	    $currentTime = (string) time();
	    $hash = md5($currentTime);
	    $ws = new wslib();
	    $id = $ws->init_ws($hash);
	    $tikilib->set_preference('ws_container', $id);
	    return true;
	}
	else
	    return false;
    }

    public function prueba()
    {
	global $tikilib;
	$ws = new wslib();
	$ws->exist_ws_child("Circuitos electricos 2", '0');
    }
}

