<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-objectpermissions.php,v 1.25.2.2 2008-03-11 15:17:54 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once ("tiki-setup.php");

if (!isset(
	$_REQUEST['objectName']) || empty($_REQUEST['objectType']) || empty($_REQUEST['objectId'])) {
	$smarty->assign('msg', tra("Not enough information to display this page"));

	$smarty->display("error.tpl");
	die;
}

$comments_per_page = $prefs['wiki_comments_per_page'];
$thread_sort_mode = $prefs['wiki_comments_default_ordering'];
$comments_vars=Array('objectName', 'objectType', 'objectId');
$comments_prefix_var='wiki page:';
$comments_object_var='objectId';
$comments_show = 'y';
include_once('comments.php');

$smarty->assign('show_comzone', 'y');

require_once('TikiPageControls.php');
if( $controls = TikiPageControls::factory( $_REQUEST['objectType'], $_REQUEST['objectId'], $_REQUEST['objectName'] ) ) {
	$controls->setMode('comment');
	$controls->build();
	$smarty->assign('object_page_controls', $controls);
}

$smarty->assign('mid','tiki-view_comments.tpl');
$smarty->display("tiki.tpl");

?>
