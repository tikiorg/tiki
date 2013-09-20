<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * The Wizard's File storage handler 
 */
class AdminWizardFileStorage extends Wizard 
{
	function onSetupPage ($homepageUrl) 
	{
		global	$smarty, $prefs;

		// Run the parent first
		parent::onSetupPage($homepageUrl);
		
		$showPage = false;

		// Show if any more specification is needed
		
		// File Gallery
		if ($prefs['fgal_use_db'] !== 'y') {
			$showPage = true;
			$smarty->assign('promptFileGalleryStorage', 'y');
		}

		// Attachments and not in the file gallery
		if (($prefs['feature_wiki_attachments'] === 'y') && ($prefs['feature_use_fgal_for_wiki_attachments'] !== 'y')) {
			$showPage = true;
			$smarty->assign('promptAttachmentStorage', 'y');
		}

		
		// Assign the page tempalte
		$wizardTemplate = 'wizard/admin_files_storage.tpl';
		$smarty->assign('wizardBody', $wizardTemplate);
		
		return $showPage;
	}

	function onContinue ($homepageUrl) 
	{
		// Run the parent first
		parent::onContinue($homepageUrl);
	}
}