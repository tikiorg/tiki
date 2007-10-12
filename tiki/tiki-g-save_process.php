<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-g-save_process.php,v 1.9 2007-10-12 07:55:27 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/Galaxia/ProcessManager.php');

if ($prefs['feature_workflow'] != 'y') {
	die;
}

if ($tiki_p_admin_workflow != 'y') {
	die;
}

// The galaxia process manager PHP script.

/*
// Check if feature is enabled and permissions
if($prefs['feature_galaxia'] != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("error.tpl");
  die;  
}
*/

// Check if we are editing an existing process
// if so retrieve the process info and assign it.
if (!isset($_REQUEST['pid']))
	$_REQUEST['pid'] = 0;

header ('Content-type: text/xml');
echo ('<?xml version="1.0"?>');
$data = $processManager->serialize_process($_REQUEST['pid']);
echo $data;

?>
