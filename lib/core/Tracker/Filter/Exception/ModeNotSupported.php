<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Filter\Exception;

class ModeNotSupported extends Exception
{
	private $mode;
	private $permName;

	function __construct($permName, $mode)
	{
		parent::__construct(tr('Filter mode not found: %0 for %1', $mode, $permName));
		$this->mode = $mode;
		$this->permName = $permName;
	}
}

