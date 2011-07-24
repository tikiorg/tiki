<?php
require_once('tiki-setup.php');
require_once('lib/profilelib/installlib.php');
require_once('lib/profilelib/profilelib.php');
require_once('lib/trackers/trackerquerylib.php');

global $tikilib, $trkqrylib;

$access->check_feature('feature_invoice');
$access->check_permission('tiki_p_admin');

//check if profile is created
$installer = new Tiki_Profile_Installer();
$profile = Tiki_Profile::fromNames( "profiles.tiki.org","Invoice" );
if (!$installer->isInstalled( $profile )) {
	$smarty->assign('msg', tra('You need to apply the "Invoice" profile'));
	$smarty->display("error.tpl");
	die;
}

$headerlib->add_jq_onready("
	$('.ClientName').each(function(i) {
		$(this)
			.click(function() {
				$('.ClientDetails').eq(i).toggle('fast');
			})
			.css('cursor', 'pointer');
	});
");

$smarty->assign("Clients", $trkqrylib->tracker_query_by_names("Invoice Clients"));
$smarty->assign("Settings", end($trkqrylib->tracker_query_by_names("Invoice Settings")));

// Display the template
$smarty->assign('mid', 'tiki-list_invoice_clients.tpl');
$smarty->display("tiki.tpl");