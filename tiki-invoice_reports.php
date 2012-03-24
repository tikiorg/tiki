<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-list_invoices.php 40258 2012-03-20 13:59:58Z robertplummer $

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
$smarty->assign('mid', 'tiki-invoice_reports.tpl');
$smarty->display("tiki.tpl");
