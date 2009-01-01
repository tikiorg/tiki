<?php

require_once 'DeclFilter/UnsetRule.php';

class DeclFilter_CatchAllUnsetRule extends DeclFilter_UnsetRule
{
	function match( $key )
	{
		return true;
	}
}

?>
