<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * The Wizard's jCapture handler 
 */
class AdminWizardJCapture extends Wizard 
{
	function onSetupPage ($homepageUrl) 
	{
		global	$smarty, $prefs;

		// Run the parent first
		parent::onSetupPage($homepageUrl);
		
		$showPage = false;

		// Show if option is selected
		if ($prefs['feature_jcapture'] === 'y') {
			$showPage = true;
		}
		
		// Assign the page tempalte
		$wizardTemplate = 'wizard/admin_jcapture.tpl';
		$smarty->assign('wizardBody', $wizardTemplate);
		
		return $showPage;
	}

	function onContinue () 
	{
		global	$tikilib;
		
		// Run the parent first
		parent::onContinue();
		
		// Set the jcapture file gallery to the file gallery root, unless it is already set
		if (intval($tikilib->get_preference('fgal_for_jcapture')) == 0) {
			$tikilib->set_preference('fgal_for_jcapture', '1');
		}
			
		// Set token access if not enabled
		if ($tikilib->get_preference('auth_token_access') !== 'y') {
			$tikilib->set_preference('auth_token_access', 'y');
		}
	}
}