<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Recommendation;

class Recommendation
{
	private $type;
	private $object;

	function __construct($type, $object)
	{
		$this->type = $type;
		$this->object = $object;
	}

	function getType()
	{
		return $this->type;
	}

	function getId()
	{
		return $this->object;
	}
}
