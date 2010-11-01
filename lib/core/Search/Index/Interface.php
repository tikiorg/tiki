<?php

interface Search_Index_Interface
{
	function addDocument(array $document);

	function find(Search_Expr_Interface $query, Search_Query_Order $sortOrder);

	function getTypeFactory();
}

