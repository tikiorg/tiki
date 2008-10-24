<?php

class JitFilter_CallbackTest extends PHPUnit_Framework_TestCase
{
	function testSimple()
	{
		$filter = new JitFilter_Callback( 'strtoupper' );

		$this->assertEquals( 'HELLO', $filter->filter( 'hello' ) );
	}
}

?>
