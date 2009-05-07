<?php

require_once 'lib/core/lib/DeclFilter/UnsetRule.php';

class DeclFilter_StaticKeyUnsetRule extends DeclFilter_UnsetRule
{
	private $keys;

	function __construct( $keys )
	{
		$this->keys = $keys;
	}

	function match( $key )
	{
		return in_array( $key, $this->keys );
	}
}

?>
