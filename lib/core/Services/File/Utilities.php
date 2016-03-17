<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_File_Utilities
{
	function checkTargetGallery($galleryId)
	{
		global $prefs;

		if (! $gal_info = $this->getGallery($galleryId)) {
			throw new Services_Exception(tr('Requested gallery does not exist.'), 404);
		}

		if ($prefs['feature_use_fgal_for_user_files'] !== 'y' || $gal_info['type'] !== 'user') {
			$perms = Perms::get('file gallery', $galleryId);
			$canUpload = $perms->upload_files;
		} else {
			global $user;
			$perms = TikiLib::lib('tiki')->get_local_perms($user, $galleryId, 'file gallery', $gal_info, false);		//get_perm_object($galleryId, 'file gallery', $galinfo);
			$canUpload = $perms['tiki_p_upload_files'] === 'y';
		}
		if (!$canUpload) {
			throw new Services_Exception(tr('Permission denied.'), 403);
		}

		return $gal_info;
	}

	function getGallery($galleryId)
	{
		$filegallib = TikiLib::lib('filegal');
		return $filegallib->get_file_gallery_info($galleryId);
	}

	function uploadFile($gal_info, $name, $size, $type, $data, $asuser = null)
	{
		$filegallib = TikiLib::lib('filegal');
		return $filegallib->upload_single_file($gal_info, $name, $size, $type, $data, $asuser);
	}

	function updateFile($gal_info, $name, $size, $type, $data, $fileId, $asuser = null)
	{
		$filegallib = TikiLib::lib('filegal');
		return $filegallib->update_single_file($gal_info, $name, $size, $type, $data, $fileId, $asuser);
	}
}

