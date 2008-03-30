<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_messages.php,v 1.1.2.1 2008-03-16 17:43:14 luciash Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.

// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($_REQUEST["messagesprefs"])) {
	ask_ticket('admin-inc-messages');
	simple_set_value('messu_mailbox_size');
	simple_set_value('messu_archive_size');
	simple_set_value('messu_sent_size');
	simple_set_toggle('allowmsg_is_optional');
	simple_set_toggle('allowmsg_by_default');
}

?>
