<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

class WizardLib extends TikiLib
{
	public function onLogin($user, $homePageUrl, $force = false)
	{
		global $base_url, $userlib, $tikilib;
		
		$openWizard = false;
		
		if ($force) {
			// Force opening of the wizard
			// Needed for fresh-/re-installs, when admin is an anonymous user at check time
			$openWizard = true;
			
		} else {
			// Check the user status
			$isAdmin = $userlib->user_has_permission($user, 'tiki_p_admin');
		
			// Check if the wizard should be opened
			$activeLoginWizard = $tikilib->get_preference('wizard_admin_hide_on_login') !== 'y';
			if ($isAdmin && $activeLoginWizard) {
				$openWizard = true;
			}
		}
		
		if ($openWizard) {	
			// User is an admin
			// Start the admin wizard
			$url = $base_url.'tiki-wizard_admin.php?url=' . $homePageUrl;
			header('Location: '.$url);
			exit;
			
		} else {
			// A regular user
			// New User wizard is not implemented
		}
	}
	
	
	public function setupEditor($editorType) // ($useWysiwyg, $editorType, $useInlineEditing)
	{
		$tikilib = TikiLib::lib('tiki');
		
		$wysisygPrefs = array();
		switch ($editorType) {
			case 'html':
				$wysisygPrefs['wysiwyg_htmltowiki'] = 'n';
				break;
			
			case 'wiki':
			default:
				$wysisygPrefs['wysiwyg_htmltowiki'] = 'y';
				break;
		}
		$tikilib->set_preference('wysiwyg_htmltowiki', $wysisygPrefs['wysiwyg_htmltowiki']);
	}
	
	public function showOnLogin($showOnLogin)
	{
		$tikilib = TikiLib::lib('tiki');
		$hide = $showOnLogin === 'y' ? 'n' : 'y';
		$tikilib->set_preference('wizard_admin_hide_on_login', $hide);
	}
}
