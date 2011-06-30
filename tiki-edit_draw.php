<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
$section = "draw";
require_once ('tiki-setup.php');
require_once ('lib/svg-edit_tiki/draw.php');

$access->check_feature('feature_draw');

include_once ("categorize_list.php");
include_once ('tiki-section_options.php');

ask_ticket('draw');

//Obtain fileId, DO NOT LET ANYTHING OTHER THAN NUMBERS BY (for injection free code)
if (is_numeric($_REQUEST['fileId']) == false) $_REQUEST['fileId'] = 0; 
if (is_numeric($_REQUEST['galleryId']) == false) $_REQUEST['galleryId'] = 0;

$fileId = $_REQUEST['fileId'];
$galleryId = $_REQUEST['galleryId'];

$label = $_REQUEST['label'];
$index = $_REQUEST['index'];
$page = $_REQUEST['page'];

$smarty->assign( "page", $page );
$smarty->assign( "isFromPage", isset($page) );

$backLocation = (isset($page) ? "tiki-index.php?page=$page" : "tiki-list_file_gallery.php?galleryId=$galleryId");

$smarty->assign( "fileId", $fileId );
$smarty->assign( "galleryId", $galleryId );

$headerlib->add_jsfile("lib/svg-edit/embedapi.js");

if (
	isset($_REQUEST['label']) && 
	isset($_REQUEST['index']) &&
	isset($_REQUEST['page'])
) {
	$headerlib->add_jq_onready("	
		window.wikiTracking = {
			label: '$label',
			index: '$index',
			page: '$page',
			type: 'draw',
			content: ''
		};
	");
}

$headerlib->add_jq_onready("
	window.svgFileId = $fileId;
	var win = $(window);
	win
		.resize(function() {
			$('#svgedit')
				.height(win.height())
				.width(win.width());
		})
		.resize();
	
	$('body').css('overflow', 'hidden');
	
	window.svgCanvas = null;
	
	window.handleSvgDataUpdate = function(data, error) {
		if (error) {
			alert('error ' + error);
		} else {
			$.post('tiki-list_file_gallery.php', {
				fileId: window.svgFileId,
				galleryId: $galleryId,
				data: data,
				edit: true,
				file: window.svgFileId,
				edit_mode: 'y'
			}, function(o) {
				alert('".tr("Saved!")."');
			});
		}			
	}

	window.handleSvgDataNew = function(data, error) {
		if (error) {
			alert('error ' + error);
		} else {
			$.post('tiki-batch_upload_files.php', {
				batch_upload: 'svg',
				galleryId: $galleryId,
				name: 'New Svg Image',
				data: data
			}, function(id) {
				alert('".tr("Saved file id ' + id + '!")."');
				window.svgFileId = id;
				
				if (window.wikiTracking) {
					window.wikiTracking['params[id]'] = id;
					
					$.post('tiki-wikiplugin_edit.php', window.wikiTracking, function() {
						window.wikiTracking = null;
					});
				}
			});
		}			
	}
	
	window.loadSvg = function(svg) {
		window.svgCanvas.setSvgString(svg);
	};
	
	window.saveSvg = function() {
		window.svgCanvas.getSvgString()(window.svgFileId ? window.handleSvgDataUpdate : window.handleSvgDataNew);
	};
	
	$('#svgedit').load(function() {
		var frame = document.getElementById('svgedit');
		window.svgCanvas = new embedded_svg_edit(frame);
		
		// Hide main button, as we will be controlling new/load/save etc from the host document
		var doc;
		doc = frame.contentDocument;
		if (!doc)
		{
			doc = frame.contentWindow.document;
		}
		
		var mainButton = $(doc).find('#main_button').hide();
		
		$('#tiki-draw_save')
			//.prependTo($(doc).find('#editor_panel'))
			.click(function() {
				window.saveSvg();
			});
		
		var thisDoc = document;
		
		$('#svg-editHeaderRight')
			//.appendTo($(doc).find('#tools_top'))
			.click(function() {
				thisDoc.location = '$backLocation';
			});
		
		if (window.svgFileId) {
			$('<div />').load('tiki-download_file.php?fileId=$fileId&r=' + Math.floor(Math.random() * 9999999999), function(o) {
				window.loadSvg(o);
			});
		}
	});
");
// Display the template
$smarty->assign('mid', 'tiki-edit_draw.tpl');
// use tiki_full to include include CSS and JavaScript
$smarty->display("tiki_full.tpl");

