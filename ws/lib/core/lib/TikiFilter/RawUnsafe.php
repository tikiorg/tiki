<?php

class TikiFilter_RawUnsafe implements Zend_Filter_Interface
{
	function filter( $value )
	{
		return $value;
	}
}
