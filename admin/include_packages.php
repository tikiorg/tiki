<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Tiki\Package\ComposerManager;

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

global $tikipath;

$composerManager = new ComposerManager($tikipath);
$composerManagerBundled = new ComposerManager($tikipath, $tikipath . DIRECTORY_SEPARATOR . 'vendor_bundled');

if ($access->ticketMatch()) {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if ($_POST['auto-fix-missing-packages']){
			$smarty->assign('composer_output', $composerManager->fixMissing());
		}
		if ($_POST['auto-install-package']){
			$smarty->assign('composer_output', $composerManager->installPackage($_POST['auto-install-package']));
		}
		if ($_POST['auto-remove-package']){
			$smarty->assign('composer_output', $composerManager->removePackage($_POST['auto-remove-package']));
		}
		if ($_POST['auto-run-diagnostics']){
			if (!$composerManager->composerIsAvailable()){
				$smarty->assign('diagnostic_composer_location', '');
				$smarty->assign('diagnostic_composer_output', '');
			} else {
				$smarty->assign('diagnostic_composer_location', $composerManager->composerPath());
				$smarty->assign('diagnostic_composer_output', $composerManager->getComposer()->execDiagnose());
			}
		}
	}
}

$installableList = $composerManager->getInstalled();
if ($installableList === false){
	$packagesMissing = false;
} else {
	$packagesMissing = array_reduce(
		$installableList,
		function ($carry, $item) {
			return $carry || $item['status'] === ComposerManager::STATUS_MISSING;
		},
		false
	);
}

$smarty->assign('composer_available', $composerManager->composerIsAvailable());
$smarty->assign('composer_packages_installed', $installableList);
$smarty->assign('composer_packages_missing', $packagesMissing);
$smarty->assign('composer_packages_available', $composerManager->getAvailable());
$smarty->assign('composer_bundled_packages_installed', $composerManagerBundled->getInstalled());