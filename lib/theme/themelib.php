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
ThemeLib 
@uses TikiLib
*/

class ThemeLib extends TikiLib
{

	/* 
	@return array of folder names in themes directory
	*/
	function get_themes($theme_base_path = '')
	{
		$themes = array();
		$list_css = glob("{$theme_base_path}themes/*/css/*.css");
		if( $list_css == FALSE ) {
			return array();
		}
		foreach ($list_css as $css) {
			$css = dirname(dirname($css));
			$theme = basename($css);
			$themes[$theme] = tr($theme);
		}
		unset($themes['base_files']); //make sure base_files directory is removed from the array
		unset($themes['templates']); //make sure templates directory is removed from the array
		return $themes;
	}

	/* replaces legacy list_styles() function
	@return array of all themes offered by Tiki
	*/
	function list_themes()
	{
		//set special array values and get themes from the main themes directory
		$themes = [
			'default' => tr('Default Bootstrap'),
			'custom_url' => tr('Custom theme by specifying URL'),
		];
		$themes = $themes + $this->get_themes(); //this way default and custom remains on the top of the array and default keeps its description

		//get multidomain themes
		$theme_base_path = $this->get_theme_path();    // knows about $tikidomain
		if ($theme_base_path) {
			$themes = array_unique(array_merge($themes, $this->get_themes($theme_base_path)));
		}

		return $themes;
	}

	/*
	@return array of all theme options
	*/
	function get_options()
	{
		$options = array();
		foreach (glob("themes/*/options/*/css/*.css") as $css) {
			$css = dirname(dirname($css));
			$option = basename($css);
			$options[$option] = tr($option);
		}
		return $options;
	}

	/* replaces legacy list_style_options function
	@param $theme - main theme (e.g. "fivealive")
	@return array of options the theme's options directory (e.g. from "themes/fivealive/options/")
	*/
	function list_theme_options($theme)
	{
		$theme_options = array();
		if (isset($theme) and $theme != 'custom_url') { //don't consider custom URL themes to have options
			$option_base_path = $this->get_theme_path($theme);
			$list_css = glob("{$option_base_path}/options/*/css/*.css");
			if( $list_css == FALSE ) {
				return array();
			}
			foreach ($list_css as $css) {
				$css = dirname(dirname($css));
				$option = basename($css);
				$theme_options[$option] = tr($option);
			}
		}
		return $theme_options;
	}

	/* the group theme setting is stored in one column, so we need an array where all themes and all options are all available
	@return array of all themes and all options
	*/
	function list_themes_and_options()
	{
		$themes = $this->list_themes();
		unset($themes['custom_url']); //make sure Custom URL is removed from the list as it can not have options
		foreach ($themes as $theme) {
			$options = $this->list_theme_options($theme);
			foreach ($options as $option) {
				$theme_options[$theme . '/' . $option] = $theme . '/' . $option;
			}
		}
		$themes_and_options = array_merge($themes, $theme_options); //merge the two array
		natsort($themes_and_options); //sort the values
		return $themes_and_options;
	}

	/* if theme and option is concatenated into one string (eg: group themes, theme control), than extract theme and option info from the string
	@return theme and option name
	*/
	function extract_theme_and_option($themeoption)
	{
		$items = explode("/", $themeoption);
		$theme = $items[0]; //theme is always there
		if (isset($items[1])) { //check if we have option
			$option = $items[1];
		} else {
			$option = '';
		}
		return array($theme, $option);
	}

	/* get thumbnail for theme if there is one. The thumbnail should be a png file.
	@param $theme - theme name (e.g. fivealive)
	@param $option - optional theme option file name
	@return string path to thumbnail file to be used by an img element
	*/
	function get_thumbnail_file($theme, $option = '')
	{
		if (!empty($option) && $option != tr('None')) {
			$filename = $option . '.png'; // add .png

		} else {
			$filename = $theme . '.png'; // add .png
			$option = '';
		}
		return $this->get_theme_path($theme, $option, $filename);
	}

	/** replaces legacy get_style_path function
	 * @param string $theme - main theme (e.g. "fivealive" - can be empty to return main themes dir)
	 * @param string $option - optional theme option file name (e.g. "akebi")
	 * @param string $filename - optional filename to look for (e.g. "purple.png")
	 * @param string $subdir - optional dir to look in, e.g. 'css' etc (will guess by file extension if this not set but filename is)
	 * @return string          - path to dir or file if found or empty if not - e.g. "themes/mydomain.tld/fivealive/options/akebi/"
	 */

	function get_theme_path($theme = '', $option = '', $filename = '', $subdir = '')
	{
		global $tikidomain;

		$path = '';
		$dir_base = '';
		if ($tikidomain && is_dir("themes/$tikidomain")) {
			$dir_base = $tikidomain . '/';
		}

		$theme_base = '';
		if (!empty($theme)) {
			$theme_base = $theme . '/';
		}

		$option_base = '';
		if (!empty($option)) {
			$option_base = 'options/' . $option . '/';
		}

		if (empty($subdir) && !empty($filename)) {
			$extension = substr($filename, strrpos($filename, '.') + 1);
			switch ($extension) {
				case 'css':
					$subdir = 'css/';
					break;
				case 'php':
					$subdir = 'icons/';
					break;
				case 'png':
				case 'gif':
				case 'jpg':
				case 'jpeg':
					$subdir = 'images/';
					break;
				case 'less':
					$subdir = 'less/';
					break;
				case 'js':
					$subdir = 'js/';
					break;
				case 'tpl':
					$subdir = 'templates/';
					break;
			}
		}

		if (empty($filename)) {
			if (is_dir('themes/' . $dir_base . $theme_base . $option_base . $subdir)) {
				$path = 'themes/' . $dir_base . $theme_base . $option_base . $subdir;
			} else if (is_dir('themes/' . $dir_base . $theme_base . $subdir)) {
				$path = 'themes/' . $dir_base . $theme_base . $subdir;                // try "parent" theme dir if no option one
			} else if (is_dir('themes/' . $theme_base . $option_base . $subdir)) {
				$path = 'themes/' . $theme_base . $option_base . $subdir;                // try non-tikidomain theme dirs if no domain one
			} else if (is_dir('themes/' . $theme_base . $subdir)) {
				$path = 'themes/' . $theme_base . $subdir;                            // try root theme dir if no domain one
			} else if (is_dir('themes/' . $theme_base)) {
				$path = 'themes/' . $theme_base;                                    // fall back to "parent" theme dir with no subdir if not
			}
		} else {
			if (is_file('themes/' . $dir_base . $theme_base . $option_base . $subdir . $filename)) {
				$path = 'themes/' . $dir_base . $theme_base . $option_base . $subdir . $filename;
			} else if (is_file('themes/' . $dir_base . $theme_base . $subdir . $filename)) {    // try "parent" themes dir if no option one
				$path = 'themes/' . $dir_base . $theme_base . $subdir . $filename;
			} else if (is_file('themes/' . $theme_base . $option_base . $subdir . $filename)) {    // try non-tikidomain dirs if not found
				$path = 'themes/' . $theme_base . $option_base . $subdir . $filename;
			} else if (is_file('themes/' . $theme_base . $subdir . $filename)) {
				$path = 'themes/' . $theme_base . $subdir . $filename;                        // fall back to "parent" themes dir if no option
			} else if (is_file('themes/' . $dir_base . $subdir . $filename)) {
				$path = 'themes/' . $dir_base . $subdir . $filename;                            // tikidomain root themes dir?
			} else if (is_file('themes/' . $subdir . $filename)) {
				$path = 'themes/' . $subdir . $filename;                                    // root themes subdir?
			} else if (is_file('themes/' . $filename)) {
				$path = 'themes/' . $filename;                                            // root themes dir?
			}
		}
		return $path;
	}

	function get_theme_css($theme = '', $option = '')
	{
		if ($option) {
			return $this->get_theme_path($theme, $option, $option . '.css');
		} else {
			return $this->get_theme_path($theme, $option, $theme . '.css');
		}
	}
	
	/* get list of base iconsets 
	@return $base_iconsets - an array containing all icon set names from themes/base_files/iconsets folder
	*/
	function list_base_iconsets()
	{
		$base_iconsets = [];
		$iconsetlib = TikiLib::lib('iconset');

		foreach (scandir('themes/base_files/iconsets') as $iconset_file) {
			if ($iconset_file[0] != '.' && $iconset_file != 'index.php') {
				$data = $iconsetlib->loadFile('themes/base_files/iconsets/' . $iconset_file);
				$base_iconsets[substr($iconset_file,0,-4)] = $data['name'];
			}
		}
		return $base_iconsets;
	}

	/* get list of available themes and options 
	@return array of available themes and options based on $prefs['available_themes'] setting. This function does not consider if change_theme is on or off.
	*/
	function get_available_themesandoptions()
	{
		global $prefs;
		$available_themesandoptions = array();
		if (count($prefs['available_themes'] != 0) and !empty($prefs['available_themes'][0])) { //if pref['available_themes'] is set, than use it
			$available_themesandoptions = array_combine($prefs['available_themes'], $prefs['available_themes']);
		}
		else {
			$available_themesandoptions = $this->list_themes_and_options(); //else load all themes and options
			unset($available_themesandoptions['custom_url']); //make sure Custom URL is removed from the list
		}
		return $available_themesandoptions;
	}
	/* get a list of available themes 
	@return array of available themes based on $prefs['available_themes'] setting. This function does not consider if change_theme is on or off.
	*/
	function get_available_themes()
	{
		global $prefs;
		$available_themes = array();
		if (count($prefs['available_themes'] != 0) and !empty($prefs['available_themes'][0])) { //if pref['available_themes'] is set, than use it
			foreach ($prefs['available_themes'] as $available_theme){
				$theme = $this->extract_theme_and_option($available_theme)[0];
				$available_themes[$theme] = $theme;
				$available_themes['default'] = tr('Default Bootstrap');
			}
		}
		else {
			$available_themes = $this->list_themes(); //else load all themes and options
			unset($available_themes['custom_url']); //make sure Custom URL is removed from the list
		}
		return $available_themes;
	}
	
	/* get a list of available options for a theme 
	@return array of available theme options based on $prefs['available_themes'] setting. This function does not consider if change_theme is on or off.
	*/
	function get_available_options($theme)
	{
		global $prefs;
		$available_options = array();
		if (count($prefs['available_themes'] != 0) and !empty($prefs['available_themes'][0])) {
			foreach ($prefs['available_themes'] as $available_themeandoption){
				$themeandoption = $this->extract_theme_and_option($available_themeandoption);
				if($theme === $themeandoption[0] && !empty($themeandoption[1])){
					$available_options[$themeandoption[1]] = $themeandoption[1];
				}
			}
			return $available_options;
		} else {
			return $this->list_theme_options($theme);
		}
	}
}
