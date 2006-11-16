<?php

class HeaderLib {
	var $title;
	var $jsfiles;
	var $js;
	var $cssfiles;
	var $css;
	var $rssfeeds;
	var $matatags;
	
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

	function add_css($rules,$rank=0) {
		if (empty($this->css[$rank]) or !in_array($script,$this->css[$rank])) {
			$this->css[$rank][] = $script;
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
			foreach ($this->cssfiles as $cssf) {
				foreach ($cssf as $cf) {
					$back.= "<link rel=\"stylesheet\" href=\"$cf\" type=\"text/css\" />\n";
				}
			}
		}

		if (count($this->css)) {
			$back.= "<style><!--\n";
			foreach ($this->css as $css) {
				foreach ($css as $c) {
					$back.= "$c\n";
				}
			}
			$back.= "-->\n</style>\n\n";
		}
		
		if (count($this->jsfiles)) {
			foreach ($this->jsfiles as $jsf) {
				foreach ($jsf as $jf) {
					$back.= "<script type=\"javascript\" href=\"$jf\"></script>\n";
				}
			}
			$back.= "\n";
		}

		if (count($this->js)) {
			$back.= "<script type=\"javascript\">\n<!--\n";
			foreach ($this->js as $js) {
				foreach ($js as $j) {
					$back.= "$j\n";
				}
			}
			$back.= "-->\n</script>\n\n";
		}
		
		if (count($this->rssfeeds)) {
			foreach ($this->rssfeeds as $rssf) {
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
?>
