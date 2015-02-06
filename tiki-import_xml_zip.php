<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'wiki page';
require_once('tiki-setup.php');

$access->check_feature('feature_wiki');
$access->check_permission('tiki_p_admin');
@ini_set('max_execution_time', 0); //will not work in safe_mode is on

if (isset($_REQUEST['import'])) {
	check_ticket('import_xml_zip');
	if (!empty($_REQUEST['local'])) {
		$zipFile = $_REQUEST['local'];
	} elseif (is_uploaded_file($_FILES['zip']['tmp_name'])) {
		$zipFile = $_FILES['zip']['tmp_name'];
	} else {
		$smarty->assign('error', tra('Error'));
		$zipFile = '';
	}
	if ($zipFile) {
		include_once('lib/wiki/xmllib.php');
		$xmllib = new XmlLib;
		$config = array();
		if ($xmllib->import_pages($zipFile, $config)) {
			$smarty->assign('msg', tra('Operations executed successfully'));
		} else {
			$smarty->assign('error', $xmllib->get_error());
		}
	}
}
ask_ticket('import_xml_zip');
$smarty->assign('mid', 'tiki-import_xml_zip.tpl');
$smarty->display("tiki.tpl");

