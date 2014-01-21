<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Query_FacetWikiBuilder
{
	private $facets = array();

	function apply(WikiParser_PluginMatcher $matches)
	{
		$argumentParser = new WikiParser_PluginArgumentParser;

		foreach ($matches as $match) {
			if ($match->getName() === 'facet') {
				$arguments = $argumentParser->parse($match->getArguments());
				$operator = isset($arguments['operator']) ? $arguments['operator'] : 'or';

				if (isset($arguments['name'])) {
					$this->facets[] = array(
						'name' => $arguments['name'],
						'operator' => $operator,
					);
				}
			}
		}
	}

	function build(Search_Query $query, Search_FacetProvider $provider)
	{
		foreach ($this->facets as $facet) {
			if ($real = $provider->getFacet($facet['name'])) {
				$real->setOperator($facet['operator']);
				$query->requestFacet($real);
			}
		}
	}
}

