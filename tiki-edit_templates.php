<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-edit_templates.php,v 1.23 2007-10-12 07:55:27 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

// if any of the two rights is not set, yell!
if ($prefs['feature_edit_templates'] != 'y' ) {
	$smarty->assign('msg', tra("Feature disabled"));

	$smarty->display("error.tpl");
	die;
}

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
		$smarty->assign('msg', tra("You do not have permission to do that"));

		$smarty->display('error.tpl');
		die;
	}
}

// do editing stuff only if you have the permission to:
if ($tiki_p_edit_templates == 'y') {
	if ((isset($_REQUEST["save"]) || isset($_REQUEST['saveTheme'])) && !empty($_REQUEST['template'])) {
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
			$smarty->assign('msg', tra("You do not have permission to write the template:").' '.$file);
			$smarty->display('error.tpl');
			die;
		}
		$_REQUEST["data"] = str_replace("\r\n","\n",$_REQUEST["data"]);
		fwrite($fp, $_REQUEST["data"]);
		fclose ($fp);
	}
	
	if (isset($_REQUEST['delete']) && !empty($_REQUEST['template'])) {
		$area = 'deltpl';
		if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
	    		key_check($area);
			$file = $smarty->get_filename($_REQUEST['template']);
			unlink($file);
			unset($_REQUEST['template']);
		} else {
			 key_get($area);
		}
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
	$where = array('', 'modules/', 'mail/', 'map/');
	$files = array();
	foreach ($where as $w) {
		$h = opendir($smarty->template_dir.$w);
		while (($file = readdir($h)) !== false) {
			if (substr($file,-4,4) == '.tpl') {
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

?>
