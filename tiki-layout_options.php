<?php # $Header: /cvsroot/tikiwiki/tiki/tiki-layout_options.php,v 1.2 2003-01-04 19:34:16 rossta Exp $

$section_top_bar=$section.'_top_bar';
$section_bot_bar=$section.'_bot_bar';
$section_left_column=$section.'_left_column';
$section_right_column=$section.'_right_column';
$smarty->assign('feature_top_bar',$$section_top_bar);
$smarty->assign('feature_bot_bar',$$section_bot_bar);
$smarty->assign('feature_left_column',$$section_left_column);
$smarty->assign('feature_right_column',$$section_right_column);
?>