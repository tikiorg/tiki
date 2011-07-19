<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_preference( $params, $smarty ) {
	global $prefslib, $prefs; require_once 'lib/prefslib.php';
	if( ! isset( $params['name'] ) ) {
		return tra( 'Preference name not specified.' );
	}

	$source = null;
	if( isset( $params['source'] ) ) {
		$source = $params['source'];
	}
	$get_pages = isset( $params['get_pages']) && $params['get_pages'] != 'n' ? true : false;

	if( $info = $prefslib->getPreference( $params['name'], true, $source, $get_pages ) ) {
		if( isset($params['label']) ) {
			$info['name'] = $params['label'];
		}
		if (isset($params['default'])) {// use for site_language
			$info['value'] = $params['default'];
		}

		if ($get_pages) {
			if (count($info['pages']) > 0) {
			$pages_string = tra(' (found in ');
			foreach($info['pages'] as $pg) {
				$ct_string = $pg[1] > 1 ? '&amp;cookietab=' . $pg[1] : '';
				$pages_string .= '<a class="lm_result" href="tiki-admin.php?page='.$pg[0].$ct_string.'&amp;highlight='.$info['preference'].'">' . $pg[0] . '</a>, ';
			}
			$pages_string = substr($pages_string, 0, strlen($pages_string) - 2);
			$pages_string .= ')';
			} else {
				$pages_string = tra('(not found in an admin panel)');
			}
		} else {
			$pages_string = '';
		}
		$info['pages'] = $pages_string;

		$smarty->assign( 'p', $info );

		if( isset( $params['mode'] ) && $params['mode'] == 'invert' ) {
			$smarty->assign( 'mode', 'invert' );
		} else {
			$smarty->assign( 'mode', 'normal' );
		}
		
		return $smarty->fetch( 'prefs/' . $info['type'] . '.tpl' );
	} else {
		$info = array('value' => tra('Error'), 'default_val' => tra('Error'),
			'name' => tr( 'Preference %0 is not defined', $params['name'] )
		);
		if (strpos($_SERVER["SCRIPT_NAME"], 'tiki-edit_perspective.php') !== false) {
			$info['hint'] = tra('Drag this out of the perspective and resave it.');
		}
		$smarty->assign( 'p', $info );
		return $smarty->fetch( 'prefs/text.tpl' );
	}
}
