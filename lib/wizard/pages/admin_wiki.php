<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * Set up the wiki settings
 */
class AdminWizardWiki extends Wizard 
{
	public function onSetupPage ($homepageUrl) 
	{
		global	$smarty, $prefs;

		// Run the parent first
		parent::onSetupPage($homepageUrl);

		$hideNamespaceIndicators = $this->isNamespaceIndicatorsHidden();
		$smarty->assign('hideNamespaceIndicators', $hideNamespaceIndicators);

		// Assign the page temaplte
		$wizardTemplate = 'wizard/admin_wiki.tpl';
		$smarty->assign('wizardBody', $wizardTemplate);
	}

	public function onContinue () 
	{
		global $wizardlib, $tikilib; 
		$prefslib = TikiLib::lib('prefs');

		// Run the parent first
		parent::onContinue();
		
		// Commit new preferences
		if ( isset( $_REQUEST['lm_preference'] ) ) {
			$changes = $prefslib->applyChanges((array) $_REQUEST['lm_preference'], $_REQUEST);
		}
		
		// Check if namespace indicator should be hidden
		if (isset($_REQUEST['hideNamespaceIndicators']) && $_REQUEST['hideNamespaceIndicators'] == 'on') {
			
			// Hide in structure path
			$tikilib->set_preference('namespace_indicator_in_structure', 'n');
		}
	}
	
	private function isNamespaceIndicatorsHidden() 
	{
		global $tikilib; 
		
		$hideInStructure = $tikilib->get_preference('namespace_indicator_in_structure');
		return $hideInStructure === 'y';
	}
}