<?php
require_once('tiki-setup.php');

if (!empty($_REQUEST['preview'])) {
	print_r(
		UniversalReports_Builder::load($_REQUEST['preview'])
			->setValuesFromRequest($_REQUEST['values'])
			->outputArray()
	);
	die;
}

if (!empty($_REQUEST['load'])) {
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
	
	$('#universalReportsPreview').click(function() {
		$.post('tiki-edit_universal_reports.php', {
			values: $('#universalReportsEditor').serializeArray(),
			preview: $('#universalReportsType').val()
		}, function(o) {
			$('#universalReportsDebug').text(o);
		});
		
		return false;
	});
");
$smarty->assign('definitions', UniversalReports_Builder::listDefinitions());
$smarty->assign('mid', 'tiki-edit_universal_reports.tpl');
$smarty->display("tiki.tpl");