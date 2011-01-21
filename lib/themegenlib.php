<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class ThemeGenLib
{
	private $currentTheme;	// ThemeGenTheme

	public function ThemeGenLib() {
		global $prefs;
		
		if (!empty($prefs['themegenerator_theme'])) {
			$t = $prefs['themegenerator_theme'];
		} else {
			$t = '';
		}
		$this->currentTheme = new ThemeGenTheme($t);
	}
	
	public function setupEditor() {
		global $headerlib, $smarty, $prefs;
		// set up colorpicker
		$headerlib->add_cssfile('lib/jquery/colorpicker/css/colorpicker.css');
		$headerlib->add_cssfile('lib/jquery_tiki/colorpicker/layout.css');
	
		$headerlib->add_jsfile('lib/jquery/colorpicker/js/colorpicker.js');
		$headerlib->add_jsfile('lib/jquery/colorpicker/js/eye.js');
		$headerlib->add_jsfile('lib/jquery/colorpicker/js/utils.js');
		$headerlib->add_jsfile('lib/jquery/colorpicker/js/layout.js');
		
		$data = $this->currentTheme->loadPref();
		
		if (!empty($_REQUEST['tg_css_file'])) {
			$css_file = $_REQUEST['tg_css_file'];
		} else if ($data) {
			$css_file = array_keys($data['files']);
			$css_file = $css_file[0];
		} else {
//			$css_file = $prefs['themegenerator_css_file'];
			$css_file = '';
		}
		$mincss .= $headerlib->minify_css( $css_file );	// clean out comments etc
		
		$num = preg_match_all( '/[^-]color:([^\};!]*?)[;\}!]/i', $mincss, $matches );
		if ($num) {
			$colors = $this->currentTheme->processMatches( $matches[1], $css_file, 'fgcolors' );
		} else {
			$colors = array();
		}
		$num = preg_match_all('/background(?:-color)?:.*?(#[0-9A-F]{3,6})[\s;\}\!]/i', $mincss, $matches);
		if ($num) {
			$bgcolors = $this->currentTheme->processMatches( $matches[1], $css_file, 'bgcolors' );
		} else {
			$bgcolors = array();
		}		
				
		$smarty->assign_by_ref('tg_fore_colors', $colors);
		$smarty->assign_by_ref('tg_back_colors', $bgcolors);
		
		$smarty->assign_by_ref('tg_css_files', $this->setupCSSFiles());
		$smarty->assign_by_ref('tg_css_file', $css_file);
		$headerlib->add_jq_onready('$("#tg_css_file").change(function() { location.replace(location.href + "&tg_css_file=" + escape($(this).val())); });');
	}
	
	public function setupCSSFiles () {
		global $tikilib, $prefs, $tikipath, $style_base;
	
		$css_files = array('' => tra('Select...'));
		$css = '';
		
		if (!empty($prefs['style_option'])) {
			$css_files[$tikilib->get_style_path($prefs['style'], $prefs['style_option'], $prefs['style_option'])] = $style_base . '/' . $prefs['style_option'];
			$css .= file_get_contents( $tikilib->get_style_path($prefs['style'], $prefs['style_option'], $prefs['style_option']) );
		}
		
		$css_files[$tikilib->get_style_path() . $prefs['style']] = $prefs['style'];
		$css .= file_get_contents( $tikilib->get_style_path() . $prefs['style'] );
		
		preg_match_all( '/@import\s+url\("([^;]*)"\);/', $css, $parts );
		$imports = array_reverse(array_unique( $parts[1] ));
		foreach( $imports as $import) {
			$css_files[$tikilib->get_style_path() . $import] = $import;
		}
		
		return $css_files;
		
	}
	
	public function processCSSFile($file, $swaps) {
		global $headerlib;
		
		$css = $headerlib->minify_css( $file );
		
		foreach ($swaps['fgcolors'] as $old => $new) {
			$css = preg_replace('/([^-]color:\s*)' . $old . '/Umis', '$1' . $new, $css);
		}
		
		foreach ($swaps['bgcolors'] as $old => $new) {
			$css = preg_replace('/(background(?:-color)?:\s*)' . $old . '/Umis', '$1' . $new, $css);
		}
		
		return $css;
	}
	
	public function saveNewTheme($name) {
		$this->currentTheme = new ThemeGenTheme($name);
		$this->currentTheme->savePref();
	}
	
	public function updateCurrentTheme($css_file, $swaps, $type) {
		$this->currentTheme->setData(array($swaps, $css_file, $type));
		$this->currentTheme->savePref();
	}
	
	public function deleteCurrentTheme() {
		global $tikilib;
		
		if ($this-currentTheme) {
			$tikilib->set_preference( 'themegenerator_theme', '' );
			$this->currentTheme->deletePref();
		}
	}
	
	public function getCurrentTheme() {
		return $this->currentTheme;
	}
}

require_once 'lib/serializedlist.php';

class ThemeGenTheme extends SerializedList
{
	public function ThemeGenTheme($name) {

		parent::__construct($name);
		$this->data = array( 'files' => array() );
		
	}
	
	public function initData() {
		$this->data = array( 'files' => array() );
	}

	public function initPrefPrefix() {
		$this->prefPrefix = 'themegenerator_theme_';
	}
	
	public function setData($params) {
		list($swaps, $css_file, $type) = $params;
		
		if (!isset($this->data['files'][$css_file])) {
			$this->data['files'][$css_file] = array();
		}
		if (!isset($this->data['files'][$css_file][$type])) {
			$this->data['files'][$css_file][$type] = array();
		}
		
		foreach ($swaps as $kswap => $swap) {
			if ($kswap !== $swap) {
				$this->data['files'][$css_file][$type][$kswap] = $swap;
			}
		}
	}
	
	public function processMatches($matches, $css_file, $type) {
		$processed = array();
		if (is_array( $matches )) {
			$matches = array_map('trim', $matches);
			$matches = array_map('strtolower', $matches);
			$matches = array_unique($matches);
			$matches = array_filter($matches);
			sort($matches);
			//$data = $this->currentTheme->loadPref();
			foreach ($matches as $color) {
				$processed[$color] = array();
				$processed[$color]['old'] = $color;
				if (isset($this->data['files'][$css_file][$type][$color])) {
					$processed[$color]['new'] = $this->data['files'][$css_file][$type][$color];
				} else {
					$processed[$color]['new'] = $color;
				}
			}
		}
		return $processed;
	}
	
	
}


global $themegenlib;
$themegenlib = new ThemeGenLib();
