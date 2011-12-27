<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
global $headerlib, $smarty;

TikiLib::lib("sheet")->setup_jquery_sheet();

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

if (isset($_REQUEST['wikidata'])) {
	echo Report_Builder::load($_REQUEST['wikidata'])
		->setValuesFromRequest($_REQUEST['values'])
		->outputWikiData();
	die;
}

$headerlib->add_jsfile( 'lib/core/Report/Builder.js');

$headerlib->add_jq_onready("
	$('#reportType')
		.change(function() {
			$('#reportEditor').html('');
			if ($(this).val()) {
				$('#reportButtons').show();
				$.getJSON('tiki-edit_report.php?',{load: $(this).val()}, function(data) {
					$('#reportEditor').reportBuilder({
						definition: data
					});
				});
			} else {
				$('#reportButtons').hide();
			}
		})
		.change();
	
	$('#reportPreview').click(function() {
		$('#report').modal(tr('Loading...'));
		$.post('tiki-edit_report.php', {
			values: $('#reportEditor').serializeArray(),
			preview: $('#reportType').val()
		}, function(o) {
			var jS = $('#reportSheetPreview').getSheet();
			if (jS) {
				jS.openSheet(o);
			} else {
				$('#reportSheetPreview')
					.html($(o).attr('title', tr('Preview')))
					.sheet({
						buildSheet: true,
						editable: false
					});
			}
			
			$('#report').modal();
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
	
	$('#reportWikiData').click(function() {
		$.post('tiki-edit_report.php', {
			values: $('#reportEditor').serializeArray(),
			'wikidata': $('#reportType').val()
		}, function(o) {
			$('<pre />')
				.html(o)
				.dialog({
					modal: true,
					title: tr('Wiki Data Output For REPORT Plugin') 
				});
			return;
			$('#reportWikiDataOutput').html(o);
		});
		
		return false;
	});
	
");

$smarty->assign('definitions', Report_Builder::listDefinitions());
$smarty->assign('mid', 'tiki-edit_report.tpl');
$smarty->display("tiki.tpl");
