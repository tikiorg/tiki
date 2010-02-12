<?php
/* $Id $
 * 
 * return the attributes for a standard tiki page body tag
 * jonnyb refactoring for tiki5
 * 
 */

function smarty_function_html_body_attributes($params, &$smarty) {
	global $section, $prefs, $cookietab, $page, $smarty, $tiki_p_edit, $section_class;
	
	$back = '';
	$onload = '';
	$class = '';
	
	$dblclickedit = $smarty->get_template_vars('dblclickedit');
	
	if (isset($section) && $section == 'wiki page' && $prefs['user_dbl'] == 'y' and $dblclickedit == 'y' and $tiki_p_edit == 'y') {
		$back .= ' ondblclick="location.href=\'tiki-editpage.php?page='.rawurlencode($page).'\';"';
	}
	
	if ($prefs['feature_tabs'] == 'y') {
		$onload .= 'tikitabs('.$cookietab.',50);';
	}
	
	// this appears to be unused - smarty var $msgError is never assigned 
	// {if $msgError} javascript:location.hash='msgError'{/if}"
	
	if (isset($section_class)) {
		$class .= 'tiki '.$section_class;
	}
	
	if ($_SESSION['fullscreen'] == 'y') {
		$class .= empty($class) ? ' ' : '';
		$class .= 'fullscreen';
	}
	
	if (!empty($onload)) {
		$back .= ' onload="'.$onload.'"';
	}
	
	if (!empty($class)) {
		$back .= ' class="'.$class.'"';
	}
	
	return $back;
	
}