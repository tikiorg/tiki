#!/usr/bin/php4 
<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// See http://doc.tiki.org/Restore+Help+Pages

include_once("lib/init/initlib.php");
include_once("lib/tikilib.php");
require_once("db/tiki-db.php");
$tikilib = TikiLib::lib('tiki');
$categlib = TikiLib::lib('categ');
$structlib = TikiLib::lib('struct');

/**
 * @param $s
 * @return mixed
 */
function tra($s)
{ 
	return $s; 
}

$tikilib = new TikiLib;

$structId = '160';

$pages = explode("\n", `grep -r '{\$helpurl}' templates | sed -e "s/^.*helpurl}\([^\"']*\)[\"'].*$/\\1/" | sort | uniq`);
$afterid = NULL;
foreach ($pages as $p) {
	if ($p) {
		echo $p;
		if (!$tikilib->page_exists($p)) {
			$tikilib->create_page($p, 0, 'to do', time(), 'automatic import from RestoreHelp script', 'RestoreHelp', 'RestoreHelp', 'to do');
			echo ', not found, is created,';
		}
		
		if ($structlib->page_is_in_structure($p)) {
			$alls = $structlib->get_page_structures($p, 'Help');
			if (count($alls)) {
				echo ". It is in Help structure with Id ";
				echo $alls[0]["req_page_ref_id"];
				$tikilib->remove_from_structure($alls[0]["req_page_ref_id"]);
				echo ": erased and";
			}
		}
		$afterid = $structlib->s_create_page($structId, $afterid, $p);
		echo " created with id : $afterid";
		echo "\n";
	}
}
