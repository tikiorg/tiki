<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
