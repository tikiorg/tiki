<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ContentFilter_VersionNumber implements Zend\Filter\FilterInterface
{
	public function filter($value)
	{
		return preg_replace_callback('/[0-9]+(\.[0-9]+)+/', array($this, 'augmentVersionTokens'), $value);
	}

	function augmentVersionTokens($version)
	{
		$version = $version[0];
		$out = $version;

		$pos = -1;
		while (false !== $pos = strpos($version, '.', $pos + 1)) {
			$out .= ' ' . substr($version, 0, $pos);
		}

		return $out;
	}
}
