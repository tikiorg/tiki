<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* return the attributes for a standard tiki page body tag
 * jonnyb refactoring for tiki5
 * eromneg adding additional File Gallery popup body class
 */

function smarty_function_html_body_attributes($params, $smarty)
{
	global $section, $prefs, $page, $tiki_p_edit, $section_class, $user;
	$smarty = TikiLib::lib('smarty');
	$back = '';
	$onload = '';
	$class = isset($params['class']) ? $params['class'] : '';
	
	$dblclickedit = $smarty->getTemplateVars('dblclickedit');
	
	if (isset($section) && $section == 'wiki page' && $prefs['user_dbl'] == 'y' and $dblclickedit == 'y' and $tiki_p_edit == 'y') {
		$back .= ' ondblclick="location.href=\'tiki-editpage.php?page=' . rawurlencode($page) . '\';"';
	}

	$class .= ' tiki ';
	
	if (isset($section_class)) {
		$class .= $section_class;
	}
	
	if ($prefs['feature_fixed_width'] == 'y') {
		$class .= ' fixed_width ';
	}

    if ($prefs['site_layout']) {
        $class .= ' layout_' . $prefs['site_layout'];
    }
	
	if (!empty($_REQUEST['filegals_manager'])) {
		$class .= ' filegal_popup ';
	}
		
	if (isset($_SESSION['fullscreen']) && $_SESSION['fullscreen'] == 'y') {
		$class .= empty($class) ? ' ' : '';
		$class .= ' fullscreen';
	}

	if (isset($prefs['layout_add_body_group_class']) && $prefs['layout_add_body_group_class'] === 'y') {
		if (empty($user)) {
			$class .= ' grp_Anonymous';
		} else if (TikiLib::lib('user')->user_is_in_group($user, 'Registered')) {
			$class .= ' grp_Registered';
			if (TikiLib::lib('user')->user_is_in_group($user, 'Admins')) {
				$class .= ' grp_Admins';
			}
		}
	}

	if ($prefs['feature_perspective'] == 'y' && isset($_SESSION['current_perspective'])) {
		$class .= ' perspective' . $_SESSION['current_perspective'];
		$class .= ' perspective_' . preg_replace("/[^a-z0-9]/", "_", strtolower($_SESSION['current_perspective_name']));
	}

	if ($categories = $smarty->getTemplateVars('objectCategoryIds')) {
		foreach ($categories as $cat) {
			if (in_array($cat, $prefs['categories_add_class_to_body_tag'])) {
				$class .= ' cat_' . str_replace(' ', '-', TikiLib::lib('categ')->get_category_name($cat));
			}
		}
	}
	
	if (!empty($onload)) {
		$back .= ' onload="' . $onload . '"';
	}
	
	if (!empty($class)) {
		$back .= ' class="' . $class . '"';
	}
	
	return $back;
	
}
