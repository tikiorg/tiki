<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_FacetProvider implements Search_FacetProvider_Interface
{
	private $facets = array();

	function addContentSource($type, Search_ContentSource_Interface $source)
	{
		if ($source instanceof Search_FacetProvider_Interface) {
			$this->addProvider($source);
		}
	}

	function addGlobalSource(Search_GlobalSource_Interface $source)
	{
		if ($source instanceof Search_FacetProvider_Interface) {
			$this->addProvider($source);
		}
	}

	function addProvider(Search_FacetProvider_Interface $provider)
	{
		$this->addFacets($provider->getFacets());
	}

	function addFacets(array $facets)
	{
		foreach ($facets as $facet) {
			$this->facets[$facet->getName()] = $facet;
		}
	}

	function getFacets()
	{
		return $this->facets;
	}

	function getFacet($name)
	{
		if (isset($this->facets[$name])) {
			return $this->facets[$name];
		}
	}
}

