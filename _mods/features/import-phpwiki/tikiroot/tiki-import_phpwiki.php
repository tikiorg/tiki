<?php

// $Header: /cvsroot/tikiwiki/_mods/features/import-phpwiki/tikiroot/tiki-import_phpwiki.php,v 1.1 2004-10-29 19:33:42 damosoft Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

if ($tiki_p_admin != 'y') {
    $smarty->assign('msg', tra("You dont have permission to use this feature"));

    $smarty->display("error.tpl");
    die;
}

require ("lib/webmail/mimeDecode.php");

function parse_output(&$obj, &$parts, $i) {
    if (!empty($obj->parts)) {
	$temp_max = count($obj->parts);
	for ($i = 0; $i < $temp_max; $i++)
	    parse_output($obj->parts[$i], $parts, $i);
    } else {
	$ctype = $obj->ctype_primary . '/' . $obj->ctype_secondary;

	switch ($ctype) {
	    case 'application/x-phpwiki':
		$aux["body"] = $obj->body;

		$ccc = $obj->headers["content-type"];
		$items = split(';', $ccc);

		foreach ($items as $item) {
		    $portions = split('=', $item);

		    if (isset($portions[0]) && isset($portions[1])) {
			$aux[trim($portions[0])] = trim($portions[1]);
		    }
		}

		$parts[] = $aux;
		break;

	    case 'application/x-tikiwiki':
		$aux["body"] = $obj->body;

		$ccc = $obj->headers["content-type"];
		$items = split(';', $ccc);

		foreach ($items as $item) {
		    $portions = split('=', $item);

		    if (isset($portions[0]) && isset($portions[1])) {
			$aux[trim($portions[0])] = trim($portions[1]);
		    }
		}

		$parts[] = $aux;
		break;
	}
    }
}

function compare_import_versions($a1, $a2) {
    return $a1["version"] - $a2["version"];
}

$smarty->assign('result', 'y');

if (isset($_REQUEST["import"])) {
	check_ticket('import-phpwiki');

    $path = $_REQUEST["path"];

    $h = opendir("$path/");
    $lines = array();

    while (false !== $file = readdir($h)) {
	if (is_file("$path/$file")) {
	    $fp = fopen("$path/$file", "r");

	    $full = fread($fp, filesize("$path/$file"));
	    //$full=htmlspecialchars($full);
	    fclose ($fp);
	    $params = array(
		    'input' => $full,
		    'crlf' => "\r\n",
		    'include_bodies' => TRUE,
		    'decode_headers' => TRUE,
		    'decode_bodies' => TRUE
		    );

	    $output = Mail_mimeDecode::decode($params);
	    parse_output($output, $parts, 0);
	    usort($parts, 'compare_import_versions');
	    $last_part = '';
	    $last_part_ver = 0;

	    foreach ($parts as $part) {
		if ($part["version"] > $last_part_ver) {
		    $last_part_ver = $part["version"];

		    $last_part = $part["body"];
		}

		if (isset($part["pagename"])) {
		    // PHPWiki footnotes get handled by refusing to
		    // convert [[...] at all; the latest tikilib.php
		    // will render [[foo] as [foo], no link.  So the
		    // (?<!\[) scattered here is to handle [[...] stuff.
		    // -rlpowell

		    // Fixing [description|URL] format of PHPWiki.
		    // -rlpowell
		    $part["body"] = preg_replace("/(?<!\[)\[([^\[\|\]]+)\|(http:[^\]\|\]]+)\]/",
			"[$2|$1]", $part["body"]);

		    // Parse the body replacing links to Tiki links
		    $part["body"] = preg_replace("/ (http:\/\/[^ ]+) /", " [$1] ", $part["body"]);

		    // I have great difficulty imagining why someone
		    // thought this was right.  It's not. It turns
		    // external links into image links that point
		    // nowhere. -rlpowell
		    //	$part["body"] = preg_replace("/\[(http:\/\/[^\]]+)\]/", "{img src=$1}", $part["body"]);

		    // The (?!http:) below should really include other URL
		    // forms, but that would get complicated very quickly.
		    // -rlpowell
		    $part["body"] = preg_replace("/(?<!\[)\[((?!http:|\[)[^\|\]]+)\]/", "(($1))", $part["body"]);
		    $part["body"] = preg_replace("/(?<!\[)\[((?!http:|\[)[^\|]+)\|([^\]]+)\]/", "(($2|$1))", $part["body"]);

		    // %%% makes a linebreak in PHPWiki. -rlpowell
		    $part["body"] = str_replace("%%%", "", $part["body"]);

		    // ! and !!! need to be switched, annoyingly enough.
		    // This is a bit non-trivial. -rlpowell
		    $part["body"] = preg_replace("/\n\!([^\!])/", "\n&!!!&$1", $part["body"]);
		    $part["body"] = preg_replace("/\n\!\!\!([^\!])/", "\n!$1", $part["body"]);
		    $part["body"] = str_replace("\n&!!!&", "\n!!!", $part["body"]);


		    $pagename = urldecode($part["pagename"]);
		    $version = urldecode($part["version"]);
		    $author = urldecode($part["author"]);
		    $lastmodified = $part["lastmodified"];

		    if (isset($part["description"])) {
			$description = $part["description"];
		    } else {
			$description = '';
		    }

		    $authorid = urldecode($part["author_id"]);

		    if (isset($part["hits"])) {
			$hits = urldecode($part["hits"]);
		    } else {
			$hits = 0;
		    }

		    $ex = substr($part["body"], 0, 25);
		    //print(strlen($part["body"]));
		    $msg = '';

		    if ($_REQUEST["remo"] == 'y') {
			$tikilib->remove_all_versions($pagename, '');
		    }

		    if ($tikilib->page_exists($pagename)) {
			if ($_REQUEST["crunch"] == 'n') {
			    $msg = '<b>' . tra('page not added (Exists)'). '</b>';
			} else {
			    $msg = '<b>' . tra('overwriting old page'). '</b>';

			    $tikilib->update_page($pagename, $part["body"],
				    tra('updated by the phpwiki import process'), $author,
				    $authorid, $description);
			}
		    } else {
			$msg = tra('page created');

			$tikilib->create_page($pagename, $hits, $part["body"], $lastmodified,
				tra('created from phpwiki import'), $author, $authorid, $description);
		    }

		    $aux["page"] = $pagename;
		    $aux["version"] = $version;
		    $aux["part"] = $ex;
		    $aux["msg"] = $msg;

		    $lines[] = $aux;
		}
	    }

	    unset ($parts);
	}
    }

    closedir ($h);
    $smarty->assign('lines', $lines);
    $smarty->assign('result', 'y');
}
ask_ticket('import-phpwiki');

$smarty->assign('mid', 'tiki-import_phpwiki.tpl');
$smarty->display("tiki.tpl");

?>
