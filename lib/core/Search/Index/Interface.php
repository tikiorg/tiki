<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

interface Search_Index_Interface
{
	function addDocument(array $document);

	function invalidateMultiple(Search_Expr_Interface $query);

	function find(Search_Expr_Interface $query, Search_Query_Order $sortOrder, $resultStart, $resultCount);

	function getTypeFactory();

	function optimize();
}

