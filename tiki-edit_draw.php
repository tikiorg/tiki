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
if (
		isset($_REQUEST['fileId']) && 
		is_numeric($_REQUEST['fileId']) &&
		isset($_REQUEST['galleryId']) && 
		is_numeric($_REQUEST['galleryId'])
	) {
	
	$fileId = $_REQUEST['fileId'];
	$galleryId = $_REQUEST['galleryId'];
	
	$smarty->assign( "fileId", $fileId );
	$smarty->assign( "galleryId", $galleryId );
	
} else {
	die;
}

$headerlib->add_jsfile("lib/svg-edit/embedapi.js");
$headerlib->add_jq_onready("
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

	window.init_embed = function() {
		var frame = document.getElementById('svgedit');
		window.svgCanvas = new embedded_svg_edit(frame);
		
		// Hide main button, as we will be controlling new/load/save etc from the host document
		var doc;
		doc = frame.contentDocument;
		if (!doc)
		{
			doc = frame.contentWindow.document;
		}
		
		var mainButton = doc.getElementById('main_button');
		mainButton.style.display = 'none';			
	};
	
	window.handleSvgData = function(data, error) {
		if (error) {
			alert('error ' + error);
		} else {
			$('#file').val(data);
			$('#upform').submit();
		}			
	}
	
	window.loadSvg = function(svg) {
		window.svgCanvas.setSvgString(svg);
		$('#file').val(svg);
	};
	
	window.saveSvg = function() {
		window.svgCanvas.getSvgString()(window.handleSvgData);
	};
	
	window.init_embed();
	
	if ('$fileId') {
		$('<div />').load('tiki-download_file.php?fileId=$fileId', function(o) {
			window.loadSvg(o);
		});
	}
");
// Display the template
$smarty->assign('mid', 'tiki-edit_draw.tpl');
// use tiki_full to include include CSS and JavaScript
$smarty->display("tiki_full.tpl");

