<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
	function pageTitle ()
	{
		return tra('Set up jCapture');
	}
	function isEditable ()
	{
		return true;
	}
	function isVisible ()
	{
		global	$prefs;
		return $prefs['feature_jcapture'] === 'y';
	}

	function onSetupPage ($homepageUrl) 
	{
		global $prefs;
		$filegalib = TikiLib::lib('filegal');
		$smarty = TikiLib::lib('smarty');
		// Run the parent first
		parent::onSetupPage($homepageUrl);

		if (!$this->isVisible()) {
			return false;
		}

		// Set the name of the current jcapture gallery
		//	Unless the root file gallery is selected, then leave the name empty
		$galleryId = $prefs['fgal_for_jcapture'];
		$galleryName = '';
		if ($galleryId != $prefs['fgal_root_id']) {
			$gallery = $filegalib->get_file_gallery($galleryId);
			$galleryName = $gallery['name'];
		}
		$smarty->assign('jcaptureFileGalleryName', $galleryName);
		
		return true;
	}

	function getTemplate()
	{
		$wizardTemplate = 'wizard/admin_jcapture.tpl';
		return $wizardTemplate;
	}

	function onContinue ($homepageUrl) 
	{
		global	$prefs, $tikilib;
		
		// Run the parent first
		parent::onContinue($homepageUrl);
		
		// Set the specified gallery name
		$jcaptureFileGalleryName = isset($_REQUEST['jcaptureFileGalleryName']) ? $_REQUEST['jcaptureFileGalleryName'] : '';
		if (!empty($jcaptureFileGalleryName)) {
			$filegalib = TikiLib::lib('filegal');

			// Get the currently selected file gallery
			//	If the root gallery is selected, create a new one, if a name is specified
			$galleryId = intval($prefs['fgal_for_jcapture']);
			if (($galleryId > 0) && ($galleryId != $prefs['fgal_root_id'])) {
				$gallery = $filegalib->get_file_gallery($galleryId);
			}
			
			// If the specified jcapture gallery does not exist, create it
			if (empty($gallery)) {
				
				// Load the top level file galleries
				$galleryIdentifier = $prefs['fgal_root_id'];
				$subGalleries = $filegalib->getSubGalleries($galleryIdentifier);

				$galleryId = 0;
				foreach ($subGalleries['data'] as $gallery) {
					if ($gallery['name'] == $jcaptureFileGalleryName) {
						$galleryId = $gallery['galleryId'];
					}
				}
				
				// If the gallery doesn't exist, create it
				if ($galleryId == 0) {
					$fgal_info = $filegalib->get_file_gallery();
					$fgal_info['name'] = $jcaptureFileGalleryName;
					$galleryId = $filegalib->replace_file_gallery($fgal_info);
				}
			}
			
			// If a gallery exists, use it for jCapture
			if ($galleryId > 0) {
				$tikilib->set_preference('fgal_for_jcapture', ''.$galleryId);
			}
		} 

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
