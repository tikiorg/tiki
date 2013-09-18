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
		
		return true;		
	}

	public function onContinue () 
	{
		global $tikilib; 

		// Run the parent first
		parent::onContinue();
		
		// If Auto TOC is selected, also set the inline Toc
		if ($tikilib->get_preference('wiki_auto_toc')) {
			$tikilib->set_preference('wiki_inline_auto_toc', 'y');
		}

		// Check if namespace indicator should be hidden
		if ($tikilib->get_preference('namespace_enabled')) {
			
			// Hide in structure path
			$tikilib->set_preference('namespace_indicator_in_structure', 'n');
		}
		
		// jCapture
		$isJCapture = $tikilib->get_preference('feature_jcapture') === 'y';
		if ($isJCapture) {
			
			// Set the root file gallery to store captures
			if (intval($tikilib->get_preference('fgal_for_jcapture')) == 0) {
				$tikilib->set_preference('fgal_for_jcapture', '1');
			}
			
			// Set token access if not enabled
			if ($tikilib->get_preference('auth_token_access') !== 'y') {
				$tikilib->set_preference('auth_token_access', 'y');
			}
		}		
	}
	
	private function isNamespaceIndicatorsHidden() 
	{
		global $tikilib; 
		
		$hideInStructure = $tikilib->get_preference('namespace_indicator_in_structure');
		return $hideInStructure === 'y';
	}
}