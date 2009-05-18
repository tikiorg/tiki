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
	
	function HeaderLib() {
		$this->title = '';
		$this->jsfiles = array();
		$this->js = array();
		$this->jq_onready = array();
		$this->cssfiles = array();
		$this->css = array();
		$this->rssfeeds = array();
		$this->metatags = array();
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
		global $style_ie6_css, $style_ie7_css, $style_ie8_css, $prefs;

		ksort($this->jsfiles);
		ksort($this->js);
		ksort($this->jq_onready);
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
			$back.= "<style><!--\n";
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

		if (count($this->jsfiles)) {
			foreach ($this->jsfiles as $x=>$jsf) {
				$back.= "<!-- jsfile $x -->\n";
				foreach ($jsf as $jf) {
					$back.= "<script type=\"text/javascript\" src=\"$jf\"></script>\n";
				}
			}
			$back.= "\n";
		}

		if (count($this->js)) {
			$back.= "<script type=\"text/javascript\">\n<!--//--><![CDATA[//><!--\n";
			foreach ($this->js as $x=>$js) {
				$back.= "// js $x \n";
				foreach ($js as $j) {
					$back.= "$j\n";
				}
			}
			$back.= "//--><!]]>\n</script>\n\n";
		}
		
		if ($prefs['feature_jquery'] == 'y') {
			if (count($this->jq_onready)) {
				$back .= "<script type=\"text/javascript\">\n<!--//--><![CDATA[//><!--\n";
				$back .= '$jq("document").ready(function(){'."\n";
				foreach ($this->jq_onready as $x=>$js) {
					$back.= "// jq_onready $x \n";
					foreach ($js as $j) {
						$back.= "$j\n";
					}
				}
				$back .= "});\n";
				$back.= "//--><!]]>\n</script>\n";
			}
		}
		
		if (count($this->rssfeeds)) {
			foreach ($this->rssfeeds as $x=>$rssf) {
				$back.= "<!-- rss $x -->\n";
				foreach ($rssf as $rsstitle=>$rssurl) {
					$back.= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"$rsstitle\" href=\"$rssurl\" />\n";
				}
			}
			$back.= "\n";
		}

		return $back;
	}

}

$headerlib = new HeaderLib();
$smarty->assign_by_ref('headerlib', $headerlib);
