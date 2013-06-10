<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ResultSet_FacetFilter
{
	private $facet;
	private $data;

	function __construct(Search_Query_Facet_Interface $facet, array $data)
	{
		$this->facet = $facet;
		$this->data = $data;
	}

	function isFacet(Search_Query_Facet_Interface $facet)
	{
		return $this->facet->getName() === $facet->getName();
	}
}

