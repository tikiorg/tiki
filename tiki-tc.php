<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-tc.php,v 1.4 2004-03-28 07:32:23 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
if ($feature_theme_control == 'y') {
	// defined: $cat_type and cat_objid
	// search for theme for $cat_type
	// then search for theme for md5($cat_type.cat_objid)
	include_once ('lib/themecontrol/tcontrol.php');

	include_once ('lib/categories/categlib.php');

	//SECTIONS
	if (isset($section)) {
		$tc_theme = $tcontrollib->tc_get_theme_by_section($section);
	}

	// CATEGORIES
	if (isset($cat_type) && isset($cat_objid)) {
		$tc_categs = $categlib->get_object_categories($cat_type, $cat_objid);

		$tc_theme = '';

		if (count($tc_categs)) {
			$tc_theme = $tcontrollib->tc_get_theme_by_categ($tc_categs[0]);
		}
	}

    // OBJECTS - if object has been particularly set, override SECTION or CATEGORIES $tc_theme
	// if not set, make sure we don't squash whatever $tc_theme may have been
    if (isset($cat_type) && isset($cat_objid)) {
        if( $obj_theme = $tcontrollib->tc_get_theme_by_object($cat_type, $cat_objid) ) {
            $tc_theme = $obj_theme;
        }
    }
										
	if ($tc_theme) {
		$style = $tc_theme;

		$tc_parts = explode('.', $style);
		$style_base = $tc_parts[0];
		$smarty->assign('style', $style);
		$smarty->assign('style_base', $style_base);
	}
}

?>
