<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-import_structuredtext.php,v 1.8 2007-03-06 19:29:49 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}


function parse_st($dump) {
	$bodysep  = '>>>>>>>>>>>>>>>>>>>>>>>>';
	$titlesep = '>>>>>>>>>>--------------';
	$pages = preg_split("/$bodysep/",$dump);
	$res = array();
	array_shift($pages);
	foreach ($pages as $p) {
		$ret['pagename'] = trim(substr($p,0,strpos($p,">")));
		$ret['pagename'] = str_replace(' ','',ucwords(str_replace('_',' ',$ret['pagename'])));
		$ret['body'] = substr($p,strpos($p,$titlesep)+strlen($titlesep));
		$res[] = $ret;
	}
	return $res;
}

$smarty->assign('result', 'n');

if (isset($_REQUEST["import"])) {
	check_ticket('import-st');

	$path = 'dump/'.$tikidomain.'/'.$_REQUEST["path"];

	if (is_file($path)) {
		$fp = fopen($path, "r");
		$full = fread($fp, filesize($path));
		//$full = fread($fp, 16000);
		fclose ($fp);

		$parts = parse_st($full);

		foreach ($parts as $part) {

			$part["body"] = preg_replace("/\[([^\]]*)\]/e", "str_replace(' ','',ucwords('(($1))'))", $part["body"]);
			$part["body"] = preg_replace("/(\(\([^\)]*\)\))/e", "str_replace(' ','',ucwords('$1'))", $part["body"]);
			
			$part["body"] = preg_replace("/( |\n|^)(http:\/\/[^ ]+)( |\n)/", "$1[$2]$3", $part["body"]);
			
			// "A link to Google":http://google.com
			$part["body"] = preg_replace("~\"([^\"]*)\":(((ht|f)tps?://|mailto:)[^\s]*)~", "[$2|$1]", $part["body"]);

			// internal labelled links
			$part["body"] = preg_replace("~\"([^\"]+)\":([^\s]+)~", "(($2|$1))", $part["body"]);
			
			// html links
			$part["body"] = preg_replace("~<a href=\"([^\"]*)\">([^<]*)</a>~", "[$1|$2]", $part["body"]);

			// remove <br>
			$part["body"] = preg_replace("/<br(\s*\/)?>(\r?\n)?/", "\n", $part["body"]);

		 // manage lists
			$part["body"] = preg_replace("/\n \*/","\n**", $part["body"]);
			$part["body"] = preg_replace("/\n  \*/","\n***", $part["body"]);
		 
			// change <b>..</b>
			$part["body"] = preg_replace("/ _([^_]*)_ /", " ===$1=== ", $part["body"]);
			$part["body"] = preg_replace("/\*\*([^\*\n]+)\*\*/", "__$1__", $part["body"]);
			$part["body"] = preg_replace("~<b>([^<]*)</b>~", "__$1__", $part["body"]);
			$part["body"] = preg_replace("/\*([^\*\n]+)\*/", "''$1''", $part["body"]);
			$part["body"] = preg_replace("~<i>([^<]*)</i>~", "''$1''", $part["body"]);
			
			// change <hr>
			$part["body"] = preg_replace("/<hr(\s*\/)?>(\r?\n)?/", "---\n", $part["body"]);

     // manage formatting
			$part["body"] = preg_replace("/^(\n*)([^\n]+)(\n\n) /","!$2$3", $part["body"]);
			$part["body"] = preg_replace("/(\n\n)([^\n]{1,200})(\n\n) /","$1!!$2$3", $part["body"]);
			$part["body"] = preg_replace("/\n +/","\n", $part["body"]);

			$pagename = urldecode($part["pagename"]);

			$msg = '';

			if (isset($_REQUEST["remo"]) and $_REQUEST["remo"] == 'y') {
				$tikilib->remove_all_versions($pagename, '');
			}

			if ($tikilib->page_exists($pagename)) {
				if (isset($_REQUEST["crunch"]) and $_REQUEST["crunch"] == 'y') {
					$msg = '<b>' . tra('overwriting old page'). '</b>';
					$tikilib->update_page($pagename, $part["body"], tra('updated from structured text import'), 'System', '0.0.0.0', '');
				} else {
					$msg = '<b>' . tra('page not added (Exists)'). '</b>';
				}
			} else {
				$msg = tra('page created');
				$tikilib->create_page($pagename, 0, $part["body"], time(), tra('created from structured text import'), 'System', 0, '');
			}

			$aux["page"] = $pagename;
			$aux["ex"] = substr($part['body'],0,42);
			$aux["msg"] = $msg;
			$lines[] = $aux;
		}
	}

	$smarty->assign('lines', $lines);
	$smarty->assign('result', 'y');
}
ask_ticket('import-st');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-import_structuredtext.tpl');
$smarty->display("tiki.tpl");

?>
