<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
    function pageTitle ()
    {
        return tra('Set up Text Area');
    }
	function isEditable ()
	{
		return true;
	}
	
	public function onSetupPage ($homepageUrl) 
	{
		global $prefs;
		$smarty = TikiLib::lib('smarty');
		// Run the parent first
		parent::onSetupPage($homepageUrl);

		$showPage = true;

		// Mark that Wysiwyg HTML mode is used
		if ((isset($prefs['feature_wysiwyg']) && $prefs['feature_wysiwyg'] === 'y') &&
			(!isset($prefs['wysiwyg_htmltowiki']) || $prefs['wysiwyg_htmltowiki'] === 'n')) {
			$smarty->assign('isHtmlMode', true);
		} else  {
			$smarty->assign('isHtmlMode', false);
		}

		// Hide Codemirror for RTL languages, since it doesn't work
		require_once('lib/language/Language.php');
		$isRTL = Language::isRTL();
		$smarty->assign('isRTL', $isRTL);
		
		return $showPage;		
	}

	function getTemplate()
	{
		$wizardTemplate = 'wizard/admin_text_area.tpl';
		return $wizardTemplate;
	}

	public function onContinue ($homepageUrl) 
	{
		global $tikilib; 

		// Run the parent first
		parent::onContinue($homepageUrl);
		
		// Configure detail preferences in own page
	}
}
