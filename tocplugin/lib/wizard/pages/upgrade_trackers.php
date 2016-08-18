<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * The Wizard's language handler 
 */
class UpgradeWizardTrackers extends Wizard
{
    function pageTitle ()
    {
        return tra('Trackers');
    }

	function isEditable ()
	{
		return true;
	}
	
	function onSetupPage ($homepageUrl) 
	{
		global $prefs;
		// Run the parent first
		parent::onSetupPage($homepageUrl);
		
		$showPage = true;
		
		return $showPage;
	}

	function getTemplate()
	{
		$wizardTemplate = 'wizard/upgrade_trackers.tpl';
		return $wizardTemplate;
	}

	function onContinue ($homepageUrl) 
	{
		// Run the parent first
		parent::onContinue($homepageUrl);
	}
}
