<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * smarty_function_button: Display a Tiki button
 *
 * params will be used as params for as smarty self_link params, except those special params specific to smarty button :
 *  - _icon: DEPRECATED previously used for file path for legacy icons
 *  - _icon_name: use icon name to show appropriate icon regardless of iconset chosen
 *	- _text: Text that will be shown in the button
 *	- _auto_args: comma separated list of URL arguments that will be kept from _REQUEST (like $auto_query_args) (in addition of course of those you can specify in the href param)
 *                    You can also use _auto_args='*' to specify that every arguments listed in the global var $auto_query_args has to be kept from URL
 *	- _flip_id: id HTML atribute of the element to show/hide (for type 'flip'). This will automatically generate an 'onclick' attribute that will use tiki javascript function flip() to show/hide some content.
 *	- _flip_hide_text: if set to 'n', do not display a '(Hide)' suffix after _text when status is not 'hidden'
 *	- _flip_default_open: if set to 'y', the flip is open by default (if no cookie jar)
 *	- _escape: if set to 'y', will escape the apostrophes in onclick
 *  - _type: button styling. Possible values: default, primary, success, info, warning, danger, link (following bootstrap conventions)
 *     Set different class, title, text and icon depending on whether the button is disabled or selected
 *  - _disabled: set to y to disable the button
 *  - _disabled_class: class to use if _disabled is set to y. Default is 'disabled'
 *  - _disabled_title: button title to use if _disabled is set to y
 *  - _disabled_text: button text to use if _disabled is set to y
 *  - _disabled_icon_name: button icon to use if _disabled is set to y
 *  - _selected: set to y to enable the button.
 *  - _selected_class: class to use if _selected set to y. Default is 'selected'
 *  - _selected_title: button title to use if _selected is set to y
 *  - _selected_text: button text to use if _selected is set to y
 *  - _selected_icon_name: button icon to use if _selected is set to y
 */
function smarty_function_button($params, $smarty)
{
	if ( ! is_array($params) || ! isset($params['_text']) ) return;
	global $tikilib, $prefs, $auto_query_args;

	$class = null;

	$smarty->loadPlugin('smarty_block_self_link');

	$selected = false ;
	if ( ! empty($params['_selected'])) {
		if ( $params['_selected'] == 'y' ) {
			$selected = true;
			if (!empty($params['_selected_class'])) {
				$params['_class'] = $params['_selected_class'];
			} else {
				$params['_class'] = 'selected';
			}
			if (!empty($params['_selected_text'])) {
				$params['_text'] = $params['_selected_text'];
			}
			if (!empty($params['_selected_title'])) {
				$params['_title'] = $params['_selected_title'];
			}
			if (!empty($params['_selected_icon'])) {
				$params['_icon'] = $params['_selected_icon'];
			}
			if (!empty($params['_selected_icon_name'])) {
				$params['_icon_name'] = $params['_selected_icon_name'];
			}
		}
	}

	$disabled = false ;
	if ( ! empty($params['_disabled']) ) {
		if ( $params['_disabled'] == 'y' ) {
			$disabled = true;
			if (!empty($params['_disabled_class'])) {
				$params['_class'] = $params['_disabled_class'];
			} else {
				$params['_class'] = 'disabled';
			}
			if (!empty($params['_disabled_text'])) {
				$params['_text'] = $params['_disabled_text'];
			}
			if (!empty($params['_disabled_title'])) {
				$params['_title'] = $params['_disabled_title'];
			}
			if (!empty($params['_disabled_icon'])) {
				$params['_icon'] = $params['_disabled_icon'];
			}
			if (!empty($params['_disabled_icon_name'])) {
				$params['_icon_name'] = $params['_disabled_icon_name'];
			}
		}
		unset($params['_disabled']);
	}

	//apply class only to the button
	if (!empty($params['_class'])) {
		$class = $params['_class'];
	}
	if (!empty($params['_id'])) {
		$id = ' id="'.$params['_id'].'"';
	} else {
		$id = '';
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
			if ( ! empty($params['_escape']) && $params['_escape'] === 'y') {
				$params['_onclick'] = addslashes($params['_onclick']);
			}
			if ( ! isset($params['_flip_hide_text']) || $params['_flip_hide_text'] != 'n' ) {
				$cookie_key = 'show_' . $params['_flip_id'];
				$params['_text'] .= '<span id="'.$params['_flip_id'].'_close" style="display:'
					. ( ((isset($_SESSION['tiki_cookie_jar'][$cookie_key]) && $_SESSION['tiki_cookie_jar'][$cookie_key] == 'y') || (isset($params['_flip_default_open']) && $params['_flip_default_open'] == 'y')) ? 'inline' : 'none' )
					. ';"> (' . tra('Hide') . ')</span>';
			}
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

		// Remove params that does not start with a '_', since we don't want them to modify the URL except when in auto_query_args
		if ( ! isset($params['_keepall']) || $params['_keepall'] != 'y') {
			foreach ( $params as $k => $v ) {
				if ( $k[0] != '_' && $k != 'href' && (empty($auto_query_args) || !in_array($k, $auto_query_args)) ) unset($params[$k]);
			}
		}

		$url_args = array();
		if ( ! empty($params['href']) ) {

			// Handle anchors
			if ( strpos($params['href'], '#') )
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

		$html = smarty_block_self_link(
			$params,
			$params['_text'],
			$smarty
		);
	} else {
		$params['_disabled'] = 'y';
		$html = smarty_block_self_link(
			$params,
			$params['_text'],
			$smarty
		);
	}

	$type = isset($params['_type']) ? $params['_type'] : 'default';

	$auto_query_args = $auto_query_args_orig;
	$html = preg_replace('/<a /', '<a class="btn btn-' . $type . ' ' . $class . '" data-role="button" data-inline="true" ' . $id . ' ', $html);
	return $html;
}
