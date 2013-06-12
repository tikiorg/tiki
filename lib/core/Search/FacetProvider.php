<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_FacetProvider implements Search_FacetProvider_Interface
{
	private $facets = array();

	function addContentSource(Search_ContentSource_Interface $source)
	{
		if ($source instanceof Search_FacetProvider_Interface) {
			$this->addFacets($source->getFacets());
		}
	}

	function addGlobalSource(Search_GlobalSource_Interface $source)
	{
		if ($source instanceof Search_FacetProvider_Interface) {
			$this->addFacets($source->getFacets());
		}
	}

	function addFacets(array $facets)
	{
		$this->facets = array_merge($this->facets, $facets);
	}

	function getFacets()
	{
		return $this->facets;
	}
}

