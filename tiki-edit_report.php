<?php
require_once('tiki-setup.php');
global $headerlib, $smarty;

TikiLib::lib("sheet")->setup_jquery_sheet();

$uB = new Report_Builder();

if (isset($_REQUEST['preview'])) {
	echo Report_Builder::load($_REQUEST['preview'])
		->setValuesFromRequest($_REQUEST['values'])
		->outputSheet();
	die;
}

if (isset($_REQUEST['load'])) {
	echo json_encode(Report_Builder::load($_REQUEST['load'])->input);
	die;
}

if (isset($_REQUEST['exportcsv'])) {
	echo Report_Builder::load($_REQUEST['exportcsv'])
		->setValuesFromRequest(json_decode(urldecode($_REQUEST['values'])))
		->outputCSV(true);
	die;
}

if (isset($_REQUEST['wikisyntax'])) {
	echo Report_Builder::load($_REQUEST['wikisyntax'])
		->setValuesFromRequest($_REQUEST['values'])
		->outputWiki();
	die;
}

$headerlib->add_jsfile( 'lib/core/Report/Builder.js');

$headerlib->add_jq_onready("
	$('#ReportType')
		.change(function() {
			$('#ReportEditor').html('');
			if ($(this).val()) {
				$.getJSON('tiki-edit_universal_reports.php?',{load: $(this).val()}, function(data) {
					$('#ReportEditor').ReportBuilder({
						definition: data
					});
				});
			}
		})
		.change();
	
	$('#ReportPreview').click(function() {
		$.post('tiki-edit_universal_reports.php', {
			values: $('#ReportEditor').serializeArray(),
			preview: $('#ReportType').val()
		}, function(o) {
			var jS = $('#ReportDebug').getSheet();
			if (jS) {
				jS.openSheet(o);
			} else {
				$('#ReportDebug')
					.html($(o).attr('title', tr('Preview')))
					.sheet({
						buildSheet: true,
						editable: false
					});
			}
		});
		
		return false;
	});
	
	$('#ReportExportCSV').click(function() {
		$.download('tiki-edit_universal_reports.php', { 
			values: JSON.stringify($('#ReportEditor').serializeArray()),
			exportcsv: $('#ReportType').val()
		}, 'post');
		
		return false;
	});
	
	$('#ReportWikiSyntax').click(function() {
		$.post('tiki-edit_universal_reports.php', {
			values: $('#ReportEditor').serializeArray(),
			'wikisyntax': $('#ReportType').val()
		}, function(o) {
			$('#ReportWikiSyntaxOutput').html(o);
		});
		
		return false;
	});
");

$smarty->assign('definitions', Report_Builder::listDefinitions());
$smarty->assign('mid', 'tiki-edit_report.tpl');
$smarty->display("tiki.tpl");
