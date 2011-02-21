<?php

/** 
 * @group unit
 * 
 */

class TikiFilter_CallbackTest extends TikiTestCase
{
	function testSimple()
	{
		$filter = new TikiFilter_Callback( 'strtoupper' );

		$this->assertEquals( 'HELLO', $filter->filter( 'hello' ) );
	}
}
