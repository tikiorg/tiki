<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ResultSet_WikiBuilder
{
	private $result;
	private $paginationArguments;

	function __construct(Search_ResultSet $result)
	{
		$this->result = $result;
	}

	function setPaginationArguments($paginationArguments)
	{
		$this->paginationArguments = $paginationArguments;
	}

	function apply(WikiParser_PluginMatcher $matches)
	{
		$argumentParser = new WikiParser_PluginArgumentParser;

		foreach ($matches as $match) {
			$name = $match->getName();
			if ($name == 'group') {
				$arguments = $argumentParser->parse($match->getArguments());

				$field = isset($arguments['field']) ? $arguments['field'] : 'aggregate';
				$collect = isset($arguments['collect']) ? explode(',', $arguments['collect']) : ['user'];
				$this->result->groupBy($field, $collect);
			}
			if ($name == 'aggregate') {
				$arguments = $argumentParser->parse($match->getArguments());

				$fields = isset($arguments['fields']) ? explode(',', $arguments['fields']) : [];
				$totals = isset($arguments['totals']) ? explode(',', $arguments['totals']) : [];
				$this->result->aggregate($fields, $totals);
			}
		}

		if ($this->paginationArguments) {
			$this->result->setMaxResults($this->paginationArguments['max']);
		}
	}
}
