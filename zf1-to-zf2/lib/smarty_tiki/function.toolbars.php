<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * Smarty plugin to display content only to some groups
 */

function smarty_function_toolbars($params, $smarty)
{
	global $prefs, $is_html, $tiki_p_admin, $tiki_p_admin_toolbars, $section;
	$default = array(
		'comments' => 'n',
		'is_html' => $is_html,
		'section' => $section,
	);
	$params = array_merge($default, $params);

	if ($prefs['javascript_enabled'] != 'y') {
		return '';
	}
	// some tool filters to help roll out textarea & toolbars to more sections quickly (for 4.0)
	$hidden = array();
	if ( (!isset( $params['switcheditor'] ) && !in_array($params['section'], array('wiki page', 'blogs', 'newsletters', 'cms', 'webmail'))) || $params['switcheditor'] !== 'y') {
		$hidden[] = 'switcheditor';
	}
	
	if( $tiki_p_admin != 'y' || $tiki_p_admin_toolbars != 'y' ) {
		$hidden[] = 'admintoolbar';
	}

	if ( ! isset( $params['area_id'] ) ) {
		$params['area_id'] = 'editwiki';
	}

	include_once( 'lib/toolbars/toolbarslib.php' );
	$list = ToolbarsList::fromPreference($params, $hidden);
	if ( isset($params['_wysiwyg']) && $params['_wysiwyg'] == 'y') {
		return $list->getWysiwygArray($params['area_id'], $params['is_html']);
	} else {
		return $list->getWikiHtml($params['area_id'], $params['comments']);
	}
}

