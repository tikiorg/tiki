<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class for image_gallery_changed events 
 */
class Reports_Send_EmailBuilder_ImageGalleryChanged extends Reports_Send_EmailBuilder_Abstract
{
	public function getTitle()
	{
		return tr('Image galleries changed:');
	}
	
	public function getOutput(array $change)
	{
		$base_url = $change['data']['base_url'];
		
		if (empty($change['data']['action'])) {

			$output = $change['data']['user'] . ' ' . tra('changed the picture gallery') . 
							" <a href=\"{$base_url}tiki-browse_gallery.php?galleryId=" . 
							$change['data']['galleryId'] . "&offset=0&sort_mode=created_desc\">" . $change['data']['galleryName'] . '</a>.';

		} elseif ($change['data']['action'] == 'upload image') {

			$output = '<u>' . $change['data']['user'] . '</u> ' . tra('uploaded the picture') . 
								" <a href=\"{$base_url}tiki-browse_image.php?imageId=" . $change['data']['imageId'] . "\">" . 
								$change['data']['imageName'] . '</a> ' . tra('onto') . 
								" <a href=\"{$base_url}tiki-browse_gallery.php?galleryId=" . 
								$change['data']['galleryId'] . "&offset=0&sort_mode=created_desc\">" . $change['data']['galleryName'] . '</a>.';

		} elseif ($change['data']['action']=="remove image") {

			$output = '<u>' . $change['data']['user'] . '</u> ' . tra('removed the picture') . 
								" <a href=\"{$base_url}tiki-browse_image.php?imageId=" . $change['data']['imageId'] . "\">" . 
								$change['data']['imageName'] . '</a> ' . tra('from') . 
								" <a href=\"{$base_url}tiki-browse_gallery.php?galleryId=" . $change['data']['galleryId'] . 
								"&offset=0&sort_mode=created_desc\">" . $change['data']['galleryName'] . '</a>.';

		}
				
		return $output;
	}
}
