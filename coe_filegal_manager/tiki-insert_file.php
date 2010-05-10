<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-upload_file.php,v 1.65.2.4 2008-03-11 15:17:54 nyloth Exp $
$section = 'file_galleries';
require_once ('tiki-setup.php');
if ($prefs['feature_file_galleries'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled") . ": feature_file_galleries");
	$smarty->display("error.tpl");
	die;
}
include_once ('lib/filegals/filegallib.php');
if ($prefs['feature_groupalert'] == 'y') {
	include_once ('lib/groupalert/groupalertlib.php');
}
@ini_set('max_execution_time', 0); //will not work in safe_mode is on
$auto_query_args = array('as', 'file');

$fileinfo = $tikilib->get_file($_REQUEST["file"]);
$filesize = $fileinfo["filesize"];
$filedate = date("D, M d, Y", $fileinfo["created"]);

if ($filesize>1000*1000*1000) 
	$filesize = round($filesize/(1024*1024*1024),2)."Gb";
else if ($filesize>1000*1000) 
	$filesize = round($filesize/(1024*1024),2)."Mb";
else if ($filesize>1000) 
	$filesize = round($filesize/(1024))."Kb";
else
	$filesize = $filesize."b";

$smarty->assign("file", $_REQUEST["file"]);
$smarty->assign("filesize", $filesize);
$smarty->assign("filedate", $filedate);
$smarty->assign("as", $_REQUEST["as"]);
$smarty->display("insert.tpl");
