<?php

function smarty_function_preference( $params, $smarty ) {
	global $prefslib, $prefs; require_once 'lib/prefslib.php';
	if( ! isset( $params['name'] ) ) {
		return tra( 'Preference name not specified.' );
	}

	$source = null;
	if( isset( $params['source'] ) ) {
		$source = $params['source'];
	}

	if( $info = $prefslib->getPreference( $params['name'], true, $source ) ) {
		if( isset($params['label']) ) {
			$info['name'] = $params['label'];
		}
		if (isset($params['default'])) {// use for site_language
			$info['value'] = $params['default'];
		}

		$smarty->assign( 'p', $info );
		return $smarty->fetch( 'prefs/' . $info['type'] . '.tpl' );
	} else {
		return tr( 'Preference %0 is not defined.', $params['name'] );
	}
}
