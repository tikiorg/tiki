<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Recommendation;

class Recommendation implements EngineOutput
{
	private $type;
	private $object;
	private $title;

	function __construct($type, $object, $title = null)
	{
		$this->type = $type;
		$this->object = $object;
		$this->title = $title;
	}

	function getType()
	{
		return $this->type;
	}

	function getId()
	{
		return $this->object;
	}

	function getTitle()
	{
		return $this->title;
	}

	function __toString()
	{
		return tr('Recommendation: %0:%1 (%2)', $this->type, $this->object, $this->title ?: tr('Unknown'));
	}
}
