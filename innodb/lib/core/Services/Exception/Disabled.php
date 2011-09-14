<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Exception_Disabled extends Services_Exception
{
	function __construct($preference)
	{
		parent::__construct(tr('Feature disabled: %0', $preference), 403);
	}
}

