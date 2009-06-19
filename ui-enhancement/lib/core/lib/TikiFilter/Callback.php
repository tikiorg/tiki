<?php

class TikiFilter_Callback implements Zend_Filter_Interface
{
	private $callback;

	function __construct( $callback )
	{
		$this->callback = $callback;
	}

	function filter( $value )
	{
		$f = $this->callback;

		return $f( $value );
	}
}
