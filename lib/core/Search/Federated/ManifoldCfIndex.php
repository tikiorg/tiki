<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Search\Federated;

class ManifoldCfIndex implements IndexInterface
{
	private $type;
	private $prefix;

	function __construct($type, $urlPrefix)
	{
		$this->type = $type;
		$this->prefix = $urlPrefix;
	}

	function getTransformations()
	{
		return [
			function ($entry) {
				$entry['url'] = $entry['uri'];
				$entry['title'] = $entry['file']->_name;

				return $entry;
			},
			new UrlPrefixTransform($this->prefix),
			function ($entry) {
				$entry['object_type'] = 'external';
				$entry['object_id'] = $entry['url'];

				return $entry;
			},
		];
	}

	function applyContentConditions(\Search_Query $query, $content)
	{
		$query->filterContent($content, ['file']);
	}

	function applySimilarConditions(\Search_Query $query, $type, $object)
	{
		$query->filterSimilar($type, $object, 'file');
	}

	function getType()
	{
		return $this->type;
	}
}

