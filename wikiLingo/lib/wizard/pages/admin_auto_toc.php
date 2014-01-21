<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * The Wizard's language handler 
 */
class AdminWizardAutoTOC extends Wizard 
{
	function pageTitle ()
	{
		return tra('Set up Auto TOC');
	}
	function isEditable ()
	{
		return true;
	}

	function isVisible ()
	{
		global	$prefs;
		return $prefs['wiki_auto_toc'] === 'y';
	}

	function onSetupPage ($homepageUrl) 
	{
		global	$smarty;

		// Run the parent first
		parent::onSetupPage($homepageUrl);

		if (!$this->isVisible()) {
			return false;
		}

		// Assign the page template
		$wizardTemplate = 'wizard/admin_auto_toc.tpl';
		$smarty->assign('wizardBody', $wizardTemplate);
		
		return true;
	}

	function onContinue ($homepageUrl) 
	{
		// Run the parent first
		parent::onContinue($homepageUrl);
	}
}