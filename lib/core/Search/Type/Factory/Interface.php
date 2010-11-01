<?php

interface Search_Type_Factory_Interface
{
	function plaintext($value);
	function wikitext($value);
	function timestamp($value);
	function identifier($value);
	function multivalue($values);
	function sortable($value);
}
