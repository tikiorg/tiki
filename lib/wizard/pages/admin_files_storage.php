<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
	function pageTitle ()
	{
		return tra('Set up File storage');
	}
	function isEditable ()
	{
		return true;
	}
	function isVisible ()
	{
		global	$prefs;
		return  $prefs['fgal_elfinder_feature'] === 'y' || // Elfinder
				$prefs['fgal_use_db'] !== 'y' || // File Gallery
				(($prefs['feature_wiki_attachments'] === 'y') && ($prefs['feature_use_fgal_for_wiki_attachments'] !== 'y'))
		;
	}

	function onSetupPage ($homepageUrl) 
	{
		global $prefs;
		$smarty = TikiLib::lib('smarty');
		// Run the parent first
		parent::onSetupPage($homepageUrl);
		
		$showPage = false;

		// Show if any more specification is needed
		
		// ElFinder
		if ($prefs['fgal_elfinder_feature'] === 'y') {
			$showPage = true;
			$smarty->assign('promptElFinder', 'y');

			// Determine the current filegal default view
			$defView = $prefs['fgal_default_view'];
			if (isset($defView)) {
				if ($defView == 'finder') {
					$smarty->assign('useElFinderAsDefault', true);
				} else {
					$smarty->assign('useElFinderAsDefault', false);
				}
			}
		}
		
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
		
		return $showPage;
	}

	function getTemplate()
	{
		$wizardTemplate = 'wizard/admin_files_storage.tpl';
		return $wizardTemplate;
	}

	function onContinue ($homepageUrl) 
	{
		global $tikilib;
		
		// Run the parent first
		parent::onContinue($homepageUrl);
		
		if (isset($_REQUEST['useElFinderAsDefault']) && $_REQUEST['useElFinderAsDefault'] === 'on') {
			// Set ElFinder view as the default File Gallery view
			$tikilib->set_preference('fgal_default_view', 'finder');
        } else {
            // Re-set back default File Gallery view to list
            $tikilib->set_preference('fgal_default_view', 'list');
        }
	}
}
