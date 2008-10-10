<?php

/*
	Begining with TikiWiki 3.0, PHP >=5.1 is required. This file includes soft
	implementations of functions introduced in versions later versions of PHP.
*/

if( ! function_exists( 'json_encode' ) )
{
	function json_encode( $nodes )
	{
		require_once 'lib/pear/Services/JSON.php';

		$json = new Services_JSON();
		return $json->encode($nodes);
	}

	function json_decode( $string )
	{
		require_once 'lib/pear/Services/JSON.php';

		$json = new Services_JSON();
		return $json->decode($string);
	}
}

?>
