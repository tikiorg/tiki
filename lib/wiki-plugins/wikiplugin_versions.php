<?php
/*
 * $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_versions.php,v 1.10 2007-10-12 07:55:49 nyloth Exp $
 *
 * Versions plugin: Split the text in parts visible only under some conditions:
 * 
 * Syntax:
 * {VERSIONS(nav=>y| n, title=>y| n, default=>)}text{VERSIONS}
 * 
 * Documentation
 * http://doc.tikiwiki.org/PluginVersions
 */
function wikiplugin_versions_help() {
	return tra("Split the text in parts visible only under some conditions").":<br />~np~{VERSIONS(nav=>y|n,title=>y|n,default=>)}".tra("text")."{VERSIONS}~/np~";
}

function wikiplugin_versions($data, $params) {
	global $use_best_language, $prefs;
	if (isset($params) and is_array($params)) {
		extract ($params,EXTR_SKIP);
	}
	$data = $data;
	$navbar = '';
	if (!isset($default)) { $default = 'Default'; }
	if (!isset($title)) { $title = 'y'; }
	if (!isset($nav)) { $nav = 'n'; }
	
	preg_match_all('/---\(([^\):]*)( : [^\)]*)?\)---*/',$data,$v);

	if (isset($type) and $type == 'host') {
		if (isset($_SERVER['TIKI_VERSION'])) {
			$vers = $_SERVER['TIKI_VERSION'];
		} else {
			$vers = $default;
		}
	} else {
		if (isset($_REQUEST['tikiversion'])) {
			$vers = $_REQUEST['tikiversion'];
		} elseif ($use_best_language == 'y' and in_array($prefs['language'],$v[1]))  {
			$vers = $prefs['language'];
		} else {
			$vers = $default;
		}
		$type = "request";
	}
	
	if (in_array($vers,$v[1])) {
		$p = array_search($vers,$v[1]) + 1;
	} else {
		$p = 0;
	}

	if ($p == 0) {
		if (strpos($data,'---(')) {
			$data = substr($data,0,strpos($data,'---('));
		}
		if ($nav == 'n' and $title == 'y') { $data = "<b class='versiontitle'>". $default .'</b>'.$data; }
		$data = ltrim(substr($data,strpos("\n",$data)));
	} elseif (isset($v[1][$p-1]) and strpos($data,'---('.$v[1][$p-1])) {
		if ($nav == 'n' and $title == 'y') {
			$data = substr($data,strpos($data,'---('.$v[1][$p-1]));
			$data = preg_replace('/\)---*[\r\n]*/',"</b>\n","<b class='versiontitle'>". substr($data,4));
		} else {
			$data = substr($data,strpos($data,'---('.$v[1][$p-1]));
			$data = preg_replace('/.*\)---*[\r\n]*/',"", substr($data,4));
		}
		if (strpos($data,'---(')) {
			$data = substr($data,0,strpos($data,'---('));
		}
	}
	
	if ($nav == 'y') {
		$highed = false;
		for ($i=0;$i<count($v[1]);$i++) {
			$version = $v[1][$i];
			$ver = $version.$v[2][$i];
			if ($i == $p-1) {
				$high = " highlight";
				$highed = true;
			} else {
				$high = '';
			}
			if ($type == 'host') {
				$vv = preg_replace('/[^a-z0-9]/','',strtolower($version));
				$navbar.= ' <span class="button2'.$high.'"><a href="http://'. $vv .'.'. preg_replace("/".$v[1][$p]."/","",$_SERVER['SERVER_NAME']) . preg_replace("~(\?|&)tikiversion=[^&]*~","",$_SERVER['REQUEST_URI']) .'" class="linkbut">'. $ver .'</a></span>';
			} else {
				$navbar.= ' <span class="button2'.$high.'"><a href="';
				if (strpos($_SERVER['REQUEST_URI'],'?')) { 
					$navb = preg_replace("~(\?|&)tikiversion=[^&]*~","",$_SERVER['REQUEST_URI']);
				} else {
					$navb = $_SERVER['REQUEST_URI'];
				}
				if (strpos($navb,'?')) {
					$navbar.= "$navb&";
				} else {
					$navbar.= "$navb?";
				}
				$navbar.= 'tikiversion='. urlencode($version) .'" class="linkbut">'. $ver .'</a></span>';
			}
		}
		
		if (!$highed) { $high = " highlight"; } else { $high = ''; }
		if ($type == 'host') {
			$navbar = '<span class="button2'.$high.'"><a href="http://'. preg_replace("/".$v[1][$p]."/","",$_SERVER['SERVER_NAME']) . preg_replace("~(\?|&)tikiversion=[^&]*~","",$_SERVER['REQUEST_URI']) .'" class="linkbut">'.$default.'</a></span>'.$navbar;
		} else {
			$navbar = '<span class="button2'.$high.'"><a href="'. preg_replace("~(\?|&)tikiversion=[^&]*~","",$_SERVER['REQUEST_URI']) .'" class="linkbut">'.$default.'</a></span>'.$navbar;
		}
		$data = '<div class="versions"><div class="versionav">'.$navbar."</div><div>\n".$data."</div>\n</div>";
	}

	return $data;
}

?>
