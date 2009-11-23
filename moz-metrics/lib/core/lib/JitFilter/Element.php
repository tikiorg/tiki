<?php

class JitFilter_Element
{
	private $value;

	function __construct( $value )
	{
		$this->value = $value;
	}

	function filter( $filter )
	{
		$filter = TikiFilter::get($filter);

		return $filter->filter( $this->value );
	}

	function __call( $name, $arguments )
	{
		return $this->filter( $name );
	}
}
