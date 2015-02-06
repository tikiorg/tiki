<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
global $reportFullscreen, $index, $values;
$headerlib = TikiLib::lib('header');
$smarty = TikiLib::lib('smarty');
$access = TikiLib::lib('access');

$access->check_feature('feature_reports');

TikiLib::lib("sheet")->setup_jquery_sheet();

$headerlib
	->add_jsfile('lib/core/Report/Builder.js')
	->add_jq_onready('$.reportInit();');
	
$smarty->assign('definitions', Report_Builder::listDefinitions());

if (!empty($reportFullscreen)) {
	$smarty->assign('index', $index);
	$smarty->assign('values', $values);
	$smarty->assign('reportFullscreen', 'true');
	$smarty->display('tiki-edit_report.tpl');
} else {
	$smarty->assign('mid', 'tiki-edit_report.tpl');
	$smarty->display("tiki.tpl");
}
