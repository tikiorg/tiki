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
if (is_numeric($_REQUEST['galleryId']) == false) die;
	
$fileId = $_REQUEST['fileId'];
$galleryId = $_REQUEST['galleryId'];

$smarty->assign( "fileId", $fileId );
$smarty->assign( "galleryId", $galleryId );

$headerlib->add_jsfile("lib/svg-edit/embedapi.js");
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
		
		$('#main_button', doc).css('display', 'none');
		
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

