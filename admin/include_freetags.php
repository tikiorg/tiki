<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (basename($_SERVER['SCRIPT_NAME']) === basename(__FILE__)) {
	die('This script may only be included.');
}

require_once ('tiki-setup.php');

if (isset($_REQUEST["cleanup"]) && $access->ticketMatch()) {
	$freetaglib = TikiLib::lib('freetag');
	$result = $freetaglib->cleanup_tags();
	if ($result) {
		Feedback::success(tr('Tags successfully cleaned up.'), 'session');
	} else {
		Feedback::error(tr('Tag cleanup failed.'), 'session');
	}
}