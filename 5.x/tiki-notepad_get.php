<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/notepad/notepadlib.php');
$access->check_feature('feature_notepad');
$access->check_user($user);
$access->check_permission('tiki_p_notepad');
if (!isset($_REQUEST["noteId"])) {
	$smarty->assign('msg', tra("No note indicated"));
	$smarty->display("error.tpl");
	die;
}
if (isset($_REQUEST["save"])) {
	$disposition = "attachment";
} else {
	$disposition = "inline";
}
$info = $notepadlib->get_note($user, $_REQUEST["noteId"]);
header("Content-type: text/plain");
header("Content-Disposition: $disposition; filename=note_" . urlencode($user) . '_' . $_REQUEST["noteId"] . ".txt;");
echo $info['data'];
