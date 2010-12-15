<?php

interface Search_Index_Interface
{
	function addDocument(array $document);

	function invalidateMultiple(Search_Expr_Interface $query);

	function find(Search_Expr_Interface $query, Search_Query_Order $sortOrder, $resultStart, $resultCount);

	function getTypeFactory();

	function optimize();
}

