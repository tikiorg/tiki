<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\FileGallery\FileWrapper;

interface WrapperInterface
{
	/**
	 * Returns a path to a readable file path to read the content from.
	 * Can be used by external tools who use a file path as the input.
	 */
	function getReadableFile();

	/**
	 * Returns the content of the file as a string.
	 */
	function getContents();
}

