<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: WordTest.php 36534 2011-08-26 18:22:57Z changi67 $

/** 
 * @group unit
 * 
 */

class TikiFilter_WordTest extends TikiTestCase
{
	function testFilter()
	{
		$filter = new TikiFilter_Word();

		$this->assertEquals( '123ab_c', $filter->filter('-123 ab_c') );
	}
}
