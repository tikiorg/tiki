<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-admin_include_connect.php 25076 2010-02-11 15:53:20Z changi67 $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

require_once('lib/socialnetworkslib.php');

if (isset($_REQUEST["socialnetwork"])) {
	check_ticket('admin-inc-socialnetworks');
}
ask_ticket('admin-inc-socialnetworks');

$url=$socialnetworkslib->getURL();
$url=substr($url,0,strrpos($url,'/')+1);
$smarty->assign('url',$url);
