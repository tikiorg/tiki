<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Action_UnknownStep implements Search_Action_Step
{
	private $actionName;

	function __construct($action = null)
	{
		$this->actionName = $action;
	}

	function getFields()
	{
		return array();
	}

	function validate(array $entry)
	{
		return false;
	}

	function execute(array $entry)
	{
	}
}

