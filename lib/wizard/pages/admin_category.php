<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
		global $prefs;
		// Run the parent first
		parent::onSetupPage($homepageUrl);
		
		if (!$this->isVisible()) {
			return false;
		}

		return true;
	}

	function getTemplate()
	{
		$wizardTemplate = 'wizard/admin_category.tpl';
		return $wizardTemplate;
	}

	function onContinue ($homepageUrl) 
	{
		// Run the parent first
		parent::onContinue($homepageUrl);
	}
}
