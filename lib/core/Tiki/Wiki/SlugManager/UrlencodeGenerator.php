<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Wiki\SlugManager;

class UrlencodeGenerator implements Generator
{
	function getName()
	{
		return 'urlencode';
	}

	function getLabel()
	{
		return tr('URL Encode (Tiki Classic)');
	}

	function generate($pageName, $suffix = null)
	{
		return urlencode($pageName) . $suffix;
	}
}
