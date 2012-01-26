<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Lib.php 34140 2011-04-26 21:47:41Z lphuberdeau $

class Event_Lib
{
	private $library;
	private $method;

	private function __construct($library, $method)
	{
		$this->library = $library;
		$this->method = $method;
	}

	public static function defer($library, $method)
	{
		return array(
			new self($library, $method),
			'trigger',
		);
	}

	function trigger($arguments)
	{
		TikiLib::lib($this->library)->{$this->method}($arguments);
	}
}

