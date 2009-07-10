<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-listpages.php,v 1.54.2.9 2008-03-10 20:15:22 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'wiki page';
require_once('tiki-setup.php');

if($prefs['feature_wiki'] != 'y') {
    $smarty->assign('msg', tra('This feature is disabled').': feature_wiki');
    $smarty->display('error.tpl');
    die;  
}
if($tiki_p_admin != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
    die;  
}

if (isset($_REQUEST['import'])) {
	if (is_uploaded_file($_FILES['zip']['tmp_name'])) {
		check_ticket('import_xml_zip');
		include_once('lib/wiki/xmllib.php');
		$xmllib = new XmlLib;
		$zipFile = $_FILES['zip']['tmp_name'];
		$config = array();
		if ($xmllib->import_pages($zipFile, $config)) {
			$smarty->assign('msg', tra('Operations executed successfully'));
		} else {
			$smarty->assign('error', $xmllib->get_error());
		}
	} else {
		$smarty->assign('error', tra('Error'));
	}
}
ask_ticket('import_xml_zip');
$smarty->assign('mid', 'tiki-import_xml_zip.tpl');
$smarty->display("tiki.tpl");

