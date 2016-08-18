<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class DeclFilter_KeyPatternUnsetRule extends DeclFilter_UnsetRule
{
	private $keys;

	function __construct( $keys )
	{
		$this->keys = $keys;
	}

	function match( $key )
	{
		foreach ( $this->keys as $pattern ) {
			if ( preg_match($pattern, $key) ) {
				return true;
			}
		}

		return false;
	}
}
