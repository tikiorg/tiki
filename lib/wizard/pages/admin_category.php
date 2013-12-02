<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * Wizard page handler 
 */
class AdminWizardCategory extends Wizard 
{
	function pageTitle ()
	{
		return tra('Define Categories');
	}
	function isEditable ()
	{
		return false;
	}
	function isVisible ()
	{
		global	$prefs;
		return $prefs['feature_categories'] === 'y';
	}

	function onSetupPage ($homepageUrl) 
	{
		global	$smarty, $prefs;

		// Run the parent first
		parent::onSetupPage($homepageUrl);
		
		if (!$this->isVisible()) {
			return false;
		}

		// Assign the page tempalte
		$wizardTemplate = 'wizard/admin_category.tpl';
		$smarty->assign('wizardBody', $wizardTemplate);
		
		return true;
	}

	function onContinue ($homepageUrl) 
	{
		// Run the parent first
		parent::onContinue($homepageUrl);
	}
}