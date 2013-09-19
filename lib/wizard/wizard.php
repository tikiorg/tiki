<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Wizard is the super class for all wizard pages.
 * 
 * All subclasses / wizard pages, must
 * 1) Overload the onSetupPage and onContinue methods
 * 2) Call the parent function first in the overloaded implamentation
 * 
 * The wizard page can decide if it's going to be visible or not, in the onSetupPage method.
 * If it returns true, the page is displayed, otherwize it is hidden.
 * This can be used to activate pages to specify the detail preferences, if a main preference is set.
 */
abstract class Wizard 
{
	protected $returnUrl;
	
	/**
	 * This is method onSetupPage
	 *
	 * @param string $homepageUrl The url to return to, when the wizard is complete
	 * @return bool true if page should be shown. If false, the wizard will skip the page
	 *
	 */	
	function onSetupPage ($homepageUrl) {
		$this->returnUrl = $homepageUrl;
		return true;
	}
	
	/**
	 * onContinue processes the settings on the wizard page.
	 * @return none
	 *
	 */	
	function onContinue () {
		// Save the user selection for showing the wizard on login or not
		$showOnLogin = ( isset($_REQUEST['showOnLogin']) && $_REQUEST['showOnLogin'] == 'on' ) ? 'y' : 'n';

		// Mark the login mode for the wizard
		require_once('lib/wizard/wizardlib.php');
		$wizardlib = new WizardLib();
		$wizardlib->showOnLogin($showOnLogin);	
		
		// Commit any preferences on the page
		if ( isset( $_REQUEST['lm_preference'] ) ) {
			$prefslib = TikiLib::lib('prefs');
			$changes = $prefslib->applyChanges((array) $_REQUEST['lm_preference'], $_REQUEST);
		}		
	}
}