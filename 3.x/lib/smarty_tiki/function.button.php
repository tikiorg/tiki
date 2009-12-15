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
 *                    You can also use _auto_args='*' to specify that every arguments listed in the global var $auto_query_args has to be kept from URL
 *	- _flip_id: id HTML atribute of the element to show/hide (for type 'flip'). This will automatically generate an 'onclick' attribute that will use tiki javascript function flip() to show/hide some content.
 *	- _flip_hide_text: if set to 'n', do not display a '(Hide)' suffix after _text when status is not 'hidden'
 *	- _flip_default_open: if set to 'y', the flip is open by default (if no cookie jar)
 */
function smarty_function_button($params, &$smarty) {
	if ( ! is_array($params) || ! isset($params['_text']) ) return;
	global $tikilib, $prefs, $auto_query_args;

	require_once $smarty->_get_plugin_filepath('block', 'self_link');

	$selected = false ;
	if ( ! empty($params['_selected']) ) {
		// Filter the condition
		if (preg_match('/[a-zA-Z0-9 =<>!]+/',$params['_selected'])) {
			$error_report = error_reporting(~E_ALL);
			$return = eval ( '$selected =' . $params['_selected'].";" );
			error_reporting($error_report);
			if ($return !== FALSE) {
				if ($selected) {
					if (! empty($params['_selected_class']) ) {
						$params['_class'] = $params['_selected_class'];
					} else {
						$params['_class'] = 'selected';
					}
				}
			}
		}
	}

	$disabled = false ;
	if ( ! empty($params['_disabled']) ) {
		// Filter the condition
		if (preg_match('/[a-zA-Z0-9 =<>!]+/',$params['_disabled'])) {
			$error_report = error_reporting(~E_ALL);
			$return = eval ( '$disabled =' . $params['_disabled'].";" );
			error_reporting($error_report);
			if ($return !== FALSE) {
				if ($disabled) {
					if (! empty($params['_disabled_class']) ) {
						$params['_class'] = $params['_disabled_class'];
					} else {
						$params['_class'] = 'disabled';
					}
				}
			}
		}
		unset($params['_disabled']);
	}

	//apply class only to the button
	if (!empty($params['_class'])) {
		$class = $params['_class'];
	}
	unset($params['_class']);

	if (!$disabled) {
		$flip_id = '';
		if ( ! empty($params['_flip_id']) ) {
			$params['_onclick'] = "javascript:flip('"
				. $params['_flip_id']
				. "');flip('"
				. $params['_flip_id']
				. "_close','inline');return false;";
			if ( ! isset($params['_flip_hide_text']) || $params['_flip_hide_text'] != 'n' ) {
				$cookie_key = 'show_' . $params['_flip_id'];
				$params['_text'] .= '<span id="'.$params['_flip_id'].'_close" style="display:'
					. ( ((isset($_SESSION['tiki_cookie_jar'][$cookie_key]) && $_SESSION['tiki_cookie_jar'][$cookie_key] == 'y') || (!isset($_SESSION['tiki_cookie_jar'][$cookie_key]) && isset($params['_flip_default_open']) && $params['_flip_default_open'] == 'y')) ? 'inline' : 'none' )
					. ';"> (' . tra('Hide') . ')</span>';
			}
		}

		// Remove params that does not start with a '_', since we don't want them to modify the URL
		foreach ( $params as $k => $v ) {
			if ( $k[0] != '_' && $k != 'href' ) unset($params[$k]);
		}

		$url_args = array();
		if ( ! empty($params['href']) ) {

			// Handle anchors
			if (strstr($params['href'], '#'))
				list($params['href'], $params['_anchor']) = explode('#', $params['href'], 2);

			// Handle script and URL arguments
			if ( ( $pos = strpos($params['href'], '?') ) !== false ) {
				$params['_script'] = substr($params['href'], 0, $pos);
				TikiLib::parse_str($tikilib->htmldecode(substr($params['href'], $pos+1)), $url_args);
				$params = array_merge($params, $url_args);
			} else {
				$params['_script'] = $params['href'];
			}

			unset($params['href']);
		}

		$auto_query_args_orig = $auto_query_args;
		if ( !empty($params['_auto_args']) ) {
			if ( $params['_auto_args'] != '*' ) {
				if ( !isset($auto_query_args) ) $auto_query_args = null;
				$auto_query_args = explode(',', $params['_auto_args']);
			}
		} else {
			$params['_noauto'] = 'y';
		}

		$html = smarty_block_self_link(
			$params,
			$params['_text'],
			$smarty,
			false
		);
	} else {
		$params['_disabled'] = 'y';
		$html = smarty_block_self_link(
			$params,
			$params['_text'],
			$smarty,
			false
		);
	}

	$auto_query_args = $auto_query_args_orig;
	return '<span class="button'.(!empty($class)?" $class":'').'">'.$html.'</span>';
}
