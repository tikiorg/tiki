<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
 * smarty_function_icon: Display a Tiki icon, using theme icons if they exists
 *
 * params will be used as params for the HTML tag (e.g. border, class, ...), except special params starting with '_' :
 *  - _id: [deprecated] short name (i.e. 'page_edit') or relative file path (i.e. 'img/icons/page_edit.png').
 *          Will not work with iconsets.
 *  - name: name of icon from themes/base_files/iconsets file, which allows for choosing different iconsets.
 *          Use instead of _id.
 *  - size:  size of icon when name is used
 *  - class: set custom class (otherwise default classes are applied). When using icon sets, this class will apply to
 *          anchor element
 *  - iclass: set custom class for the icon itself (not the link)
 *  - ititle: set custom title for the icon itself (not the link)
 *  - istyle: set custom style for the icon itself (not the link)
 *  - _type: type of URL to use (e.g. 'absolute_uri', 'absolute_path'). Defaults to a relative URL.
 *  - _tag: type of HTML tag to use (e.g. 'img', 'input_image'). Defaults to 'img' tag.
 *  - _notag: if set to 'y', will only return the URL (which also handles theme icons).
 *  - _menu_text: if set to 'y', will use the 'title' argument as text after the icon and place the whole
 *						content between div tags with a 'icon_menu' class (not compatible with '_notag' param set to 'y').
 *  - _menu_icon: if set to 'n', will not show icon image when _menu_text is 'y'.
 *  - _confirm: text to use in a popup requesting the user to confirm its action (yet only available with javascript)
 *  - _defaultdir: directory to use when the _id param does not include the path
 *  - _extension: Filename extension - default 'png'
 */
function smarty_function_icon($params, $smarty)
{
	if ( ! is_array($params) ) {
		$params = array();
	}

	global $prefs, $tc_theme, $tc_theme_option, $url_path, $base_url, $tikipath, $iconset;
	$cachelib = TikiLib::lib('cache');

	if (empty($tc_theme)) {
		$current_theme = !empty($prefs['theme']) ? $prefs['theme'] : '';
		$current_theme_option = isset($prefs['theme_option']) ? $prefs['theme_option'] : '';
	} else {
		$current_theme = $tc_theme;
		$current_theme_option = !empty($tc_theme_option) ? $tc_theme_option : '';
	}

	if (isset($params['_type'])) {
		if ($params['_type'] === 'absolute_uri') {
			$params['path_prefix'] = $base_url;
		} else if ($params['_type'] === 'absolute_path') {
			$params['path_prefix'] = $url_path;
		}
	}

	$serialized_params = serialize(array_merge($params, array($current_theme, $current_theme_option, isset($_SERVER['HTTPS']))));
	$cache_key = TikiLib::contextualizeKey('icons_' . '_' . md5($serialized_params), 'language', 'external');
	if ( $cached = $cachelib->getCached($cache_key) ) {
		return $cached;
	}

	$basedirs = array('img/icons', 'img/icons/mime');
	$icons_extension = empty($params['_extension']) ? '.png' : '.' . $params['_extension'];
	$tag = 'img';
	$notag = false;
	$default_class = 'icon';
	$default_width = 16;
	$default_height = 16;
	$menu_text = false;
	$menu_icon = false;
	$confirm = '';
	$html = '';

	if ( empty($params['_id']) ) {
		if ( isset($params['_defaultdir']) && $params['_defaultdir'] == 'img/icons/large' ) {
			$params['_id'] = 'green_question48x48';
		} else {
			$params['_id'] = 'green_question';
		}
	}
	if ( ! empty($params['_defaultdir']) ) {
		array_unshift($basedirs, $params['_defaultdir']);
		if ( $params['_defaultdir'] == 'img/icons/large' ) {
			$default_width = $default_height = ( strpos($params['_id'], '48x48') !== false ) ? 48 : 32;
		}
	}
	// ICONSET START, work-in-progress, more information: dev.tiki.org/icons. $iconset array is prepared by lib/setup/theme.php
	// N.B. In some contexts such as the console $iconset may not be set up
	if (!empty($params['name']) && empty($params['_tag']) && !empty($iconset)) {

		$name = $params['name'];
		$html = $iconset->getHtml($name, $params);
		$menu_text = (isset($params['_menu_text']) && $params['_menu_text'] == 'y');
		if (!empty($params['href']) || !empty($params['title']) || $menu_text) {
			/* Generate a link for the icon if href or title (for tips) parameter is set.
			 * This will produce a link element (<a>) around the icon.
			 * If you want a button element (<button>), use the {button} smarty_tiki function */

			//collect link components
			if (!empty($params['title'])) { //add title if not empty
				$a_title = $params['title'];
			} elseif (!empty($params['alt'])) {
				$a_title = $params['alt'];
			} else {
				$a_title = '';
			}
			if (!empty($a_title)) {
				$title_attr = $menu_text ? '' : 'title="' . $a_title . '"';
			} else {
				$title_attr = '';
			}
			if (isset($params['class'])) { //if set, use these classes instead of the default bootstrap
				$a_class = $params['class'];
			} else {
				$a_class = 'btn btn-link'; //the default classes to be used
			}

			if (!empty($params['href'])) { //use href if not empty
				$a_href = 'href="' . $params['href'] . '"';
			} else {
				$a_href = '';
			}
	
			if (isset($params['data-toggle'])) { //add data-toggle if set
				$a_datatoggle = 'data-toggle="' . $params['data-toggle'] . '"';
			} else {
				$a_datatoggle = '';
			}
					
			if (isset($params['onclick'])) { //add onclick if set
				$a_onclick = 'onclick="' . $params['onclick'] . '"';
			} else {
				$a_onclick = '';
			}

			//assemble the link from the components
			if ($menu_text) {
				$icon = isset($params['_menu_icon']) && $params['_menu_icon'] === 'y' ? $html : '';
				$html = '<div class="iconmenu">' . $icon . '<span class="iconmenutext"> ' . $a_title . '</span></div>';
			} else {
				$html = "<a class='$a_class' $title_attr $a_href $a_datatoggle $a_onclick>$html</a>";
			}
		}
		//return the icon
		return $html;
		
	} //ICONSET END 

	// Handle _ids that contains the real filename and path
	if ( strpos($params['_id'], '/') !== false || strpos($params['_id'], '.') !== false ) {
		if ( ($icons_basedir = dirname($params['_id'])) == '')
			$icons_basedir = $basedirs[0];

		$icons_basedir .= '/';

		if ( ($pos = strrpos($params['_id'], '.')) !== false )
			$icons_extension = substr($params['_id'], $pos);

		$params['_id'] = preg_replace(
			'/^' . str_replace('/', '\/', $icons_basedir) . '|' . $icons_extension . '$/',
			'',
			$params['_id']
		);
	} else {
		$icons_basedir = $basedirs[0].'/';
	}

	if ( ! preg_match('/^[a-z0-9_-]+$/i', $params['_id']) )
		return;


	// Include smarty functions used below
	$smarty->loadPlugin('smarty_function_html_image');

	// auto-detect 'alt' param if not set
	if ( ! isset($params['alt']) ) {
		$alt_pos = ( ($alt_pos = strrpos($params['_id'], '_')) === false ) ? 0 : $alt_pos + 1;
		$params['alt'] = tra(ucfirst(substr($params['_id'], $alt_pos)));
	}

	// handle special params and clean unrecognized params
	foreach ( $params as $k => $v ) {
		if ( $k[0] == '_' ) {
			switch ( $k ) {
				case '_id':
					$img_file = $v.$icons_extension;
					$v = $icons_basedir.$img_file;
					$themelib = TikiLib::lib('theme');
					$v2 = $themelib->get_theme_path($current_theme, $current_theme_option, $img_file, 'icons/');
					
					if (!empty($v2)) {
						$params['file'] = $v2;
					} else {
						$params['file'] = $v;
					}
					break;

				case '_notag':
					$notag = ($v == 'y');
					break;

				case '_menu_text':
					$menu_text = ($v == 'y');
					$menu_icon = ( isset($params['_menu_icon']) && $params['_menu_icon'] == 'y' );
					break;

				case '_tag':
					$tag = $v;
					break;

				case '_confirm':
					if ( $prefs['javascript_enabled'] == 'y' ) {
						$params['onclick'] = "return confirm('".str_replace("'", "\'", $v)."');";
					}
					break;
			}

			unset($params[$k]);
		}
	}

	// default values for some params

	if ( isset($params['path_prefix']) ) {
		$params['basedir'] = $tikipath;
		$params['file'] = '/'.$params['file'];
	}

	if ( $tag == 'img' && is_readable($params['file']) ) {
		$dim = getimagesize($params['file']);

		if ( ! isset($params['width']) ) {
			$params['width'] = $dim[0] ? $dim[0] : $default_width;
		}
		if ( ! isset($params['height']) ) {
			$params['height'] = $dim[1] ? $dim[1] : $default_height;
		}
	}

	if ( $notag ) {
		$html = (isset($params['path_prefix'])?$params['path_prefix']:'').$params['file'];
	} else {
		// use 'alt' as 'title' if not set
		if ( ! isset($params['title']) ) $params['title'] = $params['alt'];
		// use default class if not set
		if ( ! isset($params['class']) ) $params['class'] = $default_class;

		// remove empty arguments
		foreach ( $params as $k => $v ) {
			if ( $v == '' ) unset($params[$k]);
		}

		// No need to add a title on a menu icon since there will be the same text just after the icon
		if ( $menu_text ) {
			$menu_text_val = $params['title'];
			unset($params['title']);
		}

		if ( $tag != 'img' ) {
			$params['src'] = TikiLib::tikiUrlOpt($params['file']);
			unset($params['file']);
			foreach ( $params as $k => $v ) {
				$html .= ' ' . htmlspecialchars($k, ENT_QUOTES, 'UTF-8') . '="' . htmlspecialchars($v, ENT_QUOTES, 'UTF-8') . '"';
			}
		}

		if (!empty($params['file'])) {
			$headerlib = TikiLib::lib('header');
			$params['file'] = $headerlib->convert_cdn($params['file']);
			$params['file'] = TikiLib::tikiUrlOpt($params['file']);
		}

		switch ( $tag ) {
			case 'input_image':
				$html = '<input type="image"'.$html.' />';
				break;
			case 'img':
			default:
				try {
					$html = smarty_function_html_image($params, $smarty);
				} catch (Exception $e) {
					$html = '<span class="icon error" title="' . tra('Error:') . ' ' . $e->getMessage() . '">?</span>';
				}
		}

		if ( $tag != 'img' ) {
			// Add a span tag to be able to apply a CSS style on hover for the icon
			$html = "<span>$html</span>";
		}

		if ( $menu_text ) {
			if ( ! $menu_icon ) $html = '';
			$html = '<div class="iconmenu">' . $html . '<span class="iconmenutext"> ' . $menu_text_val . '</span></div>';
		}

	}

	$cachelib->cacheItem($cache_key, $html);
	return $html;
}
