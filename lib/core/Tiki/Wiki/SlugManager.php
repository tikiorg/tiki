<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Wiki;

class SlugManager
{
	private $generators = [];
	private $validationCallback;

	function __construct()
	{
		$table = \TikiDb::get()->table('tiki_pages');
		$this->validationCallback = function ($slug) use ($table) {
			return $table->fetchCount(['pageSlug' => $slug]) > 0;
		};
	}

	function setValidationCallback(callable $callback)
	{
		$this->validationCallback = $callback;
	}

	function addGenerator(SlugManager\Generator $generator)
	{
		$this->generators[$generator->getName()] = $generator;
	}

	function getOptions()
	{
		return array_map(function ($generator) {
			return $generator->getLabel();
		}, $this->generators);
	}

	function generate($generator, $pageName)
	{
		$exists = $this->validationCallback;

		$impl = $this->generators[$generator];

		$slug = $impl->generate($pageName);

		$counter = 2;
		while ($exists($slug)) {
			$slug = $impl->generate($pageName, $counter++);
		}

		return $slug;
	}
}

