<?php
/*
 * tiki-workspaces-index.php - TikiWiki CMS/GroupWare
 *
 * This is the index page of WS, with it a normal user can search his WS, other WS, and more stuff
 * 
 * @author 	Aldo Borrero Gonzalez (axold) <axold07@gmail.com>
 * @license	http://www.opensource.org/licenses/lgpl-2.1.php
 */

//Basic import
require_once 'tiki-setup.php';

//Rest of Imports

//Assign the title to the template
$smarty->assign('headtitle', tra('Workspaces Home'));

// Display the template
$smarty->assign('mid', 'tiki-workspaces-index.tpl');
$smarty->display("tiki.tpl");
