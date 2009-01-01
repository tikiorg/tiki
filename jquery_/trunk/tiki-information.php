<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-confirm_user_email.php,v 1.3 2007-10-12 07:55:25 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
if (isset($_REQUEST['msg'])) {
	$smarty->assign('msg', $_REQUEST['msg']);
}
$smarty->assign('mid', 'tiki-information.tpl');
$smarty->display("tiki.tpl");
