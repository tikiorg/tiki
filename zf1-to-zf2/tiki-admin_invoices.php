<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
$trklib = TikiLib::lib('trk');

$access->check_feature('feature_invoice');
$access->check_permission('tiki_p_admin');

//check if profile is created
if ($trklib->get_tracker_by_name("Invoice Items") < 1) {
	$smarty->assign('msg', tra('You need to apply the "Invoice" profile'));
	$smarty->display("error.tpl");
	die;
}


// Display the template
$smarty->assign('mid', 'tiki-admin_invoices.tpl');
$smarty->display("tiki.tpl");
