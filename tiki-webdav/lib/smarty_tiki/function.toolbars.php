<?php
/*
 * $Id$
 *
 * Smarty plugin to display content only to some groups
 */

function smarty_function_toolbars($params, &$smarty)
{
	global $prefs;
	
	if ($prefs['javascript_enabled'] != 'y') {
		return '';
	}
	if( ! isset( $params['section'] ) ) {
		global $section;
		if( ! empty($section) ) {
			$params['section'] = $section;
		} else {
			return '';	// proper features set $section, contact us (possibly others) don't
		}
	}

	if( isset( $params['comments'] ) && $params['comments'] == 'y' ) {
		$comments = true;
	} else {
		$comments = false;
	}
	
	// some tool filters to help roll out textarea & toolbars to more sections quickly (for 4.0)

	$hidden = array();
//	if ($params['section'] != 'wiki page' && $params['section'] != 'blogs' && $params['section'] != 'newsletters' && $params['section'] != 'trackers') {
//		$hidden[] = 'fullscreen';
//	}
	if (!in_array($params['section'], array('wiki page', 'blogs', 'newsletters'))) {
		$hidden[] = 'switcheditor';
	}
	
	if( ! isset( $params['area_name'] ) ) {
		$params['area_name'] = 'edit';
	}

	include_once( 'lib/toolbars/toolbarslib.php' );
	$list = ToolbarsList::fromPreference( $params['section'] . ($comments ? '_comments' : ''), $hidden );
	return $list->getWikiHtml( $params['area_name'] );
}

