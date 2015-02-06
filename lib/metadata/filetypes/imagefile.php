<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * Reads metadata common to image files
 * Called by the FileMetadata class at metadata/metadata.php, which handles generic file metadata
 * Image-specific classes (like Jpeg) extend this class
 */
class ImageFile
{
	var $header = null;
	var $width = null;
	var $height = null;
	var $otherinfo = null;

	/**
	 * Assign common image metadata information to properties
	 *
	 * @param 		FileMetadata object			$metaObj
	 */
	function __construct($metaObj)
	{
			$this->header = getimagesize($metaObj->currname, $otherinfo);
			$this->width = $this->header[0];
			$this->height = $this->header[1];
			$this->otherinfo = $otherinfo;
	}
}

