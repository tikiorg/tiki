<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_MySql_IncrementalUpdateTest extends Search_Index_IncrementalUpdateTest
{
	protected $index;

	function setUp()
	{
		$this->index = $this->getIndex();
		$this->index->destroy();

		$this->populate($this->index);
	}

	protected function getIndex()
	{
		return new Search_MySql_Index(TikiDb::get(), 'test_index');
	}

	function tearDown()
	{
		if ($this->index) {
			$this->index->destroy();
		}
	}
}

