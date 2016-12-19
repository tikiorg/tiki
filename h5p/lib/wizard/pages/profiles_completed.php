<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * The Admin Profiles last screen
 */
class AdminWizardProfilesCompleted extends Wizard 
{
    function pageTitle ()
    {
        return tra('Configuration Profiles Wizard Completed!');
    }
    function isEditable ()
	{
		return false;
	}
	
	public function onSetupPage ($homepageUrl) 
	{
		// Run the parent first
		parent::onSetupPage($homepageUrl);
		
		return true;
	}

	function getTemplate()
	{
		$wizardTemplate = 'wizard/profiles_completed.tpl';
		return $wizardTemplate;
	}

	function onContinue ($homepageUrl) 
	{
		// Run the parent first
		parent::onContinue($homepageUrl);
	}
}
