<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class for file_gallery_changed events
 */
class Reports_Send_EmailBuilder_FileGalleryChanged extends Reports_Send_EmailBuilder_Abstract
{
	public function getTitle()
	{
		return tr('File galleries changed:');
	}

	public function getOutput(array $change)
	{
		$base_url = $change['data']['base_url'];

		if (empty($change['data']['action'])) {
			$output = tr(
				'%0 edited the file gallery %1.',
				"<u>{$change['data']['user']}</u>",
				"<a href=\"{$base_url}tiki-list_file_gallery.php?galleryId={$change['data']['galleryId']}\">{$change['data']['galleryName']}</a>"
			);
		} elseif ($change['data']['action'] == 'upload file') {
			$output = tr(
				'%0 uploaded the file %1.',
				"<u>{$change['data']['user']}</u>",
				"<a href=\"{$base_url}tiki-download_file.php?fileId=" . $change['data']['fileId'] . "\">" . $change['data']['fileName'] . "</a> " .
				tra('onto') .
				" <a href=\"{$base_url}tiki-list_file_gallery.php?galleryId={$change['data']['galleryId']}\">{$change['data']['galleryName']}</a>"
			);
		} elseif ($change['data']['action'] == 'remove file') {
			$output = tr(
				'%0 removed the file %1 from %2.',
				"<u>{$change['data']['user']}</u>",
				"<a href=\"{$base_url}tiki-download_file.php?fileId={$change['data']['fileId']}\">{$change['data']['fileName']}</a>",
				"<a href=\"{$base_url}tiki-list_file_gallery.php?galleryId={$change['data']['galleryId']}\">{$change['data']['galleryName']}</a>"
			);
		}

		return $output;
	}
}
