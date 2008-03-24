<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-section_options.php,v 1.15 2007-10-12 07:55:32 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/tiki-section_options.php,v 1.15 2007-10-12 07:55:32 nyloth Exp $

if ($prefs['feature_theme_control'] == 'y') {
	include ('tiki-tc.php');
}

if ($prefs['feature_banning'] == 'y') {
	if ($msg = $tikilib->check_rules($user, $section)) {
		$smarty->assign('msg', $msg);
		$smarty->display("error.tpl");
		die;
	}
}

if ($prefs['layout_section'] == 'y') {
	$section_elements = array('top_bar', 'bot_bar', 'left_column', 'right_column');
	foreach ( $section_elements as $element ) {
		if ( isset($prefs[$section.'_'.$element]) ) {
			$prefs['feature_'.$element] = $prefs[$section.'_'.$element];
		}
	}
}

?>
