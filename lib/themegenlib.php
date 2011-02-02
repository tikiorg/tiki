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
		global $headerlib, $smarty, $prefs, $tikilib;
		
		if ($this->currentTheme->initDone ||	// filter out unnecessay setups
				strpos($_SERVER["SCRIPT_NAME"], 'tiki-download_file.php') !== false ||
				strpos($_SERVER["SCRIPT_NAME"], 'tiki-ajax_services.php') !== false) {
			
			return;
		}
		
		// tiki themegen include
		$headerlib->add_jsfile('lib/jquery_tiki/tiki-themegenerator.js');
		$headerlib->add_cssfile('css/admin.css');
		
		// set up colorpicker
		$headerlib->add_cssfile('lib/jquery/colorpicker/css/colorpicker.css');
		$headerlib->add_cssfile('lib/jquery_tiki/colorpicker/layout.css');
	
		$headerlib->add_jsfile('lib/jquery/colorpicker/js/colorpicker.js');
		
		// colour lib
		$headerlib->add_jsfile('lib/jquery/jquery.color.js');
		
		if (!empty($_COOKIE['themegen'])) {
			if (strpos($_COOKIE['themegen'], 'state:open') !== false) {
				$headerlib->add_jq_onready('openThemeGenDialog();', 100);
			}
		} else if (!empty($_SESSION['tg_preview'])) {	// or remove preview session if no cookie
			unset($_SESSION['tg_preview']);
		}
		
		// if not admin/look page so add open dialog js or remove session and return
		if (strpos($_SERVER["SCRIPT_NAME"], 'tiki-admin.php') === false ||
				strpos($_SERVER["QUERY_STRING"], 'page=look') === false) {
			return;
		}
		
		if (!empty($_SESSION['tg_preview'])) {
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
					$css_file = $tikilib->get_style_path() . $prefs['style'];
				}
			} else {
				$css_file = $tikilib->get_style_path() . $prefs['style'];
			}
		}
		$mincss = $headerlib->minify_css( $css_file );	// clean out comments etc
		
		$mincss = '}' . preg_replace('/@import url(.*);/Umis', '', $mincss);
		
		$colors		  = $this->currentTheme->findMatches('/[^-]color:([^\};!]*?)[;\}!]/i', $mincss, $css_file, 'fgcolors');
		$this->findContexts($colors, $mincss, '/[\}]\s*([^\{@]*)\{[^\}]*[^-]?color:\s*$0[\}; !]/Umis');
		
		$bgcolors	  = $this->currentTheme->findMatches('/background(?:-color)?:[^\};]*?(#[0-9A-F]{3,6})[\s;\}\!]/i', $mincss, $css_file, 'bgcolors');
		$this->findContexts($bgcolors, $mincss, '/[\}]\s*([^\{@]*)\{[^\}]*background(?:-color)?:\s*$0[\}; !]/Umis');
		
		$bordercolors = $this->currentTheme->findMatches('/border(?:-[^\};]*)?:[^\};]*(#[0-9A-F]{3,6})[\s;\}\!]/Umis', $mincss, $css_file, 'bordercolors');
		$this->findContexts($bordercolors, $mincss, '/[\}]\s*([^\{@]*)\{[^\}]*border(?:-.*)?:.*$0[\}; !]/Umis');
		
		$fontsizes	  = $this->currentTheme->findMatches('/font-size:[^\};]*?([\d\.]*[^;\} ]*)/i', $mincss, $css_file, 'fontsize');
		$this->findContexts($fontsizes, $mincss, '/[\}]\s*([^\{@]*)\{[^\}]*font-size:\s*$0[\}; !]/Umis');
		
		$fontfamilies = $this->currentTheme->findMatches('/font-family:\s*?([^;\}]*)/i', $mincss, $css_file, 'fontfamily', false);
		$this->findContexts($fontfamilies, $mincss, '/[\}]\s*([^\{@]*)\{[^\}]*font-family:\s*$0[\};]/Umis');
		
		$fonts		  = $this->currentTheme->findMatches('/font:\s*?([^;\}]*)/i', $mincss, $css_file, 'font', false);
		$this->findContexts($fonts, $mincss, '/[\}]\s*([^\{@]*)\{[^\}]*font:\s*$0[\};]/Umis');
		
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
					'font' => array(
						'items' => $fonts,
						'title' => tra('Font:'),
					),
					'fontfamily' => array(
						'items' => $fontfamilies,
						'title' => tra('Font Families:'),
					),
				),
				'title' => tra('Typography:'),
			),
		);
		
		$smarty->assign_by_ref( 'tg_data', $tg_data );
		
//		$css_files = $this->setupCSSFiles();
//		if (empty($css_file) && count($css_files) > 0) {
//			$css_file = $css_files[1];
//		}
		$smarty->assign_by_ref('tg_css_files', $this->setupCSSFiles());
		$smarty->assign_by_ref('tg_css_file', $css_file);
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_REQUEST['tg_open_dialog']))) {
			$smarty->display('themegen.tpl');
			die;
		}
		
		$this->currentTheme->initDone = true;		
	}
	
	private function findContexts( &$items, $haystack, $regexp) {
		$m = null;
		foreach ($items as &$item) {
			$c = preg_match_all( str_replace('$0', preg_quote( html_entity_decode($item['old']), '/'), $regexp), $haystack, $m);
			if ($c) {
				$item['contexts'] = htmlentities('<ul class="tgContexts"><li>' . implode('</li><li>', str_replace(',', ',<br />', $m[1])) . '</li></ul>');
			} else {
				$item['contexts'] = '<ul><li>Not found (error)</li></ul>';
			}
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
		
		foreach ($swaps['font'] as $old => $new) {
			//                        also usually start with a numeric so add a space after the $1
			$css = preg_replace('/(font:\s*)' . preg_quote($old, '/') . '/Umis', "$1 " . html_entity_decode($new), $css);
		}
		
		foreach ($swaps['fontfamily'] as $old => $new) {
			$css = preg_replace('/(font-family:\s*)' . preg_quote($old, '/') . '/Umis', "$1" . html_entity_decode($new), $css);
		}
		

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
	var $initDone;
	
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
		$this->initDone = false;
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
					$this->data['files'][$css_file][$type][htmlentities($kswap)] = htmlentities($swap);
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
	
	public function findMatches( $regexp, $haystack, $filename, $type, $lower = true, $matchNumber = 1) {
		preg_match_all( $regexp, $haystack, $matches );
		$items = $this->processMatches( $matches[$matchNumber], $filename, $type, $lower );
		return $items;
	}
	
	
	private function processMatches($matches, $css_file, $type, $lower) {
		$processed = array();
		if (is_array( $matches )) {
			$matches = array_map('trim', $matches);
			if ($lower) {
				$matches = array_map('strtolower', $matches);
			}
			$matches = array_unique($matches);
			$matches = array_filter($matches);
			sort($matches);
			//$data = $this->currentTheme->loadPref();
			foreach ($matches as $match) {
				$match = htmlentities($match);
				$processed[$match] = array();
				$processed[$match]['old'] = $match;
				if (isset($this->data['files'][$css_file][$type][$match])) {
					$processed[$match]['new'] = $this->data['files'][$css_file][$type][$match];
				} else {
					$processed[$match]['new'] = $match;
				}
			}
		}
		return $processed;
	}
	
	
}


global $themegenlib;
$themegenlib = new ThemeGenLib();
