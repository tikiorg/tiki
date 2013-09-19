<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * The Wizard's first page and frame handler 
 */
class AdminWizard extends Wizard 
{
	function onSetupPage ($homepageUrl) 
	{
		global	$smarty, $prefs;

		// Run the parent first
		parent::onSetupPage($homepageUrl);
		
		// Assign the page temaplte
		$wizardTemplate = 'wizard/admin_wizard.tpl';
		$smarty->assign('wizardBody', $wizardTemplate);
		
		return true;
	}

	function onContinue () 
	{
		// Run the parent first
		parent::onContinue();
		
		require_once('lib/wizard/wizardlib.php');
		$wizardlib = new WizardLib();

		// User selected to skip the wizard and hide it on login
		//	Save the "Show on login" setting, and no other preferences
		//	Set preference to hide on login
		if (isset($_REQUEST['skip'])) {
			
			// Save "Show on login" setting
			$showOnLogin = false;
			$wizardlib->showOnLogin($showOnLogin);
			
			//	Then exit, by returning the specified URL
			$accesslib = TikiLib::lib('access');
			$accesslib->redirect($homepageUrl);
		}
		
	}
}