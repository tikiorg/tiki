<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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

if(TikiInit::isIIS()) {
	// Check if web.config is present and current
	$webconfig = 'missing';
	$filenameUsed = 'web.config';
	$fp = fopen($filenameUsed, "r");
	if ($fp) {
		$confverUsed = -1;
		$confverNew = -1;

		// Interpret web.config in use
		$contentUsed = fread($fp, filesize($filenameUsed));
		$xmlUsed = new SimpleXMLElement($contentUsed);
		if(isset($xmlUsed->appSettings)) {
			foreach($xmlUsed->appSettings->add as $node) {
				$attr = $node->attributes();
				if(isset($attr['key'])) {
					if(!strcmp((string)$attr['key'],'TikiConfVersion') && isset($attr['value'])) {
						$confverUsed = (int)$attr['value'];
						break;
					}
				}
			}
		}
	
		// Interpret new web_config
		$filenameNew = 'web_config';
		$fpNew = fopen($filenameNew, "r");
		if ($fpNew) {
			$contentNew = fread($fpNew, filesize($filenameNew));
			$xmlNew = new SimpleXMLElement($contentNew);
			if(isset($xmlNew->appSettings)) {
				foreach($xmlNew->appSettings->add as $node) {
					$attr = $node->attributes();
					if(isset($attr['key'])) {
						if(!strcmp((string)$attr['key'],'TikiConfVersion') && isset($attr['value'])) {
							$confverNew = (int)$attr['value'];
							break;
						}
					}
				}
			}
		}

		if($confverUsed >= $confverNew) {
			$webconfig = 'current';
		} else {
			$webconfig = 'outdated';
		}
		fclose($fp);
	}
	
	$smarty->assign('webconfig', $webconfig);
	$smarty->assign('IIS', true);
	if(TikiInit::hasIIS_UrlRewriteModule() == false) {
		$smarty->assign('IIS_UrlRewriteModule', false);
	}else {
		$smarty->assign('IIS_UrlRewriteModule', true);
	}
} else {
	// Check if .htaccess is present and current
	$htaccess = "missing";
	$fp = fopen('.htaccess', "r");
	if ($fp) {
		$htCurrent = fopen('_htaccess', "r");
		$installedFirstLine = fgets($fp); 
		if ($installedFirstLine == fgets($htCurrent)) { // Do not warn if the first line of each file is identical. First lines contain _htaccess revision
			$htaccess = 'current';
		} elseif(strstr($installedFirstLine, 'This line is used to check that this htaccess file is up to date.')) {
			$htaccess = 'outdated';
		}
		fclose($htCurrent);
		fclose($fp);
	}
	$smarty->assign('htaccess', $htaccess);
	$smarty->assign('IIS', false);
}

ask_ticket('admin-inc-sefurl');
