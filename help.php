<?php

// $Id: /cvsroot/tikiwiki/tiki/help.php,v 1.5.2.1 2007-11-25 20:58:11 mose Exp $

// Initialization

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
	exit;
}

require_once ('tiki-setup.php');

$access->check_feature('feature_wiki');

include_once ('lib/wiki/wikilib.php');
$plugins = $wikilib->list_plugins(true);
$smarty->assign_by_ref('plugins', $plugins);
$smarty->display("tiki-edit_help.tpl");
