<?php

require_once 'lib/core/lib/DeclFilter/UnsetRule.php';

class DeclFilter_CatchAllUnsetRule extends DeclFilter_UnsetRule
{
	function match( $key )
	{
		return true;
	}
}
