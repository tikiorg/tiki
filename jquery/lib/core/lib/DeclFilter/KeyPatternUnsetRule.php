<?php

require_once 'lib/core/lib/DeclFilter/UnsetRule.php';

class DeclFilter_KeyPatternUnsetRule extends DeclFilter_UnsetRule
{
	private $keys;

	function __construct( $keys )
	{
		$this->keys = $keys;
	}

	function match( $key )
	{
		foreach( $this->keys as $pattern ) {
			if( preg_match( $pattern, $key ) ) {
				return true;
			}
		}

		return false;
	}
}

?>
