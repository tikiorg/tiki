<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$access->check_permission('tiki_p_admin');
include_once ('lib/directory/dirlib.php');
$tmp1 = isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : "";
$tmp2 = isset($_SERVER["PHP_SELF"]) ? $_SERVER["PHP_SELF"] : "";
// concat all, remove the // between server and path and then
// remove the name of the script itself:
$url = $tmp1 . dirname($tmp2);
$info = Array();
$info["name"] = $prefs['browsertitle'];
$info["description"] = '';
$info["url"] = $url;
$info["country"] = 'None';
$info["isValid"] = 'n';
$smarty->assign_by_ref('info', $info);
$countries = $tikilib->get_flags();
$smarty->assign_by_ref('countries', $countries);
// Display the template
$smarty->assign('mid', 'tiki-register_site.tpl');
$smarty->display("tiki.tpl");
