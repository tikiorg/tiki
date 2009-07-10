<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_webmail.php,v 1.12 2007-10-12 07:55:24 nyloth Exp $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
include_once ("lib/webmail/htmlMimeMail.php");
if (isset($_REQUEST["webmail"])) {
	check_ticket('admin-inc-webmail');
	if (isset($_REQUEST["webmail_view_html"]) && $_REQUEST["webmail_view_html"] == "on") {
		$tikilib->set_preference("webmail_view_html", 'y');
	} else {
		$tikilib->set_preference("webmail_view_html", 'n');
	}
	$tikilib->set_preference('webmail_max_attachment', $_REQUEST["webmail_max_attachment"]);
	if (isset($_REQUEST["webmail_quick_flags"])) {
		$tikilib->set_preference('webmail_quick_flags', 'y');
	} else {
		$tikilib->set_preference('webmail_quick_flags', 'n');
	}
}
ask_ticket('admin-inc-webmail');
