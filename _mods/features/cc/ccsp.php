<?php
require_once ('tiki-setup.php');
require_once ('lib/cc/cclib.php');

$thelist = $cclib->get_currencies();

header("Content-type: text/plain");

// var_dump($thelist);
foreach ($thelist['data'] as $id=>$l) {
	echo '"'.$l['owner_id'].'","'.$id.'","'.$l['cc_name'].'","'. addslashes(str_replace("\n","",trim($l['cc_description']))).'","'.$l['requires_approval']."\"\n";
}
?>
