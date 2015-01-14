<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * ThemeLib 
 * 
 * @uses TikiLib
 */
class ThemeLib extends TikiLib
{
	/** replaces legacy list_styles() function
	 * @return array of themes in the themes directory
	 */
	function list_themes()
	{
		global $tikidomain;
		$csslib = TikiLib::lib('css');

		$themes = [
		'default' => tr('Bootstrap default'),
		'custom' => tr('Custom bootstrap theme by specifying URL'),
		];

		foreach (glob("themes/*/css/tiki.css") as $css) {
			$css = dirname(dirname($css));
			$theme = basename($css);
			$themes[$theme] = tr($theme);
		}
		
		$theme_base_path = $this->get_theme_path();	// knows about $tikidomain

		if ($theme_base_path) {
			$theme = $csslib->list_css($theme_base_path);
		}

		if ($tikidomain) {
			$themes = array_unique(array_merge($themes, $csslib->list_css('styles')));
		}
		
		return $themes;
	}

	/** replaces legacy list_style_options function
	 * @param $a_style - main style (e.g. "thenews.css")
	 * @return array of css files in the style options dir
	 */
	function list_theme_options($a_theme='')
	{
		global $prefs;
		$csslib = TikiLib::lib('css');

		if (empty($a_theme)) {
			$a_theme = $prefs['theme_active'];
		}

		$options = array();
		$option_base_path = $this->get_theme_path($a_theme).'options/';

		if (is_dir($option_base_path)) {
			$options = $csslib->list_css($option_base_path);
		}

		if (count($options)) {
			foreach ($options as &$option) {	// add .css back as above
				$option .= '.css';
			}
			sort($options);
			return $options;
		} else {
			return false;
		}
	}

	/** replaces legacy get_style_base function
	 * @param $stl - main style (e.g. "thenews.css")
	 * @return string - style passed in up to - | or . char (e.g. "thenews")
	 */
	function get_theme_base($theme)
	{
		$parts = preg_split('/[\-\.]/', $theme);
		if (count($parts) > 0) {
			return $parts[0];
		} else {
			return '';
		}
	}

	/** replaces legacy get_style_path function
	 * @param $theme - main theme (e.g. "fivealive-lite" - can be empty to return main themes dir)
	 * @param $option - optional option file name (e.g. "akebi")
	 * @param $filename - optional filename to look for (e.g. "purple.png")
	 * @return path to dir or file if found or empty if not - e.g. "themes/mydomain.tld/fivealive-lite/options/akebi/"
	 */
	function get_theme_path($theme = '', $option = '', $filename = '')
	{
		global $tikidomain;

		$path = '';
		$dir_base = '';
		if ($tikidomain && is_dir("themes/$tikidomain")) {
			$dir_base = $tikidomain.'/';
		}

		$theme_base = '';
		if (!empty($theme)) {
			$theme_base = $this->get_theme_base($theme).'/';
		}

		$option_base = '';
		if (!empty($option)) {
			$option_base = 'options/';
			if ($option != $filename) {	// exception for getting option.css as it doesn't live in it's own dir
				$option_base .= substr($option, 0, strlen($option) - 4).'/';
			}
		}

		if (empty($filename)) {
			if (is_dir('themes/'.$dir_base.$theme_base.$option_base)) {
				$path = 'themes/'.$dir_base.$theme_base.$option_base;
			} else if (is_dir('themes/'.$dir_base.$theme_base)) {
				$path = 'themes/'.$dir_base.$theme_base;	// try "parent" theme dir if no option one
			} else if (is_dir('themes/'.$theme_base.$option_base)) {
				$path = 'themes/'.$theme_base.$option_base;	// try root theme dir if no domain one
			} else {
				$path = 'themes/'.$theme_base;			// fall back to "parent" theme dir if no option one
			}
		} else {
			if (is_file('themes/'.$dir_base.$theme_base.$option_base.$filename)) {
				$path = 'themes/'.$dir_base.$theme_base.$option_base.$filename;
			} else if (is_file('themes/'.$dir_base.$theme_base.$filename)) {	// try "parent" themes dir if no option one
				$path = 'themes/'.$dir_base.$theme_base.$filename;
			} else if (is_file('themes/'.$theme_base.$option_base.$filename)) {	// try non-tikidomain dirs if not found
				$path = 'themes/'.$theme_base.$option_base.$filename;
			} else if (is_file('themes/'.$theme_base.$filename)) {
				$path = 'themes/'.$theme_base.$filename;				// fall back to "parent" themes dir if no option
			} else if (is_file('themes/'.$dir_base.$filename)) {
				$path = 'themes/'.$dir_base.$filename;				// tikidomain root themes dir?
			} else if (is_file('themes/'.$dir_base.$filename)) {
				$path = 'themes/'.$filename;					// root themes dir?
			}
		}

		return $path;
	}
}
