<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class IconsetLib
{

	/**
	 * @param $theme
	 * @param $theme_option
	 * @return Iconset
	 * @throws Exception
	 */
	function getIconsetForTheme($theme, $theme_option)
	{
		global $prefs;
		$themelib = TikiLib::lib('theme');

		// start with the default base and merge in others which will generally be less complete
		$iconset = new Iconset($this->loadFile('themes/base_files/iconsets/default.php'));
		
		//override the default icons with theme specific icons or with site icon set setting
		if ($prefs['theme_iconset'] === 'theme_specific_iconset') {
			$filename = $themelib->get_theme_path($theme, '', str_replace('-', '_', $theme) . '.php');
			if ($filename) {
				$iconset1 = new Iconset($this->loadFile($filename));
				$iconset->merge($iconset1);
			}
			$filename = $themelib->get_theme_path($theme, $theme_option, str_replace('-', '_', $theme_option) . '.php');
			if ($filename) {
				$iconset1 = new Iconset($this->loadFile($filename));
				$iconset->merge($iconset1);
			}
		} else if ($prefs['theme_iconset'] !== 'default') {
			$filename = "themes/base_files/iconsets/{$prefs['theme_iconset']}.php";
			$iconset1 = new Iconset($this->loadFile($filename));
			$iconset->merge($iconset1);
		}

		//when a theme option is used, first override with the main theme's custom icons
		if(!empty($theme_option)){
			$filename = $themelib->get_theme_path($theme, '', str_replace('-', '_', $theme).'_custom.php', 'icons/');
			if ($filename) {
				$iconset1 = new Iconset($this->loadFile($filename));
				$iconset->merge($iconset1);
			}
		}
		
		//finally override with custom icons of the displayed theme
		$filename = $themelib->get_theme_path($theme, $theme_option, str_replace('-', '_', $theme_option).'_custom.php', 'icons/');
		if ($filename) {
			$iconset1 = new Iconset($this->loadFile($filename));
			$iconset->merge($iconset1);
		}
		
		return $iconset;
	}

	/**
	 * @param $filename
	 * @return array
	 */
	function loadFile($filename)
	{
		$data = [];
		if (is_readable($filename)) {
			include_once($filename);
			$function = 'iconset_' . str_replace('.php', '', basename($filename));
			if (function_exists($function)) {
				$data = $function();
			}
		}
		return $data;
	}

}

class Iconset
{

	private $name;
	private $description;
	private $tag;
	private $prepend;
	private $append;

	private $icons;
	private $defaults;

	function __construct($data)
	{
		$this->name = $data['name'];
		if (isset($data['description'])) {
			$this->description = $data['description'];
		} else {
			$this->description = '';
		}
		$this->tag = $data['tag'];
		$this->prepend = isset($data['prepend']) ? $data['prepend'] : null;
		$this->append = isset($data['append']) ? $data['append'] : null;
		$this->icons = isset($data['icons']) ? $data['icons'] : [];
		$this->defaults = isset($data['defaults']) ? $data['defaults'] : [];

		if (!empty($data['source'])) {
			$source = new Iconset(TikiLib::lib('iconset')->loadFile($data['source']));
			$this->merge($source, false);
		}
	}

	function merge(Iconset $iconset, $over = true)
	{
		$tag = $iconset->tag();
		$prepend = $iconset->prepend();
		$append = $iconset->append();

		foreach ($iconset->icons() as $name => $icon) {
			if (! isset($this->icons[$name]) || $over) {
				if (empty($icon['tag']) && $tag && $this->tag !== $tag) {
					$icon['tag'] = $tag;
				}
				if (empty($icon['prepend']) && $prepend && $this->prepend !== $prepend) {
					$icon['prepend'] = $prepend;
				}
				if (empty($icon['append']) && $append && $this->append !== $append) {
					$icon['append'] = $append;
				}
				$this->icons[$name] = $icon;
			}
		}
	}

	function getIcon($name) {
		if (isset($this->icons[$name])) {
			return $this->icons[$name];
		}

		if (array_search($name, $this->defaults) !== false) {
			return [
				'id' => $name,
			];
		} else {
			return null;
		}
	}

	public function icons()
	{
		return $this->icons;
	}

	public function tag()
	{
		return $this->tag;
	}

	public function prepend()
	{
		return $this->prepend;
	}

	public function append()
	{
		return $this->append;
	}

	public function getHtml($name) {

		global $prefs;

		if ($icon = $this->getIcon($name)) {

			$tag = isset($icon['tag']) ? $icon['tag'] : $this->tag;
			$prepend = isset($icon['prepend']) ? $icon['prepend'] : $this->prepend;
			$append = isset($icon['append']) ? $icon['append'] : $this->append;
			$icon_class = '';

			if ($tag == 'img') { //manage legacy image icons (eg: png, gif, etc)
				$src = TikiLib::lib('theme')->get_theme_path($prefs['theme'], $prefs['theme_option'], $icon['id'] . $append, 'icons/');
				if (empty($src)) {
					$src = $prepend . $icon['id'] . $append;
				}
				$alt = $name;  //use icon name as alternate text
				$html = "<span class=\"icon icon-$name $icon_class\"><img src=\"$src\" alt=\"$alt\"></span>";
			} else {
				if (isset($icon['id'])) { //use class defined for the icon if set
					$icon_class = $prepend . $icon['id'] . $append;
				} else {
					TikiLib::lib('errorreport')->report(tr('Iconset: Class not defined for icon %0', $name));
				}

				$html = "<$tag class=\"icon icon-$name $icon_class\"></$tag>";
			}

			return $html;

		} else { //if icon is not found in $iconset, than display bootstrap glyphicon warning-sign. Helps to detect missing icon definitions, typos
			return $this->getHtml('warning');
		}

	}

}