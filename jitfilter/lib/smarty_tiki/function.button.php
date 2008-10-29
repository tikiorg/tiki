<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * smarty_function_button: Display a Tikiwiki button
 *
 * params will be used as params for as smarty self_link params, except those special params specific to smarty button :
 *	- _text: Text that will be shown in the button
 *	- _auto_args: comma separated list of URL arguments that will be kept from _REQUEST (like $auto_query_args)
 */
function smarty_function_button($params, &$smarty) {
	if ( ! is_array($params) || ! isset($params['_text']) ) return;
	global $tikilib, $prefs, $auto_query_args;
	$auto_query_args_orig = null;

	require_once $smarty->_get_plugin_filepath('block', 'self_link');
	
	// Remove params that does not start with a '_', since we don't want them to modify the URL
	foreach ( $params as $k => $v ) {
		if ( $k[0] != '_' && $k != 'href' ) unset($params[$k]);
	}

	$url_args = array();
	if ( ! empty($params['href']) ) {
		if ( ( $pos = strpos($params['href'], '?') ) !== false ) {
			$params['_script'] = substr($params['href'], 0, $pos);
			parse_str($tikilib->htmldecode(substr($params['href'], $pos+1)), $url_args);
			$params = array_merge($params, $url_args);
		} else {
			$params['_script'] = $params['href'];
		}
		unset($params['href']);
		if ( !empty($params['_auto_args']) ) {
			$auto_query_args_orig = $auto_query_args;
			$auto_query_args = explode(',', $params['_auto_args']);
		} else {
			$params['_noauto'] = 'y';
		}
	}

	$html = smarty_block_self_link(
		$params,
		$params['_text'],
		$smarty,
		false
	);

	if ( $auto_query_args_orig !== null ) $auto_query_args = $auto_query_args_orig;
	return '<span class="button">'.$html.'</span>';
}
