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
if ( isset($_REQUEST['fileId']) && is_numeric($_REQUEST['fileId']) ) {
	$fileId = $_REQUEST['fileId'];
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
	
	var svgCanvas = null;

	function init_embed() {
		var frame = document.getElementById('svgedit');
		svgCanvas = new embedded_svg_edit(frame);
		
		// Hide main button, as we will be controlling new/load/save etc from the host document
		var doc;
		doc = frame.contentDocument;
		if (!doc)
		{
			doc = frame.contentWindow.document;
		}
		
		var mainButton = doc.getElementById('main_button');
		mainButton.style.display = 'none';			
	}
	
	function handleSvgData(data, error) {
		if (error) {
			alert('error ' + error);
		} else {
			alert('Congratulations. Your SVG string is back in the host page, do with it what you will ' + data);
		}			
	}
	
	function loadSvg(svgexample) {
		svgCanvas.setSvgString(svgexample);
	}
	
	function saveSvg() {			
		svgCanvas.getSvgString()(handleSvgData);
	}
	
	init_embed();
	
	if ('$fileId') {
		$('<div />').load('tiki-download_file.php?fileId=$fileId', function(o) {
			loadSvg(o);
		});
	}
");
// Display the template
$smarty->assign('mid', 'tiki-edit_draw.tpl');
// use tiki_full to include include CSS and JavaScript
$smarty->display("tiki_full.tpl");

