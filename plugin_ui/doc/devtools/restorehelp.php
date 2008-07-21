#!/usr/bin/php4 
<?php

include_once("lib/init/initlib.php");
require_once("db/tiki-db.php");
require_once("lib/tikilib.php");
require_once("lib/categories/categlib.php");
require_once("lib/structures/structlib.php");

function tra($s) { return $s; }

$tikilib = new TikiLib($dbTiki);

$categId = '113';
$structId = '160';

$pages = split("\n",`grep -r '{\$helpurl}' templates | sed -e "s/^.*helpurl}\([^\"']*\)[\"'].*$/\\1/" | sort | uniq`);
$afterid = NULL;
foreach ($pages as $p) {
	if ($p) {
		echo $p;
		if (!$tikilib->page_exists($p)) {
			$tikilib->create_page($p, 0, 'to do', time(), 'automatic import from RestoreHelp script', 'RestoreHelp', 'RestoreHelp', 'to do');
			echo ', not found, is created,';
		}
		//$categlib->categorize_page($p,$categId);
		echo ' is re-categorized';
		
		if ($structlib->page_is_in_structure($p)) {
			$alls = $structlib->get_page_structures($p,'Help');
			if (count($alls)) {
				echo ". It is in Help structure with Id ";
				echo $alls[0]["req_page_ref_id"];
				$tikilib->remove_from_structure($alls[0]["req_page_ref_id"]);
				echo ": erased and";
			}
		}
		$afterid = $structlib->s_create_page($structId,$afterid,$p);
		echo " created with id : $afterid";
		echo "\n";
	}
}
?>
