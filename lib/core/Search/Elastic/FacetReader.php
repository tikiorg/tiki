<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_FacetReader
{
	private $data;

	function __construct(stdClass $data)
	{
		$this->data = $data;
	}

	function getFacetFilter(Search_Query_Facet_Interface $facet)
	{
		$facetName = $facet->getName();

		if (empty($this->data->facets->$facetName->total)) {
			return null;
		}

		$entry = $this->data->facets->$facetName;

		return new Search_ResultSet_FacetFilter($facet, $this->getFromTerms($entry));
	}

	private function getFromTerms($entry)
	{
		$out = array();

		foreach ($entry->terms as $term) {
			if ('' !== $term->term) {
				$out[] = array('value' => $term->term, 'count' => $term->count);
			}
		}

		return $out;
	}
}

