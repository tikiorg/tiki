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
class AdminWizardTextArea extends Wizard 
{
	public function onSetupPage ($homepageUrl) 
	{
		global	$smarty, $prefs;

		// Run the parent first
		parent::onSetupPage($homepageUrl);

		// Only show page, when wiki format is used
		$showPage = true;
		if ((isset($prefs['feature_wysiwyg']) && $prefs['feature_wysiwyg'] === 'y') &&
			(!isset($prefs['wysiwyg_htmltowiki']) || $prefs['wysiwyg_htmltowiki'] === 'n')) {
			$showPage = false;
		}

		// Hide Codemirror for RTL languages, since it doesn't work
		require_once('lib/language/Language.php');
		$isRTL = Language::isRTL();
		$smarty->assign('isRTL', $isRTL);

		// Assign the page temaplte
		$wizardTemplate = 'wizard/admin_text_area.tpl';
		$smarty->assign('wizardBody', $wizardTemplate);
		
		return $showPage;		
	}

	public function onContinue ($homepageUrl) 
	{
		global $tikilib; 

		// Run the parent first
		parent::onContinue($homepageUrl);
		
		// Configure detail preferences in own page
	}
}
