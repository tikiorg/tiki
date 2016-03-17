<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\FileGallery\Handler;
use Tiki\FileGallery\FileWrapper\PreloadedContent;

class Preloaded implements HandlerInterface
{
	function getFileWrapper($data, $path)
	{
		return new PreloadedContent($data);
	}

	function delete($data, $path)
	{
	}
}

