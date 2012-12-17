<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Render_Lazy
{
	private $callback;
	private $data;

	function __construct($callback)
	{
		$this->callback = $callback;
	}

	function __toString()
	{
		if ($this->callback) {
			$this->data = call_user_func($this->callback);
			$this->callback = null;
		}

		return (string) $this->data;
	}
}

