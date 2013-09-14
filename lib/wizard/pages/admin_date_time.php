<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
	function onSetupPage ($homepageUrl) 
	{
		global	$smarty, $prefs;

		// Run the parent first
		parent::onSetupPage($homepageUrl);

		// Assign the page temaplte
		$wizardTemplate = 'wizard/admin_date_time.tpl';
		$smarty->assign('wizardBody', $wizardTemplate);
	}

	function onContinue () 
	{
		global $wizardlib; 
		$prefslib = TikiLib::lib('prefs');

		// Run the parent first
		parent::onContinue();
		
		// Commit new preferences
		if ( isset( $_REQUEST['lm_preference'] ) ) {
			$changes = $prefslib->applyChanges((array) $_REQUEST['lm_preference'], $_REQUEST);
		}
	}
}