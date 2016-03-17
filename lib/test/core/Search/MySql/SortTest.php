<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_MySql_SortTest extends Search_Index_SortTest
{
	function setUp()
	{
		$this->index = new Search_MySql_Index(TikiDb::get(), 'test_index');
		$this->index->destroy();

		$this->populate($this->index);
	}

	function tearDown()
	{
		if ($this->index) {
			$this->index->destroy();
		}
	}

	function testWeightImpact()
	{
		$this->markTestSkipped('Weighting is not supported by MySQL full-text search');
	}
}

