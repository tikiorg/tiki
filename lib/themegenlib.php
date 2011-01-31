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
		
		// tiki themegen include
		$headerlib->add_jsfile('lib/jquery_tiki/tiki-themegenerator.js');
		$headerlib->add_cssfile('css/admin.css');
		
		// set up colorpicker
		$headerlib->add_cssfile('lib/jquery/colorpicker/css/colorpicker.css');
		$headerlib->add_cssfile('lib/jquery_tiki/colorpicker/layout.css');
	
		$headerlib->add_jsfile('lib/jquery/colorpicker/js/colorpicker.js');
//		$headerlib->add_jsfile('lib/jquery/colorpicker/js/eye.js');
//		$headerlib->add_jsfile('lib/jquery/colorpicker/js/utils.js');
//		$headerlib->add_jsfile('lib/jquery/colorpicker/js/layout.js');
		
		// colour lib
		$headerlib->add_jsfile('lib/jquery/jquery.color.js');
		
		if (!empty($_SESSION['tg_preview'])) {	// && !empty($_REQUEST['tg_preview'])
			$data = unserialize($_SESSION['tg_preview']);
			$this->currentTheme->setData($data);
			if (!empty($_SESSION['tg_css_file']) && empty($_REQUEST['tg_css_file'])) {
				$css_file = $_SESSION['tg_css_file'];
			}
		} else {
			$data = $this->currentTheme->loadPref();
		}
		
		if (empty($css_file)) {
			if (!empty($_REQUEST['tg_css_file'])) {
				$css_file = $_REQUEST['tg_css_file'];
			} else if ($data) {
				$css_files = array_keys($data['files']);
				$css_file_found = false;
				foreach ( $css_files as $css_file) {
					foreach( $headerlib->cssfiles as $files) {
						if (in_array($css_file, $files)) {
							$css_file_found = true;
							break 2;
						}
					}
				}
				if ( !$css_file_found ) {
					$css_file = ''; //$css_file[0];
				}
			} else {
	//			$css_file = $prefs['themegenerator_css_file'];
				$css_file = '';
			}
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
		$num = preg_match_all('/border(?:-.*)?:.*(#[0-9A-F]{3,6})[\s;\}\!]/Umis', $mincss, $matches);
		if ($num) {
			$bordercolors = $this->currentTheme->processMatches( $matches[1], $css_file, 'bordercolors' );
		} else {
			$bordercolors = array();
		}		
		preg_match_all('/font-size:.*?([\d\.]*[^;\} ]*)/i', $mincss, $matches);
		$fontsizes = $this->currentTheme->processMatches( $matches[1], $css_file, 'fontsize' );
		
		// array for smarty to loop through
		$tg_data = array(
			'colors' => array(
				'types' => array(
					'fgcolors' => array(
						'items' => $colors,
						'title' => tra('Foreground Colors:'),
					),
					'bgcolors' => array(
						'items' => $bgcolors,
						'title' => tra('Background Colors:'),
					),
					'bordercolors' => array(
						'items' => $bordercolors,
						'title' => tra('Border Colors:'),
					),
				),
				'title' => tra('Colors:'),
			),
			'typography' => array(
				'types' => array(
					'fontsize' => array(
						'items' => $fontsizes,
						'title' => tra('Font Sizes:'),
					),
				),
				'title' => tra('Typography:'),
			),
		);
		
		$smarty->assign_by_ref( 'tg_data', $tg_data );
		
		$smarty->assign_by_ref('tg_css_files', $this->setupCSSFiles());
		$smarty->assign_by_ref('tg_css_file', $css_file);
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_REQUEST['tg_open_dialog']))) {
			$smarty->display('themegen.tpl');
			die;
		}
		
		if (!empty($_COOKIE['themegen'])) {
			if (strpos($_COOKIE['themegen'], 'state:open') !== false) {
				$headerlib->add_jq_onready('openThemeGenDialog();', 100);
			}
		} else if (!empty($_SESSION['tg_preview'])) {
			unset($_SESSION['tg_preview']);
		}

		
	}
	
	public function setupCSSFiles () {
		global $tikilib, $prefs, $tikipath, $style_base;
	
		$css_files = array('' => tra('Select...'));
		$css = '';
		
		if (!empty($prefs['style_option']) && $prefs['style_option'] !== tra('None')) {
			$css_files[$tikilib->get_style_path($prefs['style'], $prefs['style_option'], $prefs['style_option'])] = $style_base . '/' . $prefs['style_option'];
			$css .= file_get_contents( $tikilib->get_style_path($prefs['style'], $prefs['style_option'], $prefs['style_option']) );
		}
		
		$css_files[$tikilib->get_style_path() . $prefs['style']] = $prefs['style'];
		$css .= file_get_contents( $tikilib->get_style_path() . $prefs['style'] );

// shame - doesn't work on @imported files, might be a way with minified on...
//		preg_match_all( '/@import\s+url\("([^;]*)"\);/', $css, $parts );
//		$imports = array_reverse(array_unique( $parts[1] ));
//		foreach( $imports as $import) {
//			$css_files[$tikilib->get_style_path() . $import] = $import;
//		}
		
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
		
		foreach ($swaps['bordercolors'] as $old => $new) {
			$GLOBALS['tg_old'] = $old;	// for preg_replace_callback on php < 5.3
			$GLOBALS['tg_new'] = $new;
			$css = preg_replace_callback('/(border[^:]*:\s*)(.*)([;\}])/Umis', array( $this, 'processCSSColours'), $css);
		}

		foreach ($swaps['fontsize'] as $old => $new) {
			//                        sizes usually start with a numeric so add a space after the $1
			$css = preg_replace('/(font-size:\s*)' . $old . '/Umis', "$1 " . $new, $css);
		}
		
//		preg_match_all('/font-size:.*?([\d\.]*[^;\} ]*)/i', $mincss, $matches);
//		$fontsizes = $this->currentTheme->processMatches( $matches[1], $css_file, '' );

		return $css;
	}
	
	private function processCSSColours($matches) {
		$out = $matches[1] . str_replace( $GLOBALS['tg_old'], $GLOBALS['tg_new'], $matches[2]) . $matches[3];
		return $out;
	}
	
	public function saveNewTheme($name) {
		$this->currentTheme = new ThemeGenTheme($name);
		$this->currentTheme->savePref();
		if (!empty($_SESSION['tg_preview'])) {
			unset($_SESSION['tg_preview']);
		}
	}
	
	public function updateCurrentTheme($css_file, $swaps) {
		$this->currentTheme->setData(array($swaps, $css_file));
		$this->currentTheme->savePref();
		if (!empty($_SESSION['tg_preview'])) {
			unset($_SESSION['tg_preview']);
		}
	}
	
	public function previewCurrentTheme($css_file, $swaps) {
		$this->currentTheme->setData(array($swaps, $css_file));
		$_SESSION['tg_preview'] = serialize($this->currentTheme->getData());
		$_SESSION['tg_css_file'] = $css_file;
		//$this->currentTheme->savePref();
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
	}
	
	public function initData() {
		global $prefs;
		
		$this->data = array(
			'files' => array(),
			'theme' => $prefs['style'],
			'theme-option' => '',			//$prefs['style_option'], 
		);
	}

	public function initPrefPrefix() {
		$this->prefPrefix = 'themegenerator_theme_';
	}
	
	public function setData($params) {
		global $prefs;

		list($swaps, $css_file) = $params;
		
		if (!$swaps && !$css_file && isset($params['files'])) {
			$this->data = $params;
			return;
		}
		
		if (!isset($this->data['files'][$css_file])) {
			$this->data['files'][$css_file] = array();
		}
		
		foreach($swaps as $type => $swaps2) {
			$this->data['files'][$css_file][$type] = array();
		
			foreach ($swaps2 as $kswap => $swap) {
				if ($kswap !== $swap) {
					$this->data['files'][$css_file][$type][$kswap] = $swap;
				}
			}
		}
		
		$this->data['theme'] = $prefs['style'];
		if ( in_array($prefs['style_option'], array_keys( $this->data['files'] ))) {
			$this->data['theme-option'] = $prefs['style_option'];
		} else {
			$this->data['theme-option'] = '';
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
