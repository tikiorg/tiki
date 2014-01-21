<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Index_AbstractIndexDecorator implements Search_Index_Interface
{
	private $protected;

	function __construct(Search_Index_Interface $index)
	{
		$this->parent = $index;
	}

	function addDocument(array $document)
	{
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

	function destroy()
	{
		return $this->parent->destroy();
	}

	function exists()
	{
		return $this->parent->exists();
	}
}

