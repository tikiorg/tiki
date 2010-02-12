<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'wiki page';
require_once('tiki-setup.php');

$access->check_feature('feature_wiki');
$access->check_permission('tiki_p_admin');

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

