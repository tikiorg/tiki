<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * smarty_block_title : add a title to a template.
 *
 * params:
 *    help: name of the doc page on doc.tiki.org
 *    admpage: admin panel name
 *    url: link on the title
 *
 * usage: {title help='Example' admpage='example'}{tr}Example{/tr}{/title}
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_title($params, $content, $template, &$repeat)
{
	global $prefs, $tiki_p_view_templates, $tiki_p_edit_templates, $tiki_p_admin;

	if ( $repeat || empty($content) ) return;

	$template->loadPlugin('smarty_function_icon');

	if ( ! isset($params['help']) ) $params['help'] = '';
	if ( ! isset($params['admpage']) ) $params['admpage'] = '';
	if ( ! isset($params['url']) ) {
		$template->loadPlugin('smarty_function_query');
		$params['url'] = htmlspecialchars(smarty_function_query(array('_type' => 'absolute_path'), $template));
		$params['url'] = str_replace('&amp;amp;', '&', $params['url']);
	}

	// Set the variable for the HTML title tag
	$template->smarty->assign('headtitle', $content);

	$class = 'pagetitle';
	$current = current_object();
	$metadata = '';
	$coordinates = TikiLib::lib('geo')->get_coordinates($current['type'], $current['object']);
	if ($coordinates) {
		$class = ' geolocated primary';
		$metadata = " data-geo-lat=\"{$coordinates['lat']}\" data-geo-lon=\"{$coordinates['lon']}\"";
		
		if (isset($coordinates['zoom'])) {
			$metadata .= " data-geo-zoom=\"{$coordinates['zoom']}\"";
		}
	}

	$html = '<h1>';
	$html .= '<a class="' . $class . '"' . $metadata . ' href="' . $params['url'] . '">' . htmlspecialchars($content) . "</a>\n";

	if ($template->getTemplateVars('print_page') != 'y') {
		if ( $prefs['feature_help'] == 'y' && $prefs['helpurl'] != '' && $params['help'] != '' ) {
			$html .= '<a href="' . $prefs['helpurl'] . rawurlencode($params['help']) . '" class="titletips" title="' . tra('Help page:') . ' ' . htmlspecialchars($content) . '" target="tikihelp">'
			. smarty_function_icon(array('_id' => 'help'), $template)
			. "</a>\n";
		}

		if ($prefs['feature_edit_templates'] == 'y' && $tiki_p_edit_templates == 'y' && ($tpl = $template->getTemplateVars('mid'))) {
			$html .= '<a href="tiki-edit_templates.php?template=' . $tpl . '" class="titletips" title="' . tra('View or edit tpl:') . ' ' . htmlspecialchars($content) . '">'
			. smarty_function_icon(array('_id' => 'shape_square_edit', 'alt' => tra('Edit Template')), $template)
			. "</a>\n";
		} elseif ($prefs['feature_view_tpl'] == 'y' &&  $tiki_p_view_templates == 'y' && ($tpl = $template->getTemplateVars('mid'))) {
			$html .= '<a href="tiki-edit_templates.php?template=' . $tpl . '" class="titletips" title="' . tra('View tpl:') . ' ' . htmlspecialchars($content) . '">'
			. smarty_function_icon(array('_id' => 'shape_square', 'alt' => tra('View Template')), $template)
			. "</a>\n";
			
		}

		if ( $tiki_p_admin == 'y' && $params['admpage'] != '' ) {
			$html .= '<a class="titletips" href="tiki-admin.php?page=' . $params['admpage'] . '" title="' . tra('Admin page:') . ' ' . htmlspecialchars($content) . '">'
			. smarty_function_icon(array('_id' => 'wrench', 'alt' => tra('Admin Feature')), $template)
			. "</a>\n";
		}
	}

	$html .= '</h1>';

	return $html;
}
