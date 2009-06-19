<?php

require_once 'lib/core/lib/DeclFilter/FilterRule.php';
require_once 'lib/core/lib/TikiFilter.php';

class DeclFilter_CatchAllFilterRule extends DeclFilter_FilterRule
{
	private $filter;

	function __construct( $filter )
	{
		$this->filter = TikiFilter::get($filter);
	}

	function match( $key )
	{
		return true;
	}

	function getFilter( $key )
	{
		return $this->filter;
	}
}
