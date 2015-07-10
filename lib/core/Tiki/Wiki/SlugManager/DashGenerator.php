<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Wiki\SlugManager;

class DashGenerator implements Generator
{
	function getName()
	{
		return 'dash';
	}

	function getLabel()
	{
		return tr('Replace spaces with dashes');
	}

	function generate($pageName, $suffix = null)
	{
		$slug = preg_replace('/\s+/', '-', trim($pageName));

		if ($suffix) {
			$slug .= '-' . $suffix;
		}

		return $slug;
	}
}
