<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * Smarty plugin to display content only to some groups
 */

function smarty_function_toolbars($params, $smarty)
{
	global $prefs, $is_html;
	$default = array('comments' => '', 'is_html' => $is_html);
	$params = array_merge($default, $params);
	
	if ($prefs['javascript_enabled'] != 'y') {
		return '';
	}
	if ( ! isset( $params['section'] ) ) {
		global $section;
		if ( ! empty($section) ) {
			$params['section'] = $section;
		} else {
			return '';	// proper features set $section, contact us (possibly others) don't
		}
	}

	if ( isset( $params['comments'] ) && $params['comments'] == 'y' ) {
		$comments = true;
	} else {
		$comments = false;
	}
	
	// some tool filters to help roll out textarea & toolbars to more sections quickly (for 4.0)

	$hidden = array();
//	if ($params['section'] != 'wiki page' && $params['section'] != 'blogs' && $params['section'] != 'newsletters' && $params['section'] != 'trackers') {
//		$hidden[] = 'fullscreen';
//	}
	if ( (!isset( $params['switcheditor'] ) && !in_array($params['section'], array('wiki page', 'blogs', 'newsletters', 'cms', 'webmail'))) || $params['switcheditor'] !== 'y') {
		$hidden[] = 'switcheditor';
	}
	
	if ( ! isset( $params['area_id'] ) ) {
		$params['area_id'] = 'editwiki';
	}

	include_once( 'lib/toolbars/toolbarslib.php' );
	$list = ToolbarsList::fromPreference($params['section'] . ($comments ? '_comments' : ''), $hidden);
	if ( isset($params['_wysiwyg']) && $params['_wysiwyg'] == 'y') {
		return $list->getWysiwygArray( "'+CurrentEditorName+'", $params['is_html']);
	} else {
		return $list->getWikiHtml($params['area_id'], $params['comments']);
	}
}

