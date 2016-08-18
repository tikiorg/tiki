<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
