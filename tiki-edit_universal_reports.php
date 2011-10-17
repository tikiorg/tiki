<?php
require_once('tiki-setup.php');

if (isset($_REQUEST['parse'])) {
	unset($_REQUEST['parse']);
	print_r( UniversalReports_Parser::load('Tracker')->apply($_REQUEST) );
	die;
}

global $headerlib, $smarty;

$headerlib->add_jsfile( 'lib/core/UniversalReports/Builder.js' );
$headerlib->add_jsfile( 'lib/core/UniversalReports/Parser.js' );

$headerlib->add_jq_onready("
	$('#universalReportsEditor')
		.universalReportsBuilder({
			definition: ".json_encode(UniversalReports_Builder::load('Tracker')->definition)."
		});
	
	$('#universalReportsUpdate').click(function() {
		$.post('tiki-edit_universal_reports.php?parse',$('#universalReportsEditor').universalReportsParser(), function(o) {
			$('#universalReportsDebug').text(o);
		});
		
		return false;
	});
");

$smarty->assign('mid', 'tiki_edit_universal_reports.tpl');
$smarty->display("tiki.tpl");