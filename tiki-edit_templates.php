<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = array(
	array( 'staticKeyFilters' => array(
	'data' => 'none',
	)),
);


require_once ('tiki-setup.php');

$access->check_feature('feature_view_tpl');

// you have to have the perm view and edit to continue:
      // if view perm is set: continue
if ( ($tiki_p_view_templates != 'y') ||
      // if edit perm is set: continue, else quit if user tries save/delete
      ($tiki_p_edit_templates != 'y' &&
        (isset($_REQUEST["save"]) ||
         isset($_REQUEST['saveTheme']) ||
         isset($_REQUEST['delete'])
        )
      )
    ) { 
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You don't have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["mode"])) {
	$mode = 'listing';
} else {
	$mode = $_REQUEST['mode'];
}

// Validate to prevent editing any file
if (isset($_REQUEST["template"])) {
	if (strstr($_REQUEST["template"], '..')) {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to do that"));

		$smarty->display('error.tpl');
		die;
	}
}

$relativeDirectories = array('', 'mail/', 'map/', 'modules/', 'styles/'.str_replace('.css', '', $prefs['style']).'/');

// do editing stuff only if you have the permission to:
if ($tiki_p_edit_templates == 'y') {
	if ((isset($_REQUEST["save"]) || isset($_REQUEST['saveTheme'])) && !empty($_REQUEST['template'])) {
		$access->check_feature('feature_edit_templates');
		check_ticket('edit-templates');
		if (isset($_REQUEST['saveTheme'])) {
			$domainStyleTemplatesDirectory = $smarty->main_template_dir;
			if (!empty($tikidomain)) {
				$domainStyleTemplatesDirectory .= '/'.$tikidomain;
			}
			$domainStyleTemplatesDirectory .= '/styles/' . $style_base;
			if (!is_dir($domainStyleTemplatesDirectory)) {
				mkdir($domainStyleTemplatesDirectory);
			}
			$file = $domainStyleTemplatesDirectory . '/' . $_REQUEST['template'];
			$relativeDirectory = dirname($_REQUEST['template']);
			if ($relativeDirectory && !is_dir($domainStyleTemplatesDirectory . '/' . $relativeDirectory)) {
				if (in_array($relativeDirectory . '/', $relativeDirectories)) {
					mkdir($domainStyleTemplatesDirectory . '/' . $relativeDirectory);
				} else {
					$smarty->assign('msg', tr('Template directory %0 unknown', $relativeDirectory));
					$smarty->display('error.tpl');
				}
			}
		} else {
			$file = $smarty->get_filename($_REQUEST['template']);
		}
		@$fp = fopen($file, 'w');
		if (!$fp) {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("You do not have permission to write the template:").' '.$file);
			$smarty->display('error.tpl');
			die;
		}
		$_REQUEST["data"] = str_replace("\r\n", "\n", $_REQUEST["data"]);
		fwrite($fp, $_REQUEST["data"]);
		fclose($fp);
	}
	
	if (isset($_REQUEST['delete']) && !empty($_REQUEST['template'])) {
		$access->check_authenticity();
		$file = $smarty->get_filename($_REQUEST['template']);
		unlink($file);
		unset($_REQUEST['template']);
	}
}

if (isset($_REQUEST["template"])) {
	$mode = 'editing';
	$file = $smarty->get_filename($_REQUEST["template"]);
	if (strstr($file, '/styles/'))
		$style_local = 'y';
	else
		$style_local = 'n';
	$fp = fopen($file, 'r');
	if (!$fp) {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to read the template"));
		$smarty->display("error.tpl");
		die;
	}
	$data = fread($fp, filesize($file));
	fclose($fp);
	$smarty->assign('data', $data);
	$smarty->assign('template', $_REQUEST["template"]);
	$smarty->assign('style_local', $style_local);
}

if ($mode == 'listing') {
	// Get templates from the templates directory
	$files = array();
	chdir($smarty->main_template_dir);
	foreach ($relativeDirectories as $relativeDirectory) {
		$files = array_merge($files, glob($relativeDirectory . '*.tpl'));
	}
	chdir($tikipath);
	$smarty->assign('files', $files);
}
$smarty->assign('mode', $mode);

if ($tiki_p_edit_templates == 'y') {
	ask_ticket('edit-templates');
}

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Get templates from the templates/modules directory
$smarty->assign('mid', 'tiki-edit_templates.tpl');
$smarty->display("tiki.tpl");
