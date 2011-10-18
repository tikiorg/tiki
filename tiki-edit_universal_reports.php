<?php
require_once('tiki-setup.php');

if (isset($_REQUEST['parse'])) {
	print_r(
		UniversalReports_Builder::load('Tracker')->setValues(
			TikiFilter_PrepareInput::delimiter('_')->prepare(
				TikiFilter_PrepareInput::delimiter('_')->flatten(
					UniversalReports_Builder::load('Tracker')
						->setValuesFromRequest($_REQUEST['values'])
						->values
				)
			)
		)->outputArray()
	);
	die;
}

global $headerlib, $smarty;

$headerlib->add_jsfile( 'lib/core/UniversalReports/Builder.js' );
$headerlib->add_jsfile( 'lib/core/UniversalReports/Parser.js' );

$headerlib->add_jq_onready("
	$('#universalReportsEditor')
		.universalReportsBuilder({
			definition: ".json_encode(UniversalReports_Builder::load('Tracker')->input)."
		});
	
	$('#universalReportsUpdate').click(function() {
		$.post('tiki-edit_universal_reports.php', {
			values: $('#universalReportsEditor').serializeArray(),
			parse: true
		}, function(o) {
			$('#universalReportsDebug').text(o);
		});
		
		return false;
	});
");

$smarty->assign('mid', 'tiki_edit_universal_reports.tpl');
$smarty->display("tiki.tpl");