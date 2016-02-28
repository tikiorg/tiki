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
	private $class;

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
		$this->class = isset($data['class']) ? $data['class'] : null;
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
		$class = $iconset->getClass();

		foreach ($iconset->icons() as $name => $icon) {
			if (! isset($this->icons[$name]) || $over) {
				if (!isset($icon['tag']) && $tag && $this->tag !== $tag) {
					$icon['tag'] = $tag;
				}
				if (!isset($icon['prepend']) && $this->prepend !== $prepend) {
					$icon['prepend'] = $prepend;
				}
				if (!isset($icon['append']) && $this->append !== $append) {
					$icon['append'] = $append;
				}
				if (!isset($icon['class']) && $this->class !== $class) {
					$icon['class'] = $class;
				}
				$this->icons[$name] = $icon;
			}
		}

		if (!empty($iconset->defaults)) {
			foreach ($iconset->defaults as $defname) {
				if (! isset($this->icons[$defname]) || $over) {
					$deficon['id'] = $defname;
					$deficon['tag'] = $tag;
					$deficon['prepend'] = $prepend;
					$deficon['append'] = $append;
					$this->icons[$defname] = $deficon;
				}
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

	public function getClass()
	{
		return $this->class;
	}


	public function getHtml($name, array $params = []) {

		global $prefs;
		$params = new JitFilter($params);

		if ($icon = $this->getIcon($name)) {

			$tag = isset($icon['tag']) ? $icon['tag'] : $this->tag;
			$prepend = isset($icon['prepend']) ? $icon['prepend'] : $this->prepend;
			$append = isset($icon['append']) ? $icon['append'] : $this->append;
			$icon_class = isset($icon['class']) ? $icon['class'] : '';
			$custom_class = isset($params['iclass']) ? $params->iclass->striptags() : '';
			$title = isset($params['ititle']) ? 'title="' . $params->ititle->striptags() . '"' : '';
			$id = isset($params['id']) ? 'id="' . $params->id->striptags() . '"' : '';
			//apply both user defined style and any style from the icon definition
			$styleparams = [];
			if (!empty($icon['style'])) {
				$styleparams[] = $icon['style'];
			}
			if (!empty($params['istyle'])) {
				$styleparams[] = $params->istyle->striptags();
			}
			$size = !empty($params['size']) && $params['size'] < 10 ? abs($params->size->int()) : 1;
			//only used in legacy icon definition
			$sizedef = isset($icon['size']) ? $icon['size'] : 1;

			if ($tag == 'img') { //manage legacy image icons (eg: png, gif, etc)
				//some ability to use larger legacy icons based on size setting
				// 1 = 16px x 16px; 2 = 32px x 32px; 3 = 48px x 48px
				if ($size != 1 && $sizedef != $size && !empty($icon['sizes'][$size])) {
					$file = $icon['sizes'][$size]['id'];
					if (isset($icon['sizes'][$size]['prepend'])) {
						$prepend = $icon['sizes'][$size]['prepend'];
						$append = $icon['sizes'][$size]['append'];
					}
				} else {
					$file = $icon['id'];
				}
				$src = TikiLib::lib('theme')->get_theme_path($prefs['theme'], $prefs['theme_option'], $file . $append, 'icons/');
				if (empty($src)) {
					$src = $prepend . $file . $append;
				}
				$alt = $name;  //use icon name as alternate text
				$style = $this->setStyle($styleparams);
				$html = "<span class=\"icon icon-$name$icon_class$custom_class $file\" $title $style $id><img src=\"$src\" alt=\"$alt\"></span>";
			} else {
				if (isset($icon['id'])) { //use class defined for the icon if set
					$space = !empty($icon_class) ? ' ' : '';
					$icon_class .= $space . $prepend . $icon['id'] . $append;
				} else {
					TikiLib::lib('errorreport')->report(tr('Icon set: Class not defined for icon %0', $name));
				}
				if ((!empty($sizeuser) && $sizeuser != 1)) {
					$styleparams[] = 'font-size:' . ($sizeuser * 100) . '%';
				}
				$style = $this->setStyle($styleparams);
				$html = "<$tag class=\"icon icon-$name $icon_class $custom_class\" $style $title $id></$tag>";
			}

			return $html;

		} else { //if icon is not found in $iconset, then display warning sign. Helps to detect missing icon definitions, typos
			return $this->getHtml('warning');
		}

	}

	/**
	 * Get an array representation of the iconset for encoding as JSON
	 *
	 */
	public function getJS() {
		$return = [
			'defaults' => $this->defaults,
			'icons' => $this->icons,
			'tag' => $this->tag,
			'prepend' => $this->prepend,
			'append' => $this->append,
		];

		return $return;
	}

	private function setStyle(array $styleparams)
	{
		$style = '';
		if (!empty($styleparams)) {
			foreach ($styleparams as $sparam) {
				if (!empty($sparam)) {
					if (empty($style)) {
						$style = 'style="' . $sparam . ';';
					} else {
						$style .= ' ' . $sparam . ';';
					}
				}
			}
			$style .= '"';
		}
		return $style;
	}

}