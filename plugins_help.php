<?php 
// $Header: /cvsroot/tikiwiki/tiki/plugins_help.php,v 1.1 2007-03-16 15:01:29 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');  
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

global $wikilib; include_once('lib/wiki/wikilib.php');
$plugin_files = $wikilib->list_plugins();
$plugins = array();

// Request help string from each plugin module
foreach ($plugin_files as $pfile) {
  $pinfo["file"] = $pfile;

  $pinfo["help"] = $wikilib->get_plugin_description($pfile);
  $pinfo["name"] = strtoupper(str_replace(".php", "", str_replace("wikiplugin_", "", $pfile)));
  $plugins[] = $pinfo;
}

$smarty->assign_by_ref('plugins', $plugins);
?>