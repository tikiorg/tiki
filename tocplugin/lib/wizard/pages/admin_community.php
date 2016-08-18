<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * Set up the wiki settings
 */
class AdminWizardCommunity extends Wizard 
{
    function pageTitle ()
    {
        return tra('Set up User & Community features');
    }
	function isEditable ()
	{
		return true;
	}
	
	public function onSetupPage ($homepageUrl) 
	{
		// Run the parent first
		parent::onSetupPage($homepageUrl);
		
		return true;		
	}

	function getTemplate()
	{
		$wizardTemplate = 'wizard/admin_community.tpl';
		return $wizardTemplate;
	}

	public function onContinue ($homepageUrl) 
	{
		// Run the parent first
		parent::onContinue($homepageUrl);
		
		// Configure detail preferences in own page
	}
}
