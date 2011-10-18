<?php
require_once('tiki-setup.php');

if (isset($_REQUEST['parse'])) {
	print_r(
		UniversalReports_Builder::load('Logs')->setValuesFromRequest($_REQUEST['values'])->outputArray()
	);
	die;
}

if (isset($_REQUEST['load'])) {
	echo json_encode(UniversalReports_Builder::load($_REQUEST['load'])->input);
	die;
}

global $headerlib, $smarty;

$headerlib->add_jsfile( 'lib/core/UniversalReports/Builder.js' );
$headerlib->add_jsfile( 'lib/core/UniversalReports/Parser.js' );

$headerlib->add_jq_onready("
	$('#universalReportsType')
		.change(function() {
			$('#universalReportsEditor').html('');
			if ($(this).val()) {
				$.getJSON('tiki-edit_universal_reports.php?',{load: $(this).val()}, function(data) {
					$('#universalReportsEditor').universalReportsBuilder({
						definition: data
					});
				});
			}
		})
		.change();
	
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
$smarty->assign('definitions', UniversalReports_Builder::listDefinitions());
$smarty->assign('mid', 'tiki-edit_universal_reports.tpl');
$smarty->display("tiki.tpl");