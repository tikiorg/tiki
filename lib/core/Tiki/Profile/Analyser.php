<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_Analyser
{
	private $profile;
	private $builder;

	function __construct(Tiki_Profile $profile)
	{
		$this->profile = $profile;
		$this->builder = new Tiki_Profile_Builder;
	}

	function ref($name)
	{
		return $this->builder->ref($name);
	}

	function user($name)
	{
		return $this->builder->user($name);
	}
	
	function contains(array $conditions)
	{
		$objects = $this->profile->getObjects();

		if (isset($conditions['type'])) {
			$objects = array_filter($objects, function ($object) use ($conditions) {
				return $conditions['type'] == $object->getType();
			});
			unset($conditions['type']);
		}

		if (isset($conditions['ref'])) {
			$objects = array_filter($objects, function ($object) use ($conditions) {
				return $conditions['ref'] === $object->getRef();
			});
			unset($conditions['ref']);
		}

		foreach ($conditions as $condition => $value) {
			$objects = array_filter($objects, function ($object) use ($condition, $value) {
				$data = $object->getData();
				return isset($data[$condition]) && $data[$condition] === $value;
			});
		}

		return count($objects) > 0;
	}
}

