<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class ThemeGenLib
{
	private $currentTheme;

	public function ThemeGenLib() {
		return true;
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
		
		if (!empty($_REQUEST['tg_css_file'])) {
			$css_file = $_REQUEST['tg_css_file'];
		} else if (empty($prefs['themegenerator_css_file'])) {
			$css_file = $tikilib->get_style_path() . $prefs['style'];	// maybe site_style?
		} else {
			$css_file = $prefs['themegenerator_css_file'];
		}
		$mincss .= $headerlib->minify_css( $css_file );	// clean out comments etc
		
		
		preg_match_all('/[^-]color:([^\};!]*?)[;\}!]/', $mincss, $matches);
		$colors = $matches[1];
		$colors = array_map('trim', $colors);
		$colors = array_unique($colors);
		$colors = array_filter($colors);
		sort($colors);
		
		preg_match_all('/background-color:([^\};\!]*?)[;\}\!]/', $mincss, $matches);
		$bgcolors = $matches[1];
		$bgcolors = array_map('trim', $bgcolors);
		$bgcolors = array_unique($matches[1]);
		$bgcolors = array_filter($bgcolors);
		sort($bgcolors);
		
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
	
	public function newTheme($name) {
		$theme = new ThemeGenTheme($name);
	}
}

class ThemeGenTheme
{
	private $name = '';
	private $data;
	
	public function ThemeGenTheme($name) {
		$this->name = strtolower( preg_replace('/[\s,\/\|]+/', '_', $tikilib->take_away_accent( $name )) );
		$data = array( 'files' => array() );
	}
	
	public function getPrefName() {
		return 'themegenerator_theme_' . $this->name;
	}
}

class ThemeGenCSSFile
{
	private $file_path;
	private $swaps;
	
	public function ThemeGenCSSFile($file_path) {
		$this->file_path;
	}
}

global $themegenlib;
$themegenlib = new ThemeGenLib();
