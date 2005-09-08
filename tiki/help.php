<?php

// $Header: /cvsroot/tikiwiki/tiki/help.php,v 1.4 2005-09-08 01:33:00 damosoft Exp $

// Initialization
require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

include_once ('lib/wiki/wikilib.php');
include_once ('lib/structures/structlib.php');
include_once ('lib/notifications/notificationlib.php');

if ($feature_wiki != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

  $smarty->display("error.tpl");
  die;
}

// 27-Jun-2003, by zaufi
// Get plugins with descriptions
global $wikilib;
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

$smarty->display("tiki-edit_help.tpl");

?>