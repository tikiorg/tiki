<?php
/*
 * $Id: /cvsroot/tikiwiki/tiki/lib/smarty_tiki/block.display.php,v 1.1 2007-09-06 13:50:20 mose Exp $
 *
 * Smarty plugin to display content only to some groups
 */

function smarty_function_quicktags($params, $content, &$smarty, $repeat)
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

	if( ! isset( $params['area_name'] ) ) {
		$params['area_name'] = 'wikiedit';
	}

	include_once( 'lib/quicktags/quicktagslib.php' );
	$list = QuicktagsList::fromPreference( $params['section'] );
	return $list->getWikiHtml( $params['area_name'] );
}

?>
