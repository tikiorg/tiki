<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * The Wizard's language handler 
 */
class UpgradeWizardSendFeedback extends Wizard
{
    function pageTitle ()
    {
        return tra('Send feedback & Connect');
    }

	function isEditable ()
	{
		return true;
	}
	
	function onSetupPage ($homepageUrl) 
	{
		global	$smarty, $prefs;

		// Run the parent first
		parent::onSetupPage($homepageUrl);

        // Assign the page template
        $wizardTemplate = 'wizard/upgrade_send_feedback.tpl';
        $smarty->assign('wizardBody', $wizardTemplate);

		$showPage = true;
		
		return $showPage;
	}

	function onContinue ($homepageUrl)
	{
		// Run the parent first
		parent::onContinue($homepageUrl);
	}
}
