<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: themelib.php $

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
		foreach (glob("{$theme_base_path}themes/*/css/tiki.css") as $css) {
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
		$theme_base_path = $this->get_theme_path();	// knows about $tikidomain
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
		foreach (glob("themes/*/options/*/css/tiki.css") as $css) {
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
			foreach (glob("$option_base_path/options/*/css/tiki.css") as $css) {
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
		if(isset($items[1])){ //check if we have option
			$option = $items[1];
		}
		else {
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
			$filename = $option.'.png'; // add .png

		} else {
			$filename = $theme.'.png'; // add .png
			$option = '';
		}
		return $this->get_theme_path($theme, $option, $filename);
	}

	/* replaces legacy get_style_path function
	@param $theme - main theme (e.g. "fivealive" - can be empty to return main themes dir)
	@param $option - optional theme option file name (e.g. "akebi")
	@param $filename - optional filename to look for (e.g. "purple.png")
	@return path to dir or file if found or empty if not - e.g. "themes/mydomain.tld/fivealive/options/akebi/"
	*/
	function get_theme_path($theme = '', $option = '', $filename = '', $subdir = '')
	{
		global $tikidomain;

		$path = '';
		$dir_base = '';
		if ($tikidomain && is_dir("themes/$tikidomain")) {
			$dir_base = $tikidomain.'/';
		}

		$theme_base = '';
		if (!empty($theme)) {
			$theme_base = $theme.'/';
		}

		$option_base = '';
		if (!empty($option)) {
			$option_base = 'options/'.$option.'/';
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
			if (is_dir('themes/'.$dir_base.$theme_base.$option_base.$subdir)) {
				$path = 'themes/'.$dir_base.$theme_base.$option_base.$subdir;
			} else if (is_dir('themes/'.$dir_base.$theme_base.$subdir)) {
				$path = 'themes/'.$dir_base.$theme_base.$subdir;	// try "parent" theme dir if no option one
			} else if (is_dir('themes/'.$theme_base.$option_base.$subdir)) {
				$path = 'themes/'.$theme_base.$option_base.$subdir;	// try root theme dir if no domain one
			} else if (is_dir('themes/'.$theme_base.$subdir)) {
				$path = 'themes/'.$theme_base.$option_base.$subdir;	// try root theme dir if no domain one
			} else {
				$path = 'themes/'.$theme_base;			// fall back to "parent" theme dir with no subdir if not
			}
		} else {
			if (is_file('themes/'.$dir_base.$theme_base.$option_base.$subdir.$filename)) {
				$path = 'themes/'.$dir_base.$theme_base.$option_base.$subdir.$filename;
			} else if (is_file('themes/'.$dir_base.$theme_base.$subdir.$filename)) {	// try "parent" themes dir if no option one
				$path = 'themes/'.$dir_base.$theme_base.$subdir.$filename;
			} else if (is_file('themes/'.$theme_base.$option_base.$subdir.$filename)) {	// try non-tikidomain dirs if not found
				$path = 'themes/'.$theme_base.$option_base.$subdir.$filename;
			} else if (is_file('themes/'.$theme_base.$subdir.$filename)) {
				$path = 'themes/'.$theme_base.$subdir.$filename;				// fall back to "parent" themes dir if no option
			} else if (is_file('themes/'.$dir_base.$subdir.$filename)) {
				$path = 'themes/'.$dir_base.$subdir.$filename;				// tikidomain root themes dir?
			} else if (is_file('themes/'.$subdir.$filename)) {
				$path = 'themes/'.$subdir.$filename;					// root themes dir?
			} else if (is_file('themes/'.$filename)) {
				$path = 'themes/'.$filename;					// root themes dir?
			}
		}
		return $path;
	}
	
	/* get list of base iconsets 
	@return $base_iconsets - an array containing all icon set names from themes/base_files/iconsets folder
	*/
	function list_base_iconsets()
	{
		$base_iconsets = [];

		foreach (scandir('themes/base_files/iconsets') as $iconset_file) {
			if ($iconset_file[0] != '.' && $iconset_file != 'index.php') {
				global $settings;
				include('themes/base_files/iconsets/'. $iconset_file);
				$base_iconsets[substr($iconset_file,0,-4)] = $settings['iconset_name'];
			}
		}
		return $base_iconsets;
	}
		
	/* assemble $iconset array for a theme or theme_option. The values in this array are used by lib/smarty_tiki/function.icon.php for displaying icons
	@param $theme - main theme (e.g. "fivealive")
	@param $option - option of a main theme (e.g. "akebi" option of the fivealive theme)
	@return $iconset - an array containing all icon definitions
	*/
	function get_iconset($theme, $option = '')
	{
		//prepare necessary variables
		global $prefs;
		$iconset = array();
		$theme_path = $this->get_theme_path($theme, $option);
		
		//Step1: first lets see if there is a custom.php in the theme's /icons folder (eg: themes/fivealive/icons/custom.php) and load icons from it. Always do this first as custom.php files should always be preferred.
		if (file_exists("themes/{$theme_path}/icons/custom.php")) { 
			include("themes/{$theme_path}/icons/custom.php");
			if (!empty($settings) and !empty($icons)) { //make sure the iconset file is constructed as expected
				foreach ($icons as &$icon) { //apply settings for each icon
					if (empty($icon['tag'])) {
						$icon['tag'] = $settings['icon_tag'];
					}
				}
				unset($icon);
				$iconset = $iconset + $icons; //add new icons to the icon set while preserving existing icons in the array
			}
		}
	
		//Step2: lets get all the theme specific icons if relevant (when the "theme_iconset" preference has value "theme_specific_icons", than the icons defined for the given theme should be used, e.g. from themes/fivealive/icons/iconset.php)
		if (!empty($prefs['theme_iconset']) and ($prefs['theme_iconset'] === 'theme_specific_iconset') and file_exists("themes/{$theme_path}/icons/iconset.php")) { 
			include("themes/{$theme_path}/icons/iconset.php");
			if (!empty($settings) and !empty($icons)) { //make sure the iconset file is constructed as expected
				foreach ($icons as &$icon) { //apply settings for each icon
						if (empty($icon['tag'])) {
							$icon['tag'] = $settings['icon_tag'];
						}
				}
				unset($icon);
				$iconset = $icons;
			
				if (!empty($settings['iconset_source']) and file_exists($settings['source_iconset'])) { //load source icon set if it is defined in the icon set settings
					include($settings['iconset_source']);
					if (!empty($settings) and !empty($icons)) { //make sure the icon set file is constructed as expected
						foreach ($icons as &$icon) { //apply settings for each icon
							if (empty($icon['tag'])) {
								$icon['tag'] = $settings['icon_tag'];
							}
						}
						unset($icon);
						$iconset = $iconset + $icons; //add new icons to the icon set while preserving existing icons in the array
					}
				}
			}
		}
		else { //if the "theme_iconset" preference is set to one of the base icon sets available in themes/base_files/iconsets/ folder, than load icons from it
			if(file_exists("themes/base_files/iconsets/{$prefs['theme_iconset']}.php")) {
				include("themes/base_files/iconsets/{$prefs['theme_iconset']}.php"); //load icon set info from preference setting
				if (!empty($settings) and !empty($icons)) { //make sure the iconset file is constructed as expected
					foreach ($icons as &$icon) { //apply settings for each icon
						if (empty($icon['tag'])) {
							$icon['tag'] = $settings['icon_tag'];
						}
					}
					unset($icon);
					$iconset = $iconset + $icons; //add new icons to the icon set while preserving existing icons in the array
				}
			}
		}
		
		//step3: as a last resort add all missing icons from the default icon set
		if(file_exists("themes/base_files/iconsets/default.php")) { 
			include("themes/base_files/iconsets/default.php"); 
			foreach ($icons as &$icon) { //apply settings for each icon
				if (empty($icon['tag'])) {
					$icon['tag'] = $settings['icon_tag'];
				}
			}
			unset($icon);
			$iconset = $iconset + $icons; //add new icons to the icon set while preserving existing icons in the array
		}
		unset($settings);
		unset($icons);
		return $iconset;
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
}
