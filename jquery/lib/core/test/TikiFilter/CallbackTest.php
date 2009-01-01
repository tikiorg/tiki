<?php

class TikiFilter_CallbackTest extends PHPUnit_Framework_TestCase
{
	function testSimple()
	{
		$filter = new TikiFilter_Callback( 'strtoupper' );

		$this->assertEquals( 'HELLO', $filter->filter( 'hello' ) );
	}
}

?>
