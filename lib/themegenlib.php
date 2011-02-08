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
	private $tg_data;

	public function ThemeGenLib() {
		global $prefs;
		
		// array containing customisable elements
		
		// some handy matches
		$unit = '[-+]?[\d\.]+(?:px|em|ex|%|in|cm|mm|pt|pc)?';
		$color = '#[0-9a-f]{3,6}|aqua|black|blue|fuchsia|gray|green|lime|maroon|navy|olive|orange|purple|red|silver|teal|white|yellow';
		$delims = '[\s\!;\}]';	// delimiter with space
		$delimn = '[\!;\}]';	// delimiter with NO space
		$selector = '[\}]\s*([^\{]*)\{[^\}]*';
		$bstyles = 'dotted|dashed|solid|double|groove|ridge|inset|outset';	// no 'none' for now
		
		$this->tg_data = array(
			'colors' => array(
				'types' => array(
					'fgcolors' => array(
						'items' => array(),
						'title' => tra('Foreground Colors'),
						'selector' => 'color',
						'regexps' => array(
							'find' => '/(?<!-)color:\s*('.$color.')[;\}\!]/Umis',
							'context' => '/'.$selector.'(?<!-)color:\s*$0'.$delims.'/Umis',
							'replace' => '/((?<!-)color:\s*)$0('.$delims.')/Umis',
						),
					),
					'bgcolors' => array(
						'items' => array(),
						'title' => tra('Background Colors'),
						'selector' => 'color',
						'regexps' => array(
							'find' => '/background(?:-color)?:[^\};]*?('.$color.')'.$delims.'/Umis',
							'context' => '/'.$selector.'background(?:-color)?:\s*$0'.$delims.'/Umis',
							'replace' => '/(background(?:-color)?:[^;\}]*)$0('.$delims.')/Umis',
						),
					),
				),
				'title' => tra('Colors'),
			),
			'borders' => array(
				'types' => array(
					'bordercolors' => array(
						'items' => array(),
						'title' => tra('Border Colors'),
						'selector' => 'color',
						'regexps' => array(
							'find' => '/border[\w-]?:[^;\}]*(?:('.$color.')'.$delims.')/Umis',
							'context' => '/'.$selector.'border[\w-]?:[^\;\}\!]*$0[^\;\}\!]*'.$delimn.'/Umis',
							'replace' => '/border[\w-]?:[^\;\}\!]*$0[^\;\}\!]*'.$delimn.'/Umis',
						),
					),
					'borderwidths' => array(
						'items' => array(),
						'title' => tra('Border Widths'),
						'selector' => 'size',
						'regexps' => array(
							'find' => '/border(?!-radius)[\w-]*(?<!radius):[^\};]*((?<![\da-f#])'.$unit.')[\s;\}\!]/Umis',
							'context' => '/'.$selector.'border(?:(?!-radius)[\w-]*(?<!radius))?:[^\}]*(?<![\da-f#])$0'.$delims.'/Umis',
							'replace' => '/(border(?!-radius)[\w-]*(?<!radius):[^;\}]*)(?<![\da-f#])$0('.$delims.')/Umis',
						),
					),
					'borderstyles' => array(
						'items' => array(),
						'title' => tra('Border Styles'),
						'selector' => 'borderstyle',
						'regexps' => array(
							'find' => '/border[\w-]*:[^\};]*('.$bstyles.')[\s;\}\!]/Umis',
							'context' => '/'.$selector.'border[\w-]*:.*$0'.$delims.'/Umis',
							'replace' => '/(border[\w-]*:[^;\}]*)$0('.$delims.')/Umis',
						),
					),
					'borderradii' => array(
						'items' => array(),
						'title' => tra('Border Radii'),
						'selector' => 'size',
						'regexps' => array(
							'find' => '/border(?:-[^\};]*)?-radius(?:-[^\};]*)?:[^\};]*('.$unit.')[\s;\}\!]/Umis',
							'context' => '/'.$selector.'border(?:-.*)?:.*(?<![\da-f#])$0'.$delims.'/Umis',
							'replace' => '/(border[^:]*:[^;\}]*)(?<![\da-f#])$0('.$delims.')/Umis',
						),
					),
				),
				'title' => tra('Borders'),
			),
			'typography' => array(
				'types' => array(
					'fontsize' => array(
						'items' => array(),
						'title' => tra('Font Sizes'),
						'selector' => 'size',
						'regexps' => array(
							'find' => '/font-size:[^\};]*?('.$unit.')/i',
							'context' => '/'.$selector.'font-size:\s*$0'.$delims.'/Umis',
							'replace' => '/(font-size:\s*)$0('.$delims.')/Umis',
						),
					),
					'lineheight' => array(
						'items' => array(),
						'title' => tra('Line Heights'),
						'selector' => 'size',
						'regexps' => array(
							'find' => '/line-height:[^\};]*?('.$unit.')/i',
							'context' => '/'.$selector.'line-height:\s*$0'.$delims.'/Umis',
							'replace' => '/(line-height:\s*)$0('.$delims.')/Umis',
						),
					),
					'font' => array(
						'items' => array(),
						'title' => tra('Font'),
						'selector' => 'text',
						'regexps' => array(
							'find' => '/font:\s*?([^;\}]*)/i',
							'context' => '/'.$selector.'font:\s*$0'.$delimn.'/Umis',
							'replace' => '/(font:\s*)$0('.$delimn.')/Umis',
						),
					),
					'fontfamily' => array(
						'items' => array(),
						'title' => tra('Font Families'),
						'selector' => 'fontfamily',
						'regexps' => array(
							'find' => '/font-family:\s*?([^;\}]*)/i',
							'context' => '/'.$selector.'font-family:\s*$0'.$delimn.'/Umis',
							'replace' => '/(font-family:\s*)$0('.$delimn.')/Umis',
						),
					),
				),
				'title' => tra('Typography'),
			),
		);
		
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
		// units converter
		$headerlib->add_jsfile('lib/jquery/pxem.jQuery.js');

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
		
		foreach ($this->tg_data as $secName => &$secData) {
			foreach ($secData['types'] as $typeName => &$typeData) {
				$typeData['items'] = $this->currentTheme->findMatches($typeData['regexps']['find'], $mincss, $css_file, $typeName);
				$this->findContexts($typeData['items'], $mincss, $typeData['regexps']['context']);
			}
		}
		
		$smarty->assign_by_ref( 'tg_data', $this->tg_data );
		
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
		
		foreach ($this->tg_data as $secName => &$secData) {
			foreach ($secData['types'] as $typeName => &$typeData) {
				if (isset($swaps[$typeName])) {
					foreach ($swaps[$typeName] as $old => $new) {
						$reg = str_replace( '$0', preg_quote($old, '/'), $typeData['regexps']['replace'] );
						if (!in_array($typeName, array( 'bordercolors' ))) {
							$css = preg_replace( $reg, '$1 ' . html_entity_decode($new) . '$2', $css);
						} else {
							$GLOBALS['tg_old'] = $old;	// for preg_replace_callback on php < 5.3
							$GLOBALS['tg_new'] = html_entity_decode($new);
							$css = preg_replace_callback($reg, array( $this, 'processCSSMultiVars'), $css);
						}
					}
				}
			}
		}
		
// still need to deal with multi colour border defs
//		foreach ($swaps['bordercolors'] as $old => $new) {
//			$GLOBALS['tg_old'] = $old;	// for preg_replace_callback on php < 5.3
//			$GLOBALS['tg_new'] = $new;
//			$css = preg_replace_callback('/(border[^:]*:\s*)(.*)([;\}])/Umis', array( $this, 'processCSSColours'), $css);
//		}


		return $css;
	}
	
	private function processCSSMultiVars($matches) {
		$out = str_replace( $GLOBALS['tg_old'], $GLOBALS['tg_new'], $matches[0]);
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
