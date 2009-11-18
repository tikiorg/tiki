<?php
// $Id$
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class HeaderLib {
	var $title;
	var $jsfiles;
	var $js;
	var $jq_onready;
	var $cssfiles;
	var $css;
	var $rssfeeds;
	var $metatags;
	var $hasDoneOutput;
	
	function __construct() {
		$this->title = '';
		$this->jsfiles = array();
		$this->js = array();
		$this->jq_onready = array();
		$this->cssfiles = array();
		$this->css = array();
		$this->rssfeeds = array();
		$this->metatags = array();
		$this->hasDoneOutput = false;
	}

	function set_title($string) {
		$this->title = urlencode($string);
	}

	function add_jsfile($file,$rank=0) {
		if (empty($this->jsfiles[$rank]) or !in_array($file,$this->jsfiles[$rank])) {
			$this->jsfiles[$rank][] = $file;
		}
	}

	function add_js($script,$rank=0) {
		if (empty($this->js[$rank]) or !in_array($script,$this->js[$rank])) {
			$this->js[$rank][] = $script;
		}
		if ($this->hasDoneOutput) {	// if called after smarty parse header.tpl return the script so the caller can do something with it
			return $this->wrap_js($script);
		} else {
			return '';
		}
	}

	/**
	 * Adds lines or blocks of JQuery JavaScript to $jq(document).ready handler
	 * @param $script = Script to execute
	 * @param $rank   = Execution order (default=0)
	 * @return nothing
	 */
	function add_jq_onready($script,$rank=0) {
		if (empty($this->jq_onready[$rank]) or !in_array($script,$this->jq_onready[$rank])) {
			$this->jq_onready[$rank][] = $script;
		}
		if ($this->hasDoneOutput) {	// if called after smarty parse header.tpl return the script so the caller can do something with it
			return $this->wrap_js("\$jq(\"document\").ready(function(){".$script."});\n");
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

		ksort($this->cssfiles);
		ksort($this->css);
		ksort($this->rssfeeds);

		$back = "\n";
		if ($this->title) {
			$back = '<title>'.$this->title."</title>\n\n";
		}
		
		if (count($this->metatags)) { 
			foreach ($this->metatags as $n=>$m) {
				$back.= "<meta name=\"$n\" content=\"$m\" />\n";
			}
			$back.= "\n";
		}
		
		if (count($this->cssfiles)) {
			foreach ($this->cssfiles as $x=>$cssf) {
				$back.= "<!-- cssfile $x -->\n";
				foreach ($cssf as $cf) {					
					global $tikipath, $tikidomain, $style_base;
					if (!empty($tikidomain) && is_file("styles/$tikidomain/$style_base/$cf")) {
						$cf = "styles/$tikidomain/$style_base/$cf";
					} elseif (is_file("styles/$style_base/$cf")) {
						$cf = "styles/$style_base/$cf";
					}
					$cfprint = str_replace('.css','',$cf) . '-print.css';
					if (!file_exists($tikipath . $cfprint)) {
						$back.= "<link rel=\"stylesheet\" href=\"$cf\" type=\"text/css\" />\n";
					} else {
						// add support for print style sheets
						$back.= "<link rel=\"stylesheet\" href=\"$cf\" type=\"text/css\" media=\"screen\" />\n";
						$back.= "<link rel=\"stylesheet\" href=\"$cfprint\" type=\"text/css\" media=\"print\" />\n";	
					}
				}
			}
		}

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
					.'<link rel="stylesheet" href="css/ie6.css" type="text/css" />'."\n";
			if ( $style_ie6_css != '' ) {
				$back .= '<link rel="stylesheet" href="'.$style_ie6_css.'" type="text/css" />'."\n";
			}
			$back .= "<![endif]-->\n";
			$back .= "<!--[if IE 7]>\n"
					.'<link rel="stylesheet" href="css/ie7.css" type="text/css" />'."\n";
			if ( $style_ie7_css != '' ) {
				$back .= '<link rel="stylesheet" href="'.$style_ie7_css.'" type="text/css" />'."\n";
			}
			$back .= "<![endif]-->\n";
			$back .= "<!--[if IE 8]>\n"
                                        .'<link rel="stylesheet" href="css/ie8.css" type="text/css" />'."\n";
                        if ( $style_ie8_css != '' ) {
                                $back .= '<link rel="stylesheet" href="'.$style_ie8_css.'" type="text/css" />'."\n";
                        }
                        $back .= "<![endif]-->\n";

                        
        $back .= $this->output_js_files();	// TODO move some files to end of page?
        
		if (count($this->rssfeeds)) {
			foreach ($this->rssfeeds as $x=>$rssf) {
				$back.= "<!-- rss $x -->\n";
				foreach ($rssf as $rsstitle=>$rssurl) {
					$back.= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"$rsstitle\" href=\"$rssurl\" />\n";
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
				$dynamic = array();
				if( isset( $this->jsfiles['dynamic'] ) ) {
					$dynamic = $this->jsfiles['dynamic'];
					unset( $this->jsfiles['dynamic'] );
				}

				$jsfiles = $this->getMinifiedJs();

				$jsfiles['dynamic'] = $dynamic;
			} else {
				$jsfiles = $this->jsfiles;
			}

			foreach ($jsfiles as $x=>$jsf) {
				$back.= "<!-- jsfile $x -->\n";
				foreach ($jsf as $jf) {
					$back.= "<script type=\"text/javascript\" src=\"$jf\"></script>\n";
				}
			}
			$back.= "\n";
		}
		return $back;
	}

	private function getMinifiedJs() {
		$hash = md5( serialize( $this->jsfiles ) );
		$file = "temp/public/minified_$hash.js";

		if( ! file_exists( $file ) ) {
			$complete = $this->getJavascript();

			require_once 'lib/minify/JSMin.php';
			$minified = '/* ' . print_r( $this->jsfiles, true ) . ' */';
			$minified .= JSMin::minify( $complete );

			file_put_contents( $file, $minified );
		}

		return array(
			array( $file ),
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
	
	function output_js() {	// called in footer.tpl - JS output at end of file now (pre 4.0)
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
			$back.=  $this->wrap_js($b);
		}
		
		if (count($this->jq_onready)) {
			$b = '$jq("document").ready(function(){'."\n";
			foreach ($this->jq_onready as $x=>$js) {
				$b.= "// jq_onready $x \n";
				foreach ($js as $j) {
					$b.= "$j\n";
				}
			}
			$b .= "});\n";
			$back .= $this->wrap_js($b);
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
			$b = '$jq("document").ready(function(){'."\n";
			foreach ($this->jq_onready as $x=>$js) {
				$b.= "// jq_onready $x \n";
				foreach ($js as $j) {
					$b.= "$j\n";
				}
			}
			$b .= "});\n";
			$out[] = $b;
		}
		return $out;
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
					$out[] = "<script type=\"text/javascript\" src=\"$jf\"></script>\n";
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
	
	function include_jquery_ui() {
		global $prefs, $headerlib;
		
		if ($prefs['feature_jquery_ui'] != 'y') {
			if ($prefs['feature_use_minified_scripts'] == 'y') {	// could reduce to only using dialog (needs core, draggable & resizable)
				$headerlib->add_jsfile('lib/jquery/jquery-ui/ui/minified/jquery-ui.min.js');
			} else {
				$headerlib->add_jsfile('lib/jquery/jquery-ui/ui/jquery-ui.js');
			}
			$headerlib->add_cssfile('lib/jquery/jquery-ui/themes/'.$prefs['feature_jquery_ui_theme'].'/jquery-ui.css');
		}
//		// include json parser (not included by default yet - Tiki 4.0 oct 09)
//		if (0 && $prefs['feature_use_minified_scripts'] == 'y') {	// could reduce to only using dialog (needs core, draggable & resizable)
//			$headerlib->add_jsfile('lib/jquery/json2.min.js');
//		} else {
//			$headerlib->add_jsfile('lib/jquery/json2.js');
//		}
	}

}

$headerlib = new HeaderLib;
$smarty->assign_by_ref('headerlib', $headerlib);
