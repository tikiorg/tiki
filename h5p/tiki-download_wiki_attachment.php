<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$force_no_compression = true;
require_once ('tiki-setup.php');

if (!empty($_REQUEST['attId'])) {
	$info = $tikilib->get_wiki_attachment($_REQUEST['attId']);
}
if (empty($info)) {
	$smarty->assign('msg', tra('Incorrect param').' attid');
	$smarty->display('error.tpl');
	die;
}

$perms = Perms::get(array( 'type' => 'wiki page', 'object' => $info['page'] ));
if ((!$perms->view || !$perms->wiki_view_attachments) && !$perms->wiki_admin_attachments) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

$tikilib->add_wiki_attachment_hit($_REQUEST["attId"]);

if ( empty($info['filetype']) || $info['filetype'] == 'application/x-octetstream' || $info['filetype'] == 'application/octet-stream' ) {
	$mimelib = TikiLib::lib('mime');
	$info['filetype'] = $mimelib->from_filename($info['filename']);
}
$type = $info["filetype"];
$file = $info["filename"];
$content = $info["data"];

session_write_close();
//print("File:$file<br />");
//die;
header("Content-type: $type");

// This used to say "header("Content-Disposition: attachment; filename=\"$file\"");"
// which made everything try to download instead of the browser picking what to do.
// If people want the old behaviour, the right thing is probably to add an argument to 
// tiki-download_wiki_attachment.php, such as "&download", and then code in the attachment
// plugin, but the old behaviour really seems like The Wrong Thing to me.  -rlpowell
// --> added a choice for compatibility issue
if (isset($_REQUEST['download']))
	header("Content-Disposition: attachment; filename=\"$file\"");
else
	header("Content-Disposition: filename=\"$file\"");

// No reason to make everything uncacheable
header("Expires: ".date("D, d M Y H:i:s T", time()+86400));
// Added March04 Damian, Akira123 reported test
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: public");

if ($info["path"]) {
	header("Content-Length: ". filesize($prefs['w_use_dir'].$info["path"]));
	readfile($prefs['w_use_dir'] . $info["path"]);
} else {
	header("Content-Length: ". $info[ "filesize" ]);
	echo "$content";
}
