<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 12/02/2004
* @copyright (C) 2005 the Tiki community
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
include_once("lib/init/initlib.php");
require_once ('tiki-setup_base.php');
include_once('lib/tikidav/docbooklib.php');

if ($tiki_p_wiki_view_attachments != 'y' && $tiki_p_wiki_admin_attachments != 'y') {
	die;
}
if (!isset($_REQUEST["attName"])) {
	die;
}
global $dbTiki;
$docbook = new DocBookLib($dbTiki);
$info = $docbook->get_wiki_attachmentByName($_REQUEST["attName"]);

$w_use_db = $tikilib->get_preference('w_use_db', 'y');
$w_use_dir = $tikilib->get_preference('w_use_dir', '');

$tikilib->add_wiki_attachment_hit($info["attId"]);

$type = &$info["filetype"];
$file = &$info["filename"];
$content = &$info["data"];

header ("Content-type: $type");
header ("Content-Disposition: inline; filename=\"$file\"");

header ("Expires: 0");
header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header ("Pragma: public");

if ($info["path"]) {
	readfile ($w_use_dir . $info["path"]);
} else {
	echo "$content";
}

?>
