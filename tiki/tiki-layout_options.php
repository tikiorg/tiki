<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-layout_options.php,v 1.4 2004-03-28 07:32:23 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/tiki-layout_options.php,v 1.4 2004-03-28 07:32:23 mose Exp $
$section_top_bar = $section . '_top_bar';

$section_bot_bar = $section . '_bot_bar';
$section_left_column = $section . '_left_column';
$section_right_column = $section . '_right_column';
$smarty->assign('feature_top_bar', $$section_top_bar);
$smarty->assign('feature_bot_bar', $$section_bot_bar);
$smarty->assign('feature_left_column', $$section_left_column);
$smarty->assign('feature_right_column', $$section_right_column);

?>