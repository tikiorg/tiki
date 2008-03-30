<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-download_wiki_attachment.php,v 1.18 2007-10-12 07:55:26 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$force_no_compression = true;
require_once ('tiki-setup.php');

if ((isset($_REQUEST['page']) && !$tikilib->user_has_perm_on_object($user, $_REQUEST['page'], 'wiki page', 'tiki_p_wiki_view_attachments') && !$tikilib->user_has_perm_on_object($user, $_REQUEST['page'], 'wiki page', 'tiki_p_wiki_admin_attachments'))
  || (!isset($_REQUEST['page']) && $tiki_p_wiki_view_attachments != 'y' && $tiki_p_wiki_admin_attachments != 'y')) {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["attId"])) {
	die;
}

$info = $tikilib->get_wiki_attachment($_REQUEST["attId"]);
$tikilib->add_wiki_attachment_hit($_REQUEST["attId"]);

$type = &$info["filetype"];
$file = &$info["filename"];
$content = &$info["data"];

session_write_close();
//print("File:$file<br />");
//die;
header ("Content-type: $type");

// This used to say "header ("Content-Disposition: attachment; filename=\"$file\"");"
// which made everything try to download instead of the browser picking what to do.
// If people want the old behaviour, the right thing is probably to add an argument to 
// tiki-download_wiki_attachment.php, such as "&download", and then code in the attachment
// plugin, but the old behaviour really seems like The Wrong Thing to me.  -rlpowell
// --> added a choice for compatibility issue
if (isset($_REQUEST['download']))
	header ("Content-Disposition: attachment; filename=\"$file\"");
else
	header ("Content-Disposition: filename=\"$file\"");

// Added March04 Damian, Akira123 reported test
header ("Expires: 0");
header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header ("Pragma: public");

if ($info["path"]) {
	header("Content-Length: ". filesize( $prefs['w_use_dir'].$info["path"] ) );
	readfile ($prefs['w_use_dir'] . $info["path"]);
} else {
	header("Content-Length: ". $info[ "filesize" ] );
	echo "$content";
}

?>
