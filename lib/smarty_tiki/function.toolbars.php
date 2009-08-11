<?php
/*
 * $Id: function.toolbars.php 20665 2009-08-07 15:13:18Z jonnybradley $
 *
 * Smarty plugin to display content only to some groups
 */

function smarty_function_toolbars($params, $content, &$smarty, $repeat)
{
	if($repeat)
		return;

	if( ! isset( $params['section'] ) ) {
		global $section;
		if( ! empty($section) )
			$params['section'] = $section;
		else
			$params['section'] = 'global';
	}

	if( isset( $params['comments'] ) && $params['comments'] == 'y' ) {
		$comments = true;
	} else {
		$comments = false;
	}
	
	if( ! isset( $params['area_name'] ) ) {
		$params['area_name'] = 'wikiedit';
	}

	include_once( 'lib/toolbars/toolbarslib.php' );
	$list = ToolbarsList::fromPreference( $params['section'] . ($comments ? '_comments' : '') );
	return $list->getWikiHtml( $params['area_name'] );
}

