<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
$section = "docs";
require_once ('tiki-setup.php');
include_once ('lib/filegals/filegallib.php');

$access->check_feature('feature_docs');
$access->check_feature('feature_file_galleries');

include_once ("categorize_list.php");
include_once ('tiki-section_options.php');

ask_ticket('docs');

$_REQUEST['fileId'] = (int)$_REQUEST['fileId'];

$fileInfo = $filegallib->get_file_info( $_REQUEST['fileId'] );
$gal_info = $filegallib->get_file_gallery( $_REQUEST['galleryId'] );

$globalperms = Perms::get( array( 'type' => 'file galleries', 'object' => $fileInfo['galleryId'] ) );

//check permissions
if (!($globalperms->admin_file_galleries == 'y' || $globalperms->view_file_gallery == 'y')) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to view/edit this file"));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign( "page", $page );
$smarty->assign( "isFromPage", isset($page) );
$smarty->assign( "fileId", $_REQUEST['fileId']);

$headerlib->add_jsfile("lib/webodf/webodf.js");
$headerlib->add_cssfile("lib/webodf/webodf.css");

$headerlib->add_jq_onready("
	var odfelement = document.getElementById('tiki_doc'),
    odfcanvas = new odf.OdfCanvas(odfelement);
	odfcanvas.load('tiki-download_file.php?fileId=' + $('#fileId').val());
");
// Display the template
$smarty->assign('mid', 'tiki-edit_docs.tpl');
// use tiki_full to include include CSS and JavaScript
$smarty->display("tiki.tpl");
