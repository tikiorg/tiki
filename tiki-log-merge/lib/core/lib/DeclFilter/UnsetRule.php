<?php

require_once 'lib/core/lib/DeclFilter/Rule.php';

abstract class DeclFilter_UnsetRule implements DeclFilter_Rule
{
	function apply( array &$data, $key )
	{
		unset($data[$key]);
	}
}
