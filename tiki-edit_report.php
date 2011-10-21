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
	$('#reportType')
		.change(function() {
			$('#reportEditor').html('');
			if ($(this).val()) {
				$.getJSON('tiki-edit_report.php?',{load: $(this).val()}, function(data) {
					$('#reportEditor').reportBuilder({
						definition: data
					});
				});
			}
		})
		.change();
	
	$('#reportPreview').click(function() {
		$.post('tiki-edit_report.php', {
			values: $('#reportEditor').serializeArray(),
			preview: $('#reportType').val()
		}, function(o) {
			var jS = $('#reportDebug').getSheet();
			if (jS) {
				jS.openSheet(o);
			} else {
				$('#reportDebug')
					.html($(o).attr('title', tr('Preview')))
					.sheet({
						buildSheet: true,
						editable: false
					});
			}
		});
		
		return false;
	});
	
	$('#reportExportCSV').click(function() {
		$.download('tiki-edit_report.php', { 
			values: JSON.stringify($('#reportEditor').serializeArray()),
			exportcsv: $('#reportType').val()
		}, 'post');
		
		return false;
	});
	
	$('#reportWikiSyntax').click(function() {
		$.post('tiki-edit_report.php', {
			values: $('#reportEditor').serializeArray(),
			'wikisyntax': $('#reportType').val()
		}, function(o) {
			$('#reportWikiSyntaxOutput').html(o);
		});
		
		return false;
	});
");

$smarty->assign('definitions', Report_Builder::listDefinitions());
$smarty->assign('mid', 'tiki-edit_report.tpl');
$smarty->display("tiki.tpl");
