<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/** 
 * @group unit
 * 
 */

class TikiFilter_WordTest extends TikiTestCase
{
	function testFilter()
	{
		$filter = new TikiFilter_Word();

		$this->assertEquals('123ab_c', $filter->filter('-123 ab_c'));
	}
}
