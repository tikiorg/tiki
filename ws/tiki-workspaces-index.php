<?php
/*
 * tiki-workspaces-index.php - TikiWiki CMS/GroupWare
 *
 * This is the index page of WS, with it a normal user can search his WS, other WS, and more stuff
 * 
 * @author 	Aldo Borrero Gonzalez (axold) <axold07@gmail.com>
  * @author 	Benjamín Palacios Gonzalo (mangapower) <mangapowerx@gmail.com>
 * @license	http://www.opensource.org/licenses/lgpl-2.1.php
 */

//Basic import
require_once 'tiki-setup.php';
require_once('lib/workspaces/wslib.php');

//Rest of Imports

//Assign the title to the template
$smarty->assign('headtitle', tra('Workspaces Home'));

$listWS = $wslib->list_ws_that_user_have_access ($user, 10, 0);
$smarty->assign('listWS',$listWS);

// Display the template
$smarty->assign('mid', 'tiki-workspaces-index.tpl');
$smarty->display("tiki.tpl");
