<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * The Wizard's editor type selector handler 
 */
class AdminWizardLogin extends Wizard
{
    function pageTitle ()
    {
        return tra('Set up Login');
    }

	function isEditable ()
	{
		return true;
	}
	
	function onSetupPage ($homepageUrl) 
	{
		// Run the parent first
		parent::onSetupPage($homepageUrl);
		
		return true;
	}

	function getTemplate()
	{
		$wizardTemplate = 'wizard/admin_login.tpl';
		return $wizardTemplate;
	}

	function onContinue ($homepageUrl) 
	{
        // Run the parent first
        parent::onContinue($homepageUrl);

        // Configure detail preferences in own page
	}

}
