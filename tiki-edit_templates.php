<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

$access->check_feature('feature_view_tpl');

// you have to have the perm view and edit to continue:
      // if view perm is set: continue
if  ( ($tiki_p_view_templates != 'y') ||
      // if edit perm is set: continue, else quit if user tries save/delete
      ($tiki_p_edit_templates != 'y' &&
        (isset($_REQUEST["save"]) ||
         isset($_REQUEST['saveTheme']) ||
         isset($_REQUEST['delete'])
        )
      )
    )
{ 
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

// do editing stuff only if you have the permission to:
if ($tiki_p_edit_templates == 'y') {
	if ((isset($_REQUEST["save"]) || isset($_REQUEST['saveTheme'])) && !empty($_REQUEST['template'])) {
		$access->check_feature('feature_edit_templates');
		check_ticket('edit-templates');
		if (isset($_REQUEST['saveTheme'])) {
			if (!empty($tikidomain)) {
				if (!is_dir($smarty->template_dir.'/'.$tikidomain.'/styles/'.$style_base))
					mkdir($smarty->template_dir.'/'.$tikidomain.'/styles/'.$style_base);
				$file = $smarty->template_dir.'/'.$tikidomain.'/styles/'.$style_base.'/'.$_REQUEST['template'];
			} else {
				if (!is_dir($smarty->template_dir.'/styles/'.$style_base))
					mkdir($smarty->template_dir.'/styles/'.$style_base);
				$file = $smarty->template_dir.'/styles/'.$style_base.'/'.$_REQUEST['template'];
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
		$_REQUEST["data"] = str_replace("\r\n","\n",$_REQUEST["data"]);
		fwrite($fp, $_REQUEST["data"]);
		fclose ($fp);
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
	$fp = fopen($file,'r');
	if (!$fp) {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to read the template"));
		$smarty->display("error.tpl");
		die;
	}
	$data = fread($fp, filesize($file));
	fclose ($fp);
	$smarty->assign('data', $data);
	$smarty->assign('template', $_REQUEST["template"]);
	$smarty->assign('style_local', $style_local);
}

if ($mode == 'listing') {
	// Get templates from the templates directory
	$local = 'styles/'.str_replace('.css', '', $prefs['style']).'/';
	$where = array('', 'modules/', 'mail/', 'map/', $local);
	$files = array();
	foreach ($where as $w) {
		$h = opendir($smarty->template_dir.$w);
		while (($file = readdir($h)) !== false) {
			if (substr($file,-4,4) == '.tpl' && ($w != $local || !in_array($file, $files))) {
				$files[] = $w.$file;
			}
		}
		closedir ($h);
	}
	sort ($files);
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
