<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * Set up the wysiwyg editor, including inline editing
 */
class UserWizardPreferencesNotifications extends Wizard 
{
	function isEditable ()
	{
		return true;
	}

	function onSetupPage ($homepageUrl) 
	{
		global	$smarty;

		// Run the parent first
		parent::onSetupPage($homepageUrl);
		
		// Show if option is selected
		if ($prefs['feature_user_watches'] === 'y') {
			$showPage = true;
		}

		// Assign the page template
		$wizardTemplate = 'wizard/user_preferences_notifications.tpl';
		$smarty->assign('wizardBody', $wizardTemplate);
		
		return true;		
	}

	function onContinue ($homepageUrl) 
	{
		// Run the parent first
		parent::onContinue($homepageUrl);
	}
}
