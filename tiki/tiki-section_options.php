<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-section_options.php,v 1.6 2003-11-17 15:44:29 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/tiki-section_options.php,v 1.6 2003-11-17 15:44:29 mose Exp $
if ($feature_theme_control == 'y') {
	include ('tiki-tc.php');
}

if ($feature_banning == 'y') {
	if ($msg = $tikilib->check_rules($user, $section)) {
		$smarty->assign('msg', $msg);

		$smarty->display("error.tpl");
		die;
	}
}

if ($layout_section == 'y') {
	$section_top_bar = $section . '_top_bar';

	$section_bot_bar = $section . '_bot_bar';
	$section_left_column = $section . '_left_column';
	$section_right_column = $section . '_right_column';

	if (isset($$section_top_bar)) {
		$smarty->assign('feature_top_bar', $$section_top_bar);

		$smarty->assign('feature_bot_bar', $$section_bot_bar);
		$smarty->assign('feature_left_column', $$section_left_column);
		$smarty->assign('feature_right_column', $$section_right_column);
	} else {
		$smarty->assign('feature_top_bar', 'y');

		$smarty->assign('feature_bot_bar', 'y');
		$smarty->assign('feature_left_column', 'y');
		$smarty->assign('feature_right_column', 'y');
	}
}

?>