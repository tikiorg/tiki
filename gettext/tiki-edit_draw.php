<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = "draw";
require_once ('tiki-setup.php');
require_once ('lib/svg-edit_tiki/draw.php');

$access->check_feature('feature_draw');

$draw = new TikiDraw();
$draw->setup_draw();

include_once ("categorize_list.php");
include_once ('tiki-section_options.php');
ask_ticket('draw');
//$smarty->display('tiki-edit_draw.tpl');

// Display the template
$smarty->assign('mid', 'tiki-edit_draw.tpl');
$smarty->display("tiki.tpl");
echo "test";
