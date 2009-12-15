<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-section_options.php,v 1.15 2007-10-12 07:55:32 nyloth Exp $
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
	foreach($section_elements as $element) {
		if (isset($prefs[$section . '_' . $element])) {
			$prefs['feature_' . $element] = $prefs[$section . '_' . $element];
		}
	}
}
