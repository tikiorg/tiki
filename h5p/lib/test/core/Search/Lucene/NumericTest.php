<?php

class Search_Lucene_NumericTest extends Search_Index_NumericTest
{
	function setUp()
	{
		$this->dir = dirname(__FILE__) . '/test_index';
		$this->tearDown();

		$index = new Search_Lucene_Index($this->dir);

		$this->populate($index);
		$this->index = $index;
	}

	function tearDown()
	{
		if ($this->index) {
			$this->index->destroy();
		}
	}
}
