<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* return the attributes for a standard tiki page body tag
 * jonnyb refactoring for tiki5
 * eromneg adding additional File Gallery popup body class
 */

function smarty_function_html_body_attributes($params, &$smarty) {
	global $section, $prefs, $cookietab, $page, $smarty, $tiki_p_edit, $section_class, $user;
	
	$back = '';
	$onload = '';
	$class = '';
	
	$dblclickedit = $smarty->get_template_vars('dblclickedit');
	
	if (isset($section) && $section == 'wiki page' && $prefs['user_dbl'] == 'y' and $dblclickedit == 'y' and $tiki_p_edit == 'y') {
		$back .= ' ondblclick="location.href=\'tiki-editpage.php?page='.rawurlencode($page).'\';"';
	}
	
	if (isset($section_class)) {
		$class .= 'tiki '.$section_class;
	}
	
	if ($prefs['feature_fixed_width'] == 'y') {
		$class .= ' fixed_width ';
	}
	
	if (!empty($_REQUEST['filegals_manager'])) {
		$class .= ' filegal_popup ';
	}
		
	if (isset($_SESSION['fullscreen']) && $_SESSION['fullscreen'] == 'y') {
		$class .= empty($class) ? ' ' : '';
		$class .= 'fullscreen';
	}

	if ($prefs['layout_add_body_group_class'] === 'y') {
		if (empty($user)) {
			$class .= ' grp_Anonymous';
		} else if (TikiLib::lib('user')->user_is_in_group($user, 'Registered')) {
			$class .= ' grp_Registered';
			if (TikiLib::lib('user')->user_is_in_group($user, 'Admins')) {
				$class .= ' grp_Admins';
			}
		}
	}
	
	if (!empty($onload)) {
		$back .= ' onload="'.$onload.'"';
	}
	
	if (!empty($class)) {
		$back .= ' class="'.$class.'"';
	}
	
	return $back;
	
}
