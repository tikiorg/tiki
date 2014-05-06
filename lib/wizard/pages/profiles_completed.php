<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: profiles_completed.php 51026 2014-04-27 17:18:07Z xavidp $

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
		global	$smarty;

		// Run the parent first
		parent::onSetupPage($homepageUrl);

        // Assign the page template
        $wizardTemplate = 'wizard/profiles_completed.tpl';
        $smarty->assign('wizardBody', $wizardTemplate);

        return true;
	}

	function onContinue ($homepageUrl)
	{
		global $tikilib; 

		// Run the parent first
		parent::onContinue($homepageUrl);
	}
}
