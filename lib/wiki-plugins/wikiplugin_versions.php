<?php
/*
 * $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_versions.php,v 1.2 2004-08-12 22:31:46 teedog Exp $
 *
 * Tiki-Wiki BOX plugin.
 * 
 * Syntax:
 * 
 *  {BOX([title=>Title],[bg=>color|#999fff],[width=>num[%]],[align=>left|right|center])}
 *   Content inside box
 *  {BOX}
 * 
 */
function wikiplugin_versions_help() {
	return tra("Split the text in parts visible only under some conditions").":<br />~np~{VERSIONS(nav=>y|n)}".tra("text")."{VERSIONS}~/np~";
}

function wikiplugin_versions($data, $params) {
	
	extract ($params);
	$data = trim($data);
	$type = "default";
	$hosts = array();
	
	preg_match_all('/---\(([^\)]*)\)---*/',$data,$v);

	if (!isset($_REQUEST['tikiversion']) and isset($_SERVER['TIKI_VERSION'])) {
		if (in_array($_SERVER['TIKI_VERSION'],$v[1])) {
			$p = array_search($_SERVER['TIKI_VERSION'],$v[1]) + 1;
		} else {
			$p = 0;
		}
		$type = "host";
	} elseif (isset($_REQUEST['tikiversion'])) {
		if (in_array($_REQUEST['tikiversion'],$v[1])) {
			$p = array_search($_REQUEST['tikiversion'],$v[1]) + 1;
		} else {
			$p = 0;
		}
		$type = "request";
		$hosts = split(',',$_SERVER['TIKI_ALL_VERSIONS']);
	} else {
		$p = 0;
	}

	if ($p == 0) {
		if (strpos($data,'---(')) {
			$data = substr($data,0,strpos($data,'---('));
		}
		$data = substr($data,strpos("\n",$data));
	} elseif (isset($v[1][$p-1]) and strpos($data,'---('.$v[1][$p-1].')---')) {
		$data = substr($data,strpos($data,'---('.$v[1][$p-1].')---'));
		$data = preg_replace('/\)---*[\r\n]*/',"''\n","''".substr($data,4));
		if (strpos($data,'---(')) {
			$data = substr($data,0,strpos($data,'---('));
		}
	}
	
	
	if (isset($nav) and $nav == 'y') {
		$nav = '<div class="versionav">';
		if ($type == 'host') {
			$nav.= '<span class="button2"><a href="http://'. preg_replace("/".$v[1][$p]."/","",$_SERVER['SERVER_NAME']) . preg_replace("~(\?|&)tikiversion=[^&]*~","",$_SERVER['REQUEST_URI']) .'" class="linkbut">Default</a></span>';
		} else {
			$nav.= '<span class="button2"><a href="'. preg_replace("~(\?|&)tikiversion=[^&]*~","",$_SERVER['REQUEST_URI']) .'" class="linkbut">Default</a></span>';
		}
		for ($i=0;$i<count($v[1]);$i++) {
			$ver = $v[1][$i];
			if ($i == $p-1) {
				$version = "<b>$ver</b>";
			} else {
				$version = $ver;
			}
			if ($type == 'host') {
				$vv = preg_replace('/[^a-z0-9]/','',strtolower($ver));
				$nav.= '<span class="button2"><a href="http://'. $vv .'.'. preg_replace("/".$v[1][$p]."/","",$_SERVER['SERVER_NAME']) . preg_replace("~(\?|&)tikiversion=[^&]*~","",$_SERVER['REQUEST_URI']) .'" class="linkbut">'. $version .'</a></span>';
			} elseif ($type == 'request') {
				$nav.= '<span class="button2"><a href="'. preg_replace("~(\?|&)tikiversion=[^&]*~","",$_SERVER['REQUEST_URI']) .'&amp;tikiversion='. urlencode($ver) .'" class="linkbut">'. $version .'</a></span>';
			} else {
				$nav.= '<span class="button2"><a href="'. $_SERVER['REQUEST_URI'] .'&amp;tikiversion='. urlencode($ver) .'" class="linkbut">'. $version .'</a></span>';
			}
		}
		$nav.= "</div>";
		$data = $nav."\n".$data;
	}
	return $data;
}

?>
