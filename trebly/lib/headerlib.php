<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

class HeaderLib
{
	var $title;
	var $jsfiles;
	var $js;
	var $js_config;
	var $jq_onready;
	var $cssfiles;
	var $css;
	var $rssfeeds;
	var $metatags;
	var $hasDoneOutput;
	var $minified;
	var $wysiwyg_parsing;

	function __construct() {
		$this->title = '';
		$this->jsfiles = array();
		$this->js = array();
		$this->js_config = array();
		$this->jq_onready = array();
		$this->cssfiles = array();
		$this->css = array();
		$this->rssfeeds = array();
		$this->metatags = array();
		$this->hasDoneOutput = false;
		$this->minified = array();
		$this->wysiwyg_parsing = false;
	}

	function convert_cdn( $file, $type = null ) {
		global $prefs, $tikiroot;

		$https_mode = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';

		$cdn_pref = $https_mode ? $prefs['tiki_cdn_ssl'] : $prefs['tiki_cdn'];
	
		if( !empty($cdn_pref) && 'http' != substr( $file, 0, 4 ) && $type !== 'dynamic' ) {
			$file = $cdn_pref . $tikiroot . $file;
		}

		return $file;
	}

	function set_title($string) {
		$this->title = urlencode($string);
	}

	function add_jsfile($file,$rank=0,$minified=false) {
		if (!$this->wysiwyg_parsing && (empty($this->jsfiles[$rank]) or !in_array($file,$this->jsfiles[$rank]))) {
			$this->jsfiles[$rank][] = $file;
			if ($minified) {
				$this->minified[$file] = $minified;
			}
		}
	}

	function add_js_config($script,$rank=0) {
		if (!$this->wysiwyg_parsing && (empty($this->js_config[$rank]) or !in_array($script,$this->js_config[$rank]))) {
			$this->js_config[$rank][] = $script;
		}
		if ($this->hasDoneOutput) {	// if called after smarty parse header.tpl return the script so the caller can do something with it
			return $this->wrap_js($script);
		} else {
			return '';
		}
	}

	function add_js($script,$rank=0) {
		if (!$this->wysiwyg_parsing && (empty($this->js[$rank]) or !in_array($script,$this->js[$rank]))) {
			$this->js[$rank][] = $script;
		}
		if ($this->hasDoneOutput) {	// if called after smarty parse header.tpl return the script so the caller can do something with it
			return $this->wrap_js($script);
		} else {
			return '';
		}
	}

	/**
	 * Adds lines or blocks of JQuery JavaScript to $(document).ready handler
	 * @param $script = Script to execute
	 * @param $rank   = Execution order (default=0)
	 * @return nothing
	 */
	function add_jq_onready($script,$rank=0) {
		if (!$this->wysiwyg_parsing && (empty($this->jq_onready[$rank]) or !in_array($script,$this->jq_onready[$rank]))) {
			$this->jq_onready[$rank][] = $script;
		}
		if ($this->hasDoneOutput) {	// if called after smarty parse header.tpl return the script so the caller can do something with it
			return $this->wrap_js("\$(document).ready(function(){".$script."});\n");
		} else {
			return '';
		}
	}

	function add_cssfile($file,$rank=0) {
		if (empty($this->cssfiles[$rank]) or !in_array($file,$this->cssfiles[$rank])) {
			$this->cssfiles[$rank][] = $file;
		}
	}

	function replace_cssfile($old, $new, $rank) {
		foreach ($this->cssfiles[$rank] as $i=>$css) {
			if ($css == $old) {
				$this->cssfiles[$rank][$i] = $new;
				break;
			}
		}
	}

	function drop_cssfile($file) {
		foreach ($this->cssfiles as $rank=>$data) {
			foreach ($data as $f) {
				if ($f != $file) {
					$out[$rank][] = $f;
				}
			}
		}
		$this->cssfiles = $out;
	}

	function add_css($rules,$rank=0) {
		if (empty($this->css[$rank]) or !in_array($rules,$this->css[$rank])) {
			$this->css[$rank][] = $rules;
		}
	}

	function add_rssfeed($href,$title,$rank=0) {
		if (empty($this->rssfeeds[$rank]) or !in_array($href,array_keys($this->rssfeeds[$rank]))) {
			$this->rssfeeds[$rank][$href] = $title;
		}
	}

	function set_metatags($tag,$value,$rank=0) {
		$tag = addslashes($tag);
		$this->metatags[$tag] = $href;
	}

	function output_headers() {
		global $style_ie6_css, $style_ie7_css, $style_ie8_css;
		require_once('lib/smarty_tiki/modifier.escape.php');

		ksort($this->cssfiles);
		ksort($this->css);
		ksort($this->rssfeeds);

		$back = "\n";
		if ($this->title) {
			$back = '<title>'.smarty_modifier_escape($this->title)."</title>\n\n";
		}

		if (count($this->metatags)) {
			foreach ($this->metatags as $n=>$m) {
				$back.= "<meta name=\"" . smarty_modifier_escape($n) . "\" content=\"" . smarty_modifier_escape($m) . "\" />\n";
			}
			$back.= "\n";
		}

		$back .= $this->output_css_files();

		if (count($this->css)) {
			$back.= "<style type=\"text/css\"><!--\n";
			foreach ($this->css as $x=>$css) {
				$back.= "/* css $x */\n";
				foreach ($css as $c) {
					$back.= "$c\n";
				}
			}
			$back.= "-->\n</style>\n\n";
		}

		// Handle theme's special CSS file for IE6 hacks
		$back .= "<!--[if lt IE 7]>\n"
				.'<link rel="stylesheet" href="' . $this->convert_cdn('css/ie6.css') . '" type="text/css" />'."\n";
		if ( $style_ie6_css != '' ) {
			$back .= '<link rel="stylesheet" href="'.smarty_modifier_escape($this->convert_cdn($style_ie6_css)).'" type="text/css" />'."\n";
		}
		$back .= "<![endif]-->\n";
		$back .= "<!--[if IE 7]>\n"
				.'<link rel="stylesheet" href="css/ie7.css" type="text/css" />'."\n";
		if ( $style_ie7_css != '' ) {
			$back .= '<link rel="stylesheet" href="'.smarty_modifier_escape($this->convert_cdn($style_ie7_css)).'" type="text/css" />'."\n";
		}
		$back .= "<![endif]-->\n";
		$back .= "<!--[if IE 8]>\n"
				.'<link rel="stylesheet" href="css/ie8.css" type="text/css" />'."\n";
		if ( $style_ie8_css != '' ) {
			$back .= '<link rel="stylesheet" href="'.smarty_modifier_escape($this->convert_cdn($style_ie8_css)).'" type="text/css" />'."\n";
		}
		$back .= "<![endif]-->\n";

		if (count($this->rssfeeds)) {
			foreach ($this->rssfeeds as $x=>$rssf) {
				$back.= "<!-- rss $x -->\n";
				foreach ($rssf as $rsstitle=>$rssurl) {
					$back.= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"".smarty_modifier_escape($this->convert_cdn($rsstitle))."\" href=\"".smarty_modifier_escape($rssurl)."\" />\n";
				}
			}
			$back.= "\n";
		}
		$this->hasDoneOutput = true;
		return $back;
	}

	function output_js_files() {
		global $prefs;

		ksort($this->jsfiles);

		$back = "\n";

		if (count($this->jsfiles)) {

			if( $prefs['tiki_minify_javascript'] == 'y' ) {

				$jsfiles = $this->getMinifiedJs();

			} else {
				$jsfiles = $this->jsfiles;
			}

			foreach ($jsfiles as $x=>$jsf) {
				$back.= "<!-- jsfile $x -->\n";
				foreach ($jsf as $jf) {
					$jf = $this->convert_cdn( $jf, $x );
					$back.= "<script type=\"text/javascript\" src=\"".smarty_modifier_escape($jf)."\"></script>\n";
				}
			}
			$back.= "\n";
		}
		return $back;
	}

	public function getMinifiedJs() {
		global $tikidomainslash;
		
		$dynamic = array();
		if( isset( $this->jsfiles['dynamic'] ) ) {
			$dynamic = $this->jsfiles['dynamic'];
			unset( $this->jsfiles['dynamic'] );
		}

		$external = array();
		if( isset( $this->jsfiles['external'] ) ) {
			$external = $this->jsfiles['external'];
			unset( $this->jsfiles['external'] );
		}
		
		$hash = md5( serialize( $this->jsfiles ) );
		$file = 'temp/public/'.$tikidomainslash."minified_$hash.js";
		$minified_files = array();

		if( ! file_exists( $file ) ) {
			require_once 'lib/minify/JSMin.php';
			$minified = '/* ' . print_r( $this->jsfiles, true ) . ' */';
			foreach( $this->jsfiles as $x => $files ) {
				foreach( $files as $f ) {
					$content = file_get_contents( $f );
					if ( ! preg_match('/min\.js$/', $f) and $this->minified[$f] !== true) {
						$minified .= JSMin::minify( $content );
					} else {
						$minified .= "\n// skipping minification for $f \n" . $content;
					}
				}
			}

			file_put_contents( $file, $minified );
			chmod($file, 0644);
		}

		$minified_files[] = $file;
		return array(
			'external' => $external,
			'dynamic' => $dynamic,
			$minified_files,
		);
	}

	private function getJavascript() {
		$content = '';

		foreach( $this->jsfiles as $x => $files ) {
			foreach( $files as $f ) {
				$content .= file_get_contents( $f );
			}
		}

		return $content;
	}

	function output_js_config($wrap = true) {
		if (count($this->js_config)) {
			ksort($this->js_config);
			$back = "\n<!-- js_config before loading JSfile -->\n";
			$b = "";
			foreach ($this->js_config as $x=>$js) {
        $b.= "// js $x \n";
        foreach ($js as $j) {
          $b.= "$j\n";
        }
      }
      if ( $wrap === true ) {
        $back .= $this->wrap_js($b);
      } else {
        $back .= $b;
      }
    }

		return $back;

	}
	function output_js($wrap = true) {	// called in tiki.tpl - JS output at end of file now (pre 5.0)
		global $prefs;

		ksort($this->js);
		ksort($this->jq_onready);

		$back = "\n";

		if (count($this->js)) {
			$b = '';
			foreach ($this->js as $x=>$js) {
				$b.= "// js $x \n";
				foreach ($js as $j) {
					$b.= "$j\n";
				}
			}
			if ( $wrap === true ) {
				$back .= $this->wrap_js($b);
			} else {
				$back .= $b;
			}
		}

		if (count($this->jq_onready)) {
			$b = '$(document).ready(function(){'."\n";
			foreach ($this->jq_onready as $x=>$js) {
				$b.= "// jq_onready $x \n";
				foreach ($js as $j) {
					$b.= "$j\n";
				}
			}
			$b .= "});\n";
			if ( $wrap === true ) {
				$back .= $this->wrap_js($b);
			} else {
				$back .= $b;
			}
		}

		return $back;
	}

	/**
	 * Gets JavaScript and jQuery scripts as an array (for AJAX)
	 * @return array[strings]
	 */
	function getJs() {
		global $prefs;

		ksort($this->js);
		ksort($this->jq_onready);
		$out = array();

		if (count($this->js)) {
			foreach ($this->js as $x=>$js) {
				foreach ($js as $j) {
					$out[] = "$j\n";
				}
			}
		}
		if (count($this->jq_onready)) {
			$b = '$(document).ready(function(){'."\n";
			foreach ($this->jq_onready as $x=>$js) {
				$b.= "// jq_onready $x \n";
				foreach ($js as $j) {
					$b.= "$j\n";
				}
			}
			$b .= "}) /* end on ready */;\n";
			$out[] = $b;
		}
		return $out;
	}


	function getJsFilesList() {
		return $this->jsfiles;
	}
	/**
	 * Gets included JavaScript files (for AJAX)
	 * @return array[strings]
	 */
	function getJsfiles() {

		ksort($this->jsfiles);
		$out = array();

		if (count($this->jsfiles)) {
			foreach ($this->jsfiles as $x=>$jsf) {
				foreach ($jsf as $jf) {
					$out[] = "<script type=\"text/javascript\" src=\"".smarty_modifier_escape($jf)."\"></script>\n";
				}
			}
		}
		return $out;
	}

	function wrap_js($inJs) {
		return "<script type=\"text/javascript\">\n<!--//--><![CDATA[//><!--\n".$inJs."//--><!]]>\n</script>\n";
	}

	function hasOutput() {
		return $this->hasDoneOutput;
	}
	
	
	/**
	 * Get JavaScript tags from html source - used for AJAX responses and cached pages
	 * 
	 * @param string $html - source to search for JavaScript
	 * @param bool $switch_fn_definition - if set converts 'function fName ()' to 'fName = function()' for AJAX
	 * 
	 * @return array of JavaScript strings
	 */
	function getJsFromHTML( $html, $switch_fn_definition = false ) {
		$jsarr = array();
		$js_script = array();
		
		preg_match_all('/(?:<script.*type=[\'"]?text\/javascript[\'"]?.*>\s*?)(.*)(?:\s*<\/script>)/Umis', $html, $jsarr);
		if (count($jsarr) > 1 && is_array($jsarr[1]) && count($jsarr[1]) > 0) {
			$js = preg_replace('/\s*?<\!--\/\/--><\!\[CDATA\[\/\/><\!--\s*?/Umis', '', $jsarr[1]);	// strip out CDATA XML wrapper if there
			$js = preg_replace('/\s*?\/\/--><\!\]\]>\s*?/Umis', '', $js);

			if ($switch_fn_definition) {
				$js = preg_replace('/function (.*)\(/Umis', "$1 = function(", $js);
			}

			$js_script = array_merge($js_script, $js);
		}
		// this is very probably possible as a single regexp, maybe a preg_replace_callback
		// but it was stopping the CDATA group being returned (and life's too short ;)
		// the one below should work afaics but just doesn't! :(
		// preg_match_all('/<script.*type=[\'"]?text\/javascript[\'"]?.*>(\s*<\!--\/\/--><\!\[CDATA\[\/\/><\!--)?\s*?(.*)(\s*\/\/--><\!\]\]>\s*)?<\/script>/imsU', $html, $js);
		
		return $js_script;
	}
	
	public function get_all_css_content() {
		$files = $this->collect_css_files();
		$minified = '';
		foreach( $files['screen'] as $file) {
			$minified .= $this->minify_css( $file );
		}
		$minified = $this->handle_css_imports( $minified );

		return $minified;
	}

	private function output_css_files() {
		$files = $this->collect_css_files();

		$back = $this->output_css_files_list( $files['screen'], 'screen' );
		$back .= $this->output_css_files_list( $files['print'], 'print' );
		return $back;
	}
	
	private function output_css_files_list( $files, $media ) {
		global $prefs;

		$back = '';

		if( $prefs['tiki_minify_css'] == 'y' ) {
			if( $prefs['tiki_minify_css_single_file'] == 'y' ) {
				$files = $this->get_minified_css_single( $files );
			} else {
				$files = $this->get_minified_css( $files );
			}
		}

		foreach( $files as $file ) {
			$file = $this->convert_cdn( $file );
			$back.= "<link rel=\"stylesheet\" href=\"" . smarty_modifier_escape($file) . "\" type=\"text/css\" media=\"" . smarty_modifier_escape($media) . "\" />\n";
		}

		return $back;
	}

	private function get_minified_css( $files ) {
		global $tikidomainslash;
		$out = array();
			$target = 'temp/public/'.$tikidomainslash;

		foreach( $files as $file ) {
			$hash = md5( $file );
			$min = $target . "minified_$hash.css";

			if( ! file_exists( $min ) ) {
				file_put_contents( $min, $this->minify_css( $file ) );
				chmod($min, 0644);
			}

			$out[] = $min;
		}

		return $out;
	}

	private function get_minified_css_single( $files ) {
		global $tikidomainslash;
		$hash = md5( serialize( $files ) );
		$target = 'temp/public/'.$tikidomainslash;
		$file = $target . "minified_$hash.css";

		if( ! file_exists( $file ) ) {
			$minified = '';

			foreach( $files as $f ) {
				$minified .= $this->minify_css( $f );
			}

			$minified = $this->handle_css_imports( $minified );

			file_put_contents( $file, $minified );
			chmod($file, 0644);
		}

		return array( $file );
	}

	private function handle_css_imports( $minified ) {
		global $tikiroot;

		preg_match_all( '/@import\s+url\("([^;]*)"\);/', $minified, $parts );
		$imports = array_unique( $parts[0] );

		$pre = '';
		foreach( $parts[1] as $f ) {
			$pre .= $this->minify_css( $f );
		}

		$minified = $pre . $minified;
		$minified = str_replace( $imports, '', $minified );

		return $minified;
	}

	public function minify_css( $file ) {
		global $tikipath, $tikiroot;
		require_once 'lib/pear/Minify/CSS.php';
		if (strpos($file, $tikiroot) === 0) {
			$file = substr( $file, strlen( $tikiroot ) );
		}

		$currentdir = str_replace($tikipath, $tikiroot, str_replace('\\', '/', dirname(realpath( $file ))));
		if ( $file[0] == '/' ) {
			$file = $tikipath . $file;
		}

		$content = file_get_contents( $file );

		return Minify_CSS::minify( $content, array(
			'prependRelativePath' => $currentdir.'/',
			'bubbleCssImports' => true,
		) );
	}


	private function collect_css_files() {
		global $tikipath, $tikidomain, $style_base;

		$files = array(
			'screen' => array(),
			'print' => array(),
		);

		foreach ($this->cssfiles as $x=>$cssf) {
			foreach ($cssf as $cf) {
				if (!empty($tikidomain) && is_file("styles/$tikidomain/$style_base/$cf")) {
					$cf = "styles/$tikidomain/$style_base/$cf";
				} elseif (is_file("styles/$style_base/$cf")) {
					$cf = "styles/$style_base/$cf";
				}
				$cfprint = str_replace('.css','',$cf) . '-print.css';
				if (!file_exists($tikipath . $cfprint)) {
					$files['screen'][] = $cf;
					$files['print'][] = $cf;
				} else {
					$files['screen'][] = $cf;
					$files['print'][] = $cfprint;
				}
			}
		}
		$files = $this->process_themegen_files($files);

		return $files;
	}
	
	private function process_themegen_files($files) {
		global $prefs, $tikidomainslash, $in_installer;
		
		if (empty($in_installer) && $prefs['feature_themegenerator'] === 'y' && !empty($prefs['themegenerator_theme'])) {
			global $themegenlib; include_once 'lib/themegenlib.php';
			
			$data = $themegenlib->getCurrentTheme()->getData();
			$themename = $themegenlib->getCurrentTheme()->getName();
			if (count($data['files'])) {
				foreach ($data['files'] as $file => $swaps) {
					$i = array_search($file, $files['screen']);
					if ($i !== false) {
						$css = $themegenlib->processCSSFile($file, $swaps);
					
						$hash = md5( $file );
						$target = 'temp/public/'.$tikidomainslash;
						$ofile = $target . "themegen_{$themename}_$hash.css";
						
						file_put_contents( $ofile, $css );
						chmod($ofile, 0644);
						
						$files['screen'][$i] = $ofile;
					}
				}
			}
		}
		return $files;
	}

	function add_map() {
		// Annoying notice with copyrights still remain in google
		//$this->add_jsfile('http://maps.google.com/maps/api/js?v=3.3&sensor=false', 'external');
		$this->add_jsfile('http://openlayers.org/api/2.10/OpenLayers.js', 'external');
		$this->add_js('$(".map-container:not(.done)").addClass("done").createMap();');
	}
}

$headerlib = new HeaderLib;
$smarty->assign_by_ref('headerlib', $headerlib);
