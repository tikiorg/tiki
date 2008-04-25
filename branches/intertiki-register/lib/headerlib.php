<?php

class HeaderLib {
	var $title;
	var $jsfiles;
	var $js;
	var $cssfiles;
	var $css;
	var $rssfeeds;
	var $metatags;
	
	function HeaderLib() {
		$this->title = '';
		$this->jsfiles = array();
		$this->js = array();
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
		ksort($this->jsfiles);
		ksort($this->js);
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
					global $tikipath;
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
			$back.= "<script type=\"text/javascript\">\n<!--\n";
			foreach ($this->js as $x=>$js) {
				$back.= "// js $x \n";
				foreach ($js as $j) {
					$back.= "$j\n";
				}
			}
			$back.= "-->\n</script>\n\n";
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
