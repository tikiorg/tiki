<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * Set up the date and time settings
 */
class AdminWizardDateTime extends Wizard
{
    function pageTitle ()
    {
        return tra('Set up Date and Time');
    }

	function isEditable ()
	{
		return true;
	}

	function onSetupPage ($homepageUrl)
	{
		global $prefs;
		$smarty = TikiLib::lib('smarty');
		// Run the parent first
		parent::onSetupPage($homepageUrl);

		return true;
	}

	function getTemplate()
	{
		$wizardTemplate = 'wizard/admin_date_time.tpl';
		return $wizardTemplate;
	}

	function onContinue ($homepageUrl)
	{
		// Run the parent first
		parent::onContinue($homepageUrl);
	}
}
