<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_Action implements Tracker_Field_Interface
{
	function getFieldData(array $requestData = array())
	{
		return array();
	}

	function renderInput($context = array())
	{
		return null;
	}

	function renderOutput($context = array())
	{
		return null;
	}

	function watchCompare($new, $old)
	{
		return null;
	}
}
