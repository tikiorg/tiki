<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
	/**
	* pageTitle
	*	Answer the title of the wizard page.
	* @return string
	*
	*/
	function pageTitle ()
	{
		return 'Page title not set';
	}

	function getTemplate()
	{
		return null;
	}

	/**
	* isVisible
	*	Answer if the page should be displayed or not.
	* @return bool		true if the page should be displayed
	*
	*/
	function isVisible ()
	{
		return true;
	}

	/**
	 * isEditable
	 *	Answer if any preferences or other settings are changed by the wizard page.
	 *	If it is editable, the wizard will prompt to Save the page (Save and Continue). Otherwise the "Next" is shown
	 * @return bool		true if the page alters preferences or other settings
	 *
	 */	
	function isEditable ()
	{
		return false;
	}
	
	/**
	 * This is method onSetupPage
	 *
	 * @param string $homepageUrl The url to return to, when the wizard is complete
	 * @return bool true if page should be shown. If false, the wizard will skip the page
	 *
	 */	
	function onSetupPage ($homepageUrl) {
		return true;
	}
	
	/**
	 * onContinue processes the settings on the wizard page.
	 * @param string $homepageUrl The url to return to, when the wizard is complete
	 * @return array
	 *
	 */	
	function onContinue ($homepageUrl) {
		// Save the user selection for showing the wizard on login or not
		$showOnLogin = ( isset($_REQUEST['showOnLogin']) && $_REQUEST['showOnLogin'] == 'on' ) ? 'y' : 'n';

		// Mark the login mode for the wizard
		$wizardlib = TikiLib::lib('wizard');
		$wizardlib->showOnLogin($showOnLogin);	

		$changes = array();
		// Commit any preferences on the page
		if ($this->isEditable()) {
			if ( isset( $_REQUEST['lm_preference'] ) ) {
				$prefslib = TikiLib::lib('prefs');
				$changes = $prefslib->applyChanges((array) $_REQUEST['lm_preference'], $_REQUEST);
			}		
		}
		return $changes;
	}
}
