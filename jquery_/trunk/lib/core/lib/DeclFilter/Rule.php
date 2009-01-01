<?php

/**
 * General interface for filtering rules. These rules are to be verified
 * sequentially until a matching one is found. Matching is made by looking
 * up the key only.
 */
interface DeclFilter_Rule
{
	/**
	 * Determines if the current rule applies for the given key.
	 *
	 * @param mixed Key name
	 * @return bool
	 */
	function match( $key );

	/**
	 * Apply the rule on the key. This method is expected to
	 * modify the data array on the provided key only.
	 */
	function apply( array &$data, $key );
}

?>
