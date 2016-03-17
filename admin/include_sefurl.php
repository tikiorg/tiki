<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));
if (isset($_REQUEST['save'])) {
	check_ticket('admin-inc-sefurl');
	$_REQUEST['feature_sefurl_paths'] = preg_split('/ *[,\/] */', $_REQUEST['feature_sefurl_paths']);
	simple_set_value('feature_sefurl_paths');
}

if (TikiInit::isIIS()) {
	$httpd = 'IIS';
	if (TikiInit::hasIIS_UrlRewriteModule()) {
		$smarty->assign('IIS_UrlRewriteModule', true);
		$enabledFileName = 'web.config';
		$referenceFileName = 'web_config';
	} else {
		$smarty->assign('IIS_UrlRewriteModule', false);
	}
} else {
	$enabledFileName = '.htaccess';
	$referenceFileName = '_htaccess';
	$httpd = 'Apache';
}
$smarty->assign('httpd', $httpd);

// Check if the URL rewriting configuration file is present and current
$configurationFile = "missing";
if (isset($enabledFileName)) {
	$enabledFile = @fopen($enabledFileName, "r");
	if ($enabledFile) {
		$referenceFile = fopen($referenceFileName, "r");
		if ($referenceFile) {
			if ($httpd == 'IIS') {
				// On IIS, the Id line is the second line, rather than the first as in Apache.
				fgets($referenceFile);
				fgets($enabledFile);
			}
			$referenceIdLine = fgets($referenceFile);
			$enabledIdLine = fgets($enabledFile);
			if (!strstr($enabledIdLine, 'This line is used to check that this configuration file is up to date.')) {
				$configurationFile = 'unexpected';
			} elseif ($referenceIdLine == $enabledIdLine) { // Do not warn if the Id line of each file is identical. Id lines contain configuration file revision.
				$configurationFile = 'current';
			} else {
				$configurationFile = 'outdated';
			}
			if ($httpd === 'Apache') {
				// work out if RewriteBase is set up properly
				global $url_path;
				$rewritebase = '/';
				while ($nextLine = fgets($enabledFile)) {
					if (preg_match('/^\s*?RewriteBase\s*[\'"]?(.*?)[\'"]?$/', $nextLine, $m)) {
						$rewritebase = substr($m[1], -1) !== '/' ? $m[1] . '/' : $m[1];
						break;
					}
				}
				if ($url_path != $rewritebase) {
					$smarty->assign('rewritebaseSetting', $rewritebase);
				}
			}
			fclose($referenceFile);
		} else {
			$configurationFile = 'no reference';
		}
		fclose($enabledFile);
	}
	$smarty->assign('referenceFileName', $referenceFileName);
	$smarty->assign('enabledFileName', $enabledFileName);
	$smarty->assign('configurationFile', $configurationFile);
}

ask_ticket('admin-inc-sefurl');
