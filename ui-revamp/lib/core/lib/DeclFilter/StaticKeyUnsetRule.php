<?php

require_once 'DeclFilter/UnsetRule.php';

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
