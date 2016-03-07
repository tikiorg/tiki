<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

class cssLib extends TikiLib
{
	function list_layouts($theme = null, $theme_option = null)
	{
		global $prefs;

		if (empty($theme) && empty($theme_option)){ // if you submit no parameters, return the current theme/theme option
			if (isset($prefs['site_theme'])) {
				$theme = $prefs['site_theme'];
			}
			if (isset($prefs['theme_option'])) {
				$theme_option = $prefs['theme_option'];
			}
		}

		$themelib = TikiLib::lib('theme');

		$available_layouts = array();
		foreach (scandir(TIKI_PATH . '/templates/layouts/') as $layoutName) {
			if ($layoutName[0] != '.' && $layoutName != 'index.php') {
				$available_layouts[$layoutName] = ucfirst($layoutName);
			}   
		}   
		foreach (TikiAddons::getPaths() as $path) {
			if (file_exists($path . '/templates/layouts/')) {
				foreach (scandir($path . '/templates/layouts/') as $layoutName) {
					if ($layoutName[0] != '.' && $layoutName != 'index.php') {
						 $available_layouts[$layoutName] = ucfirst($layoutName);
					}
				}
			}
                }

		$main_theme_path = $themelib->get_theme_path($theme, '', '', 'templates'); // path to the main site theme

		if (file_exists(TIKI_PATH ."/". $main_theme_path . '/layouts/') ){
			foreach (scandir(TIKI_PATH ."/". $main_theme_path . '/layouts/') as $layoutName) {
				if ($layoutName[0] != '.' && $layoutName != 'index.php') {
					$available_layouts[$layoutName] = ucfirst($layoutName);
				}
			}
		}

		if ($theme_option) {
			$theme_path = $themelib->get_theme_path($theme, $theme_option, '', 'templates'); // path to the site theme options

			if (file_exists(TIKI_PATH ."/". $theme_path . '/layouts/') ) {
				foreach (scandir(TIKI_PATH . "/" . $theme_path . '/layouts/') as $layoutName) {
					if ($layoutName[0] != '.' && $layoutName != 'index.php') {
						$available_layouts[$layoutName] = ucfirst($layoutName);
					}
				}
			}
		}
		return $available_layouts;
	}

	function list_user_selectable_layouts($theme = null, $theme_option = null)
	{
		global $prefs;

		if (empty($theme) && empty($theme_option)){ // if you submit no parameters, return the current theme/theme option
			if (isset($prefs['site_theme'])) {
				$theme = $prefs['site_theme'];
			}
			if (isset($prefs['theme_option'])) {
				$theme_option = $prefs['theme_option'];
			}
		}

		$selectable_layouts = array();
		$available_layouts = $this->list_layouts($theme,$theme_option);

		foreach ($available_layouts as $layoutName => $layoutLabel) {
			if ($layoutName == 'mobile'
				|| $layoutName == 'layout_plain.tpl'
				|| $layoutName == 'internal'
			) {
				// hide layouts that are for internal use only
				continue;
			} elseif ($layoutName == 'basic') {
				$selectable_layouts[$layoutName] = tra('Basic Bootstrap');
			} elseif ($layoutName == 'classic') {
				$selectable_layouts[$layoutName] = tra('Classic Tiki (3 containers - header, middle, footer)');
			} elseif ($layoutName == 'header_middle_footer_containers_3-6-3') {
				$selectable_layouts[$layoutName] = tra('Wider side columns (3 containers - header, middle, footer)');
			} elseif ($layoutName == 'social') {
				$selectable_layouts[$layoutName] = tra('Fixed top navbar (uses site icon + "topbar" module zone)');
			} elseif ($layoutName == 'fixed_top_modules') {
				$selectable_layouts[$layoutName] = tra('Fixed top navbar (uses "top" module zone)');
			} else {
				$selectable_layouts[$layoutName] = $layoutLabel;
			} 
		}

		return $selectable_layouts;		
	}

	function list_css($path, $recursive = false)
	{
		$files = $this->list_files($path, '.css', $recursive);
		foreach ($files as $i=>$file) {
			$files[$i] = preg_replace("|^$path/(.*)\.css$|", '$1', $file);
		}
		return $files;
	}

	function list_files($path, $extension, $recursive)
	{
		$back = array();

		$handle = opendir($path);

		while ($file = readdir($handle)) {
			if ((substr($file, -4, 4) == $extension) and (preg_match('/^[-_a-zA-Z0-9\.]*$/', $file))) {
				$back[] = "$path/$file";
			} elseif ($recursive 
								&& $file != '.svn' 
								&& $file != '.' 
								&& $file != '..' 
								&& is_dir("$path/$file") 
								&& !file_exists("db/$file/local.php")
			) {
				$back = array_merge($back, $this->list_files("$path/$file", $extension, $recursive));
			}
		}
		closedir($handle);
		sort($back);
		return $back;
	}

	function browse_css($path)
	{
		if (!is_file($path)) {
			return array('error' => "No such file : $path");
		}

		$meat = implode('', file($path));

		$find[0] = '/\}/';
		$repl[0] = "\n}\n";

		$find[1] = '/\{/';
		$repl[1] = "\n{\n";

		$find[2] = '/\/\*/';
		$repl[2] = "\n/*\n";

		$find[3] = '/\*\//';
		$repl[3] = "\n*/\n";

		$find[4] = '/;/';
		$repl[4] = ";\n";

		$find[5] = '/(W|w)hite/';
		$repl[5] = '#FFFFFF';

		$find[6] = '/(B|b)lack/';
		$repl[6] = '#000000';

		$res = preg_replace($find, $repl, $meat);
		return array(
			'error' => '',
			'content' => explode("\n", $res)
		);
	}

	function parse_css($data)
	{
		$back = array();

		$index = 0;
		$type = '';

		foreach ($data as $line) {
			$line = trim($line);

			if ($line) {
				if (($type != 'comment') and ($line == '/*')) {
					$type = 'comment';

					$index++;
					$back["$index"]['comment'] = '';
					$back["$index"]['items'] = array();
					$back["$index"]['attributes'] = array();
				} elseif (($type == 'comment') and ($line == '*/')) {
					$type = '';
				} elseif ($type == 'comment') {
					$back["$index"]['comment'] .= "$line\n";
				} elseif (($type == 'items') and ($line == '{')) {
					$type = 'attributes';
				} elseif ($type == 'items') {
					$li = explode(',', $line);

					foreach ($li as $l) {
						$l = trim($l);

						if ($l)
							$back["$index"]['items'][] = $l;
					}
				} elseif (($type == 'attributes') and ($line == '}')) {
					$type = '';

					$index++;
					$back["$index"]['comment'] = '';
					$back["$index"]['items'] = array();
					$back["$index"]['attributes'] = array();
				} elseif ($type == 'attributes') {
					$parts = explode(':', str_replace(';', '', $line));

					if (isset($parts[0]) && isset($parts[1])) {
						$obj = trim($parts[0]);

						$back["$index"]['attributes']["$obj"] = trim($parts[1]);
					}
				} else {
					$li = explode(',', $line);

					foreach ($li as $l) {
						$l = trim($l);

						if ($l)
							$back["$index"]['items'][] = $l;
					}

					$type = 'items';
				}

				$back['content'] = $line;
			}
		}

		return $back;
	}

	/**
	 * Find the version of Tiki that a CSS is compatible with
	 *
	 * @TODO: cache the results
	 * @TODO: only read the first 30 lines or so of the file
	 */
	function version_css($path)
	{
		if (!file_exists($path))
			return false;

		$data = implode('', file($path));
		$pos = strpos($data, '@version');

		if ( $pos === false ) {
			return false;
		}
		// get version
		preg_match("/(@[V|v]ersion):?\s?([\d]+)\.([\d]+)/i", $data, $matches);
		$version = $matches[2] . '.' . $matches[3];

		return $version;
	}
}
