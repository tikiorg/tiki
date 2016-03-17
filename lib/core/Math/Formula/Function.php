<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

abstract class Math_Formula_Function
{
	private $callback;

	function evaluateTemplate( $element, $evaluateCallback )
	{
		$this->callback = $evaluateCallback;
		return $this->evaluate($element);
	}

	abstract function evaluate( $element );

	protected function evaluateChild( $child, array $extraVariables = array() )
	{
		return call_user_func($this->callback, $child, $extraVariables);
	}

	protected function error( $message )
	{
		throw new Math_Formula_Exception($message);
	}
}

