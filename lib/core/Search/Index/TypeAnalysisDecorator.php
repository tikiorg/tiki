<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Index_TypeAnalysisDecorator implements Search_Index_Interface
{
	private $parent;
	private $identifierClass;
	private $mapping = array();

	function __construct(Search_Index_Interface $index)
	{
		$this->parent = $index;
		$this->identifierClass = get_class($index->getTypeFactory()->identifier(1));
	}

	function addDocument(array $document)
	{
		$new = array_diff_key($document, $this->mapping);
		foreach ($new as $key => $value) {
			$this->mapping[$key] = $value instanceof $this->identifierClass;
		}
		return $this->parent->addDocument($document);
	}

	function invalidateMultiple(array $query)
	{
		return $this->parent->invalidateMultiple($query);
	}

	function endUpdate()
	{
		return $this->parent->endUpdate();
	}

	function find(Search_Query_Interface $query, $resultStart, $resultCount)
	{
		return $this->parent->find($query, $resultStart, $resultCount);
	}

	function getTypeFactory()
	{
		return $this->parent->getTypeFactory();
	}

	function optimize()
	{
		return $this->parent->optimize();
	}

	function getIdentifierFields()
	{
		return array_keys(array_filter($this->mapping));
	}
}

