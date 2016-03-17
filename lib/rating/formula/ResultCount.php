<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Formula_Function_ResultCount extends Math_Formula_Function
{
	function evaluate( $element )
	{
		$allowed = ['filter'];

		if ( $extra = $element->getExtraValues($allowed) ) {
			$this->error(tr('Unexpected values: %0', implode(', ', $extra)));
		}

		$searchlib = TikiLib::lib('unifiedsearch');
		$query = new Search_Query;
		// These are absolute counts, so exclude jail and permission checks
		$searchlib->initQueryBase($query, false);
		$builder = new Search_Query_WikiBuilder($query);

		foreach ($element as $topLevel) {
			$arguments = $this->readMap($topLevel);
			$builder->addQueryArgument($topLevel->getType(), $arguments);
		}

		$query->setRange(0, 1);
		
		$result = $query->search($searchlib->getIndex());
		return count($result);
	}

	private function readMap($element)
	{
		$out = [];

		foreach ($element as $sub) {
			$out[$sub->getType()] = $this->evaluateChild($sub[0]);
		}

		return $out;
	}
}

