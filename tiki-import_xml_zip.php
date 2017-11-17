<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
	if (! empty($_REQUEST['local'])) {
		$zipFile = $_REQUEST['local'];
		$path = pathinfo($_REQUEST['local']);
		$filename = $path['basename'];
	} elseif (is_uploaded_file($_FILES['zip']['tmp_name'])) {
		$zipFile = $_FILES['zip']['tmp_name'];
		$filename = $_FILES['zip']['name'];
	} else {
		$error = tra('Unable to locate import file.');
		$zipFile = '';
	}
	if ($zipFile) {
		include_once('lib/wiki/xmllib.php');
		$xmllib = new XmlLib;
		$config = [];
		if ($xmllib->import_pages($zipFile, $config)) {
			$success = tr('Pages in zip file %0 successfully imported.', $filename);
		} else {
			$error = $xmllib->get_error();
		}
	}
	if (isset($success)) {
		Feedback::success(['mes' => $success]);
	}
	if (isset($error)) {
		Feedback::error(['mes' => $error]);
	}
}
ask_ticket('import_xml_zip');
$smarty->assign('mid', 'tiki-import_xml_zip.tpl');
$smarty->display("tiki.tpl");
