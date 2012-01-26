<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-information.php 33195 2011-03-02 17:43:40Z changi67 $

require_once ('tiki-setup.php');
if (isset($_REQUEST['msg'])) {
	$smarty->assign('msg', $_REQUEST['msg']);
}
if (isset($_REQUEST['show_history_back_link'])) {
	$smarty->assign('show_history_back_link', $_REQUEST['show_history_back_link']);
}
$smarty->assign('mid', 'tiki-information.tpl');
$smarty->display("tiki.tpl");
