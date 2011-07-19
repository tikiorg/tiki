<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
$section = "draw";
require_once ('tiki-setup.php');
require_once ('lib/svg-edit_tiki/draw.php');
include_once ('lib/filegals/filegallib.php');

$access->check_feature('feature_draw');
$access->check_feature('feature_file_galleries');

include_once ("categorize_list.php");
include_once ('tiki-section_options.php');

ask_ticket('draw');

$fileInfo = $filegallib->get_file_info( $_REQUEST['fileId'] );
$gal_info = $filegallib->get_file_gallery( $_REQUEST['galleryId'] );

$globalperms = Perms::get( array( 'type' => 'file galleries', 'object' => $fileInfo['galleryId'] ) );

//check permissions
if (!($globalperms->admin_file_galleries == 'y' || $globalperms->view_file_gallery == 'y')) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to edit this file"));
	$smarty->display("error.tpl");
	die;
}

if (!empty($_REQUEST['name']) || !empty($fileInfo['name'])) {
	$_REQUEST['name'] = (!empty($_REQUEST['name']) ? $_REQUEST['name'] : $fileInfo['name']);
} else {
	$_REQUEST['name'] = "New Svg Image";
}

$_REQUEST['name'] = htmlspecialchars(str_replace(".svg", "", $_REQUEST['name']));

//Upload to file gallery
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['data'])) {
	$_REQUEST["galleryId"] = (int)$_REQUEST["galleryId"];
	$_REQUEST["fileId"] = (int)$_REQUEST["fileId"];
	$_REQUEST['description'] = htmlspecialchars(isset($_REQUEST['description']) ? $_REQUEST['description'] : $_REQUEST['name']);
	
	include_once ('lib/mime/mimetypes.php');
	$type = $mimetypes["svg"];
	$fileId = '';
	if (empty($_REQUEST["fileId"]) == false) {
		//existing file
		$fileId = $filegallib->save_archive($_REQUEST["fileId"], $fileInfo['galleryId'], 0, $_REQUEST['name'], $fileInfo['description'], $_REQUEST['name'].".svg", $_REQUEST['data'], strlen($_REQUEST['data']), $type, $fileInfo['user'], null, null, $user, date());
	} else {
		//new file
		$fileId = $filegallib->insert_file($_REQUEST["galleryId"], $_REQUEST['name'], $_REQUEST['description'], $file, $_REQUEST['data'], strlen($_REQUEST['data']), $type, $user, date());
	}
	
	echo $fileId;
	die;
}

$smarty->assign( "data", $fileInfo["data"] );
//Obtain fileId, DO NOT LET ANYTHING OTHER THAN NUMBERS BY (for injection free code)
if (is_numeric($_REQUEST['fileId']) == false) $_REQUEST['fileId'] = 0; 
if (is_numeric($_REQUEST['galleryId']) == false) $_REQUEST['galleryId'] = 0;

$fileId = htmlspecialchars($_REQUEST['fileId']);
$galleryId = htmlspecialchars($_REQUEST['galleryId']);
$name = htmlspecialchars($_REQUEST['name']);

$index = htmlspecialchars($_REQUEST['index']);
$page = htmlspecialchars($_REQUEST['page']);
$label = htmlspecialchars($_REQUEST['label']);
$width = htmlspecialchars($_REQUEST['width']);
$height = htmlspecialchars($_REQUEST['height']);

$smarty->assign( "page", $page );
$smarty->assign( "isFromPage", isset($page) );

$backLocation = ($page ? "tiki-index.php?page=$page" : "tiki-list_file_gallery.php?galleryId=$galleryId");

$smarty->assign( "fileId", $fileId );
$smarty->assign( "galleryId", $galleryId );
$smarty->assign( "width", $width );
$smarty->assign( "height", $width );
$smarty->assign( "name", $name);

$headerlib->add_jsfile("lib/svg-edit/embedapi.js");

if (
	isset($_REQUEST['index']) &&
	isset($_REQUEST['page']) && 
	isset($_REQUEST['label'])
) {
	$headerlib->add_jq_onready("	
		window.wikiTracking = {
			index: '$index',
			page: '$page',
			label: '$label',
			type: 'draw',
			content: ''
		};
	");
}

$headerlib->add_jq_onready("
	var win = $(window);
	
	$('body').append('<style>' +
		'#fullscreen {' +
			'left: 0px;' +
			'top: 0px;' +
			'position: absolute;' +
			'z-index: 9999;' +
			'background-color: white;' +
			'text-align: center;' +
		'}' +
		'#fullscreen #tiki_draw_editor{' +
			'width: inherit ! important;' +
			'height: inherit ! important;' +
		'}' +
		'#fullscreen #svg-menu{' +
			'position: absolute;' +
			'z-index: 99991;' +
		'}' +
		'#fullscreen iframe {' +
			'width: 100%;' +
			'border: none ! important;' +
		'}' +
		'#tiki_draw iframe {' +
			'width: 100%;' +
			'height: ' + (win.height() * 0.8) + 'px;' +
			'border: none ! important;' +
		'#tiki_draw_editor iframe {' +
			'border: none ! important;' +
		'}' +
		'.full_screen_body {' +
			'overflow: hidden;' +
		'}' +
	'</style>');
	
	$('#tiki-draw_fullscreen').click(function() {
		window.saveSvg();
		var tiki_draw = $('#tiki_draw');
		var fullscreen = $('#fullscreen');
		var menuHeight = $('#svg-menu').height();
		
		if (fullscreen.length == 0) {
			$('body').addClass('full_screen_body');
			fullscreen = $('<div />').attr('id', 'fullscreen')
				.html(tiki_draw.find('#tiki_draw_editor'))
				.prependTo('body');
			
			var fullscreenIframe = fullscreen.find('iframe');
			
			win
				.resize(function() {
					fullscreen
						.height(win.height())
						.width(win.width());
						
					fullscreenIframe.height((fullscreen.height() - menuHeight));
				})
				.resize() //we do it double here to make sure it is all resized right
				.resize();
				
		} else {
			tiki_draw.append(fullscreen.find('#tiki_draw_editor'));
			win.unbind('resize');
			fullscreen.remove();
			$('body').removeClass('full_screen_body');
		}
		
		return false;
	});
	
	window.svgCanvas = null;
	
	window.handleSvgDataUpdate = function(data, error) {
		if (error) {
			alert('error ' + error);
		} else {
			window.handleSvgDataNew(data, error);
		}			
	}

	window.handleSvgDataNew = function(data, error) {
		if (error) {
			alert('error ' + error);
		} else {
			$.modal('".tra("Saving...")."');
			$.post('tiki-edit_draw.php', {
				galleryId: $('#svg_gallery_id').val(),
				fileId: $('#svg_file_id').val(),
				name: $('#svg_file_name').val(),
				data: data
			}, function(id) {
				if (id) {
					$.modal('".tr("Saved file id:")." ' + id + '!');
					$('#svg_file_id').val(id);
				} else {
					$.modal('".tr("Saved file id")." ' + $('#svg_file_id').val() + '!');
				}
				
				if (window.wikiTracking) {
					window.wikiTracking['params[id]'] = $('#svg_file_id').val();
					window.wikiTracking['params[width]'] =  $('#svg_file_width').val();
					window.wikiTracking['params[height]'] =  $('#svg_file_height').val();
					
					$.modal('".tr("Updating Wiki Page")."');
					$.post('tiki-wikiplugin_edit.php', window.wikiTracking, function() {
						$.modal();
					});
				} else {
					$.modal();
				}
			});
		}			
	}
	
	window.saveSvg = function() {
		window.svgCanvas.getSvgString()(window.handleSvgDataNew);
		window.svgWindow.svgCanvas.undoMgr.resetUndoStack();
	};
	
	$('#svgedit').load(function() {
		var frame = window.svgFrame = document.getElementById('svgedit');
		window.svgCanvas = new embedded_svg_edit(frame);
		window.svgWindow = frame.contentWindow;
		
		// Hide main button, as we will be controlling new/load/save etc from the host document
		var doc;
		doc = frame.contentDocument;
		if (!doc) {
			doc = frame.contentWindow.document;
		}
		
		var mainButton = $(doc).find('#main_button').hide();
		
		$('#tiki-draw_save')
			.click(function() {
				window.saveSvg();
			});
		
		var thisDoc = document;
		
		$('#tiki-draw_back')
			.click(function() {
				thisDoc.location = '$backLocation';
			});
		
		if ($('#svg_file_id').val()) {
			window.svgCanvas.setSvgString($('#svg-data').html());
		}
		
		window.svgWindow.onbeforeunload = function() {};
		window.onbeforeunload = function() {
			if ( window.svgWindow.svgCanvas.undoMgr.getUndoStackSize() > 1 ) {
				return '".tra("There are unsaved changes, leave page?")."';
			}
		};
	});
	
	$('#tiki-draw_rename').click(function() {
		var name = $('#svg_file_name').val();
		var newName = prompt('".tra("Enter new name")."', name);
		if (newName) {
			if (newName != name) {
				$('#svg_file_name').val(newName);
				window.saveSvg();
			}
		}
		return false;
	});
");
// Display the template
$smarty->assign('mid', 'tiki-edit_draw.tpl');
// use tiki_full to include include CSS and JavaScript
$smarty->display("tiki.tpl");
