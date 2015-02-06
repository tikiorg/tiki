<?php
/**
 * (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
 *
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 * $Id: securitycheck.php 44444 2013-01-05 21:24:24Z changi67 $
 *
 * In Tiki 12.0 beta and before, the feature feature_use_fgal_for_wiki_attachments would add an empty gallery
 * for every single wiki page (on page load, even by anon i think).
 * This gets rid of all the empty ones...
 *
 * Usage:
 *
 *     php doc/devtools/remove_empty_wiki_attachment_filegals.php
 *
 */

if (isset($_SERVER['REQUEST_METHOD'])) {
	die;
}

require_once('tiki-setup.php');

function removeEmptyAttachmentGals()
{
	$galleryTable = TikiDb::get()->table('tiki_file_galleries');
	$fileTable = TikiDb::get()->table('tiki_files');
	$galleriesToDelete = array();

	$attachmentGalleries = $galleryTable->fetchAll(
		array('galleryId', 'name'),
		array('type' => 'attachments'));

	foreach ($attachmentGalleries as $gal) {
		$files = $fileTable->fetchCount(array('galleryId' => $gal['galleryId']));
		if (!$files) {
			$galleriesToDelete[] = $gal;
			echo "Attachment gallery: #{$gal['galleryId']} \"{$gal['name']}\" is empty, and will be removed\n";
			ob_flush();
		}
	}
	if ($galleriesToDelete) {
		$prompt = 'Are you sure you want to permanently remove all these (' . count($galleriesToDelete) . ') galleries? There is no undo... (y/n): ';
		if (readSTDIN($prompt, array('y', 'n')) == 'y') {
			echo "\n\n\nDeleting...\n\n";
			foreach ($galleriesToDelete as $gal) {
				TikiLib::lib('filegal')->remove_file_gallery($gal['galleryId']);
				echo "Removed gallery: #{$gal['galleryId']} \"{$gal['name']}\"\n";
				ob_flush();
			}
		}
	} else {
		echo "No empty attachement galleries found\n";
		ob_flush();
	}
	$remaining = count($attachmentGalleries) - count($galleriesToDelete);
	echo "There are $remaining attachment galleries left that contain files.\n";
	ob_flush();
}

function readSTDIN($prompt, $valid_inputs, $default = '') {
    while(!isset($input) || (is_array($valid_inputs) && !in_array($input, $valid_inputs)) || ($valid_inputs == 'is_file' && !is_file($input))) {
		echo $prompt;
		ob_flush();
        $input = strtolower(trim(fgets(STDIN)));
        if(empty($input) && !empty($default)) {
            $input = $default;
        }
    }
    return $input;
}

removeEmptyAttachmentGals();

