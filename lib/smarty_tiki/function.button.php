<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * smarty_function_button: Display a Tikiwiki button
 *
 * params will be used as params for the HTML tag (e.g. href, class, ...), except special params starting with '_' :
 *	- _text: Text that will be shown in the button
 */
function smarty_function_button($params, &$smarty) {
  if ( ! is_array($params) || ! isset($params['_text']) ) return;
  global $smarty, $prefs, $auto_query_args;

  require_once $smarty->_get_plugin_filepath('block', 'self_link');
	
	$url_args = array();
	if ( ! empty($params['href']) ) {
		if ( ( $pos = strpos($params['href'], '?') ) !== false ) {
			$params['_script'] = substr($params['href'], 0, $pos);
			parse_str(substr($params['href'], $pos+1), $url_args);
			$params = array_merge($params, $url_args);
		} else {
			$params['_script'] = $params['href'];
		}
		unset($params['href']);
		$params['_noauto'] = 'y';
	}

	$html = smarty_block_self_link(
		$params,
		$params['_text'],
		$smarty,
		false
	);

	return '<span class="button">'.$html.'</span>';
}

?>
