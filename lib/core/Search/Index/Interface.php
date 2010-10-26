<?php

interface Search_Index_Interface
{
	function addDocument(array $document);

	function rawQuery($query);

	function getTypeFactory();
}

