<?php /* 
$Id: /cvsroot/tikiwiki/tiki/lib/tikibot/modules/google.php,v 1.3 2004-01-08 23:50:17 damosoft Exp $

Google "get-the-first-link" wollabot module
*/

class google extends Wollabot_Module {

	function google() {
		$this->bind_prefix('google_en','!G ');
		$this->bind_prefix('google_en','!GOO ');
		$this->bind_prefix('google_fr','!GF ');
		$this->bind_prefix('google_cs','!GC ');
		$this->bind_prefix('google_tiki','!GT ');
	}

	function unhtmlentities ($string) {
		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);
		return strtr($string, $trans_tbl);
	}

	function google_tiki($params) {
		array_shift($params["message_exploded"]);
		$param = implode(" ", $params["message_exploded"]);
		$url = "http://www.google.com/search?as_sitesearch=tikiwiki.org&q=".urlencode($param);
		$this->google_lookup($params,$url);
	}

	function google_cs($params) {
		array_shift($params["message_exploded"]);
		$param = implode(" ", $params["message_exploded"]);
		$url = "http://www.google.com/search?hl=cs&lr=lang_cs&q=".urlencode($param);
		$this->google_lookup($params,$url);
	}

	function google_fr($params) {
		array_shift($params["message_exploded"]);
		$param = implode(" ", $params["message_exploded"]);
		$url = "http://www.google.com/search?hl=fr&lr=lang_fr&q=".urlencode($param);
		$this->google_lookup($params,$url);
	}

	function google_en($params) {
		array_shift($params["message_exploded"]);
		$param = implode(" ", $params["message_exploded"]);
		$url = "http://www.google.com/search?hl=en&lr=lang_en&q=".urlencode($param);
		$this->google_lookup($params,$url);
	}
			
	function google_lookup($params,$url) {
		$param = implode(" ", $params["message_exploded"]);
		if (substr($params['channel'],0,1) == '#') {
			$target = $params['channel'];
			$who = $params["nick"].": ";
		} else {
			$target = $params["nick"];
			$who = '';
		}
		$this->wollabot->print_log("GOOGLE: Looking up '$param' for $who'$target'");
		$this->wollabot->print_log("GOOGLE: $url");
		
		$buffer = "";
		if ($fp = fsockopen ("www.google.com", 80, $errno, $errstr, 30)) {
			fputs ($fp, "GET $url HTTP/1.0\r\nHost: perdu.com\r\n\r\n");
			while (!feof($fp)) $buffer .= fgets ($fp, 1024);
			fclose ($fp);
		} else {
			$this->wollabot->print_log("GOOGLE: Socket connection failed: $errstr ($errno)");
		}
		$start = strpos ($buffer, 'class=g')+16;
		$buffer = substr($buffer, $start);
		$end = strpos ($buffer, '</a>');
		$buffer = substr($buffer, 0, $end);
		$buffer = str_replace('</b>', '', $buffer);
		$buffer = str_replace('<b>', '', $buffer);
		$buffer = str_replace('&#39;', "'", $buffer);
		$results = explode('>',$buffer);
		if ($results[1] != '<head') {
			$this->send_privmsg($target, $who.$results[1].' - url: '.$results[0]);
		} else {
			$this->send_privmsg($target, $who."Search string not found");
		}
	}
}

$wollabot->register_module("google");

?>
