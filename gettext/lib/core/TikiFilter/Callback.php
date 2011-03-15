<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
