<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: copyrights.php 33195 2011-03-02 17:43:40Z changi67 $

require_once ('tiki-setup.php');
$access->check_feature('feature_references');
$access->check_permission(array('tiki_p_edit_referencelib'), tra("Edit Library References"));

include_once ("lib/references/referenceslib.php");
global $dbTiki;
$referenceslib = new referencesLib;

$tiki_p_use_referencelib = $referenceslib->get_permission('tiki_p_use_referencelib');
$tiki_p_edit_referencelib = $referenceslib->get_permission('tiki_p_edit_referencelib');
if(isset($tiki_p_edit_referencelib) && $tiki_p_edit_referencelib=='y'){
	$edit_referencelib = 1;
}else{
	$edit_referencelib = 0;
}

if (!$edit_referencelib) {
	$smarty->assign('msg', tra("You do not have permissions to view this page."));
	$smarty->display("error.tpl");
	die;
}


$smarty->assign('page', $_REQUEST["page"]);
$page = $_REQUEST["page"];

$page_id = TikiLib::lib('tiki')->get_page_id_from_name($page);
$action = $_REQUEST['action'];
$ref_id = $_REQUEST['referenceId'];
$ref_biblio_code = $_REQUEST['ref_biblio_code'];
$ref_author = $_REQUEST['ref_author'];
$ref_title = $_REQUEST['ref_title'];
$ref_part = $_REQUEST['ref_part'];
$ref_uri = $_REQUEST['ref_uri'];
$ref_code = $_REQUEST['ref_code'];
$ref_publisher = $_REQUEST['ref_publisher'];
$ref_location = $_REQUEST['ref_location'];
$ref_year = $_REQUEST['ref_year'];
$ref_style = $_REQUEST['ref_style'];
$ref_template = $_REQUEST['ref_template'];

if (isset($_REQUEST['addreference'])) {
	$errors = array();
	
	if($ref_biblio_code==''){
		$errors[] = 'Please enter Biblio Code.';
	}

	$exists = $referenceslib->check_lib_existence($ref_biblio_code);
	if($exists > 0){
		$errors[] = 'This reference already exists.';
	}

	if (count($errors)<1) {
		$id = $referenceslib->add_lib_reference($ref_biblio_code, $ref_author, $ref_title, $ref_part, $ref_uri, $ref_code, $ref_year, $ref_style, $ref_template, $ref_publisher, $ref_location);
	} else {
		foreach($errors as $error){
			$msg .= tra($error);
		}
		$access->display_error(basename(__FILE__), $msg);
	}
}

if (isset($_REQUEST['editreference'])) {

	$errors = array();

	if($ref_id==''){
		$errors[] = 'Reference not found.';
	}
	if($ref_biblio_code==''){
		$errors[] = 'Please enter Biblio Code.';
	}

	if (count($errors)<1) {
		$referenceslib->edit_libReference($ref_id, $ref_biblio_code, $ref_author, $ref_title, $ref_part, $ref_uri, $ref_code, $ref_year, $ref_style, $ref_template, $ref_publisher, $ref_location);
	} else {
		foreach($errors as $error){
			$msg .= tra($error);
		}
		$access->display_error(basename(__FILE__), $msg);
	}
}

if (isset($_REQUEST['action']) && isset($ref_id)) {
	if ($_REQUEST['action'] == 'delete') {
		$access->check_authenticity();
		$referenceslib->remove_libReference($ref_id);
	}
}

$references = $referenceslib->list_lib_references();
$smarty->assign('references', $references["data"]);

// Display the template
$smarty->assign('mid', 'references.tpl');
$smarty->display("tiki.tpl");
