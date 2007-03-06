<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-layout_options.php,v 1.8 2007-03-06 19:29:49 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$section_top_bar = $section . '_top_bar';

$section_bot_bar = $section . '_bot_bar';
$section_left_column = $section . '_left_column';
$section_right_column = $section . '_right_column';
$smarty->assign('feature_top_bar', $$section_top_bar);
$smarty->assign('feature_bot_bar', $$section_bot_bar);
$smarty->assign('feature_left_column', $$section_left_column);
$smarty->assign('feature_right_column', $$section_right_column);

?>
