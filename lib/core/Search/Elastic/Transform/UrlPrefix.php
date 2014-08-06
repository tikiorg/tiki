<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_Transform_UrlPrefix extends ArrayObject
{
	private $prefixMap = null;

	function __construct(array $inner, array $prefixMap)
	{
		parent::__construct($inner);
		$this->prefixMap = $prefixMap;
	}

	function offsetGet($name)
	{
		if ($name == 'url' && isset($this['url'], $this['_index'])) {
			$index = parent::offsetGet('_index');
			if (isset($this->prefixMap[$index])) {
				return $this->prefixMap[$index] . parent::offsetGet('url');
			}
		}

		return parent::offsetGet($name);
	}
}

