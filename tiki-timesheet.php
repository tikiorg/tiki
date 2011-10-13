<?php
require_once('tiki-setup.php');

$headerlib = TikiLib::lib("header");

$headerlib->add_cssfile("lib/jquery/jtrack/css/jtrack.css");
$headerlib->add_jsfile("lib/jquery/jtrack/js/domcached-0.1-jquery.js");
$headerlib->add_jsfile("lib/jquery/jtrack/js/jtrack.js");
$headerlib->add_jq_onready("
	jTask.init();
");
$smarty->assign('mid', 'tiki-timesheet.tpl');
// use tiki_full to include include CSS and JavaScript
$smarty->display("tiki.tpl");