<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_Runner
{
	private $sources = array();
	private $collected = array();
	private $element;
	private $known = array();
	private $variables = array();

	function __construct( array $sources )
	{
		foreach ($sources as $prefix => $factory) {
			if (empty($factory)) {
				$factory = $this->getPrefixFactory($prefix);
			}

			$this->sources[] = $factory;
		}
	}

	function setFormula( $element )
	{
		$this->element = $this->getElement($element);
		$this->collected = array();

		return $this->element;
	}

	function setVariables( array $variables )
	{
		$this->variables = $variables;
	}

	function inspect()
	{
		if ( $this->element ) {
			$this->inspectElement($this->element);		
			return $this->collected;
		} else {
			throw new Math_Formula_Runner_Exception(tra('No formula provided.'));
		}
	}

	function evaluate()
	{
		return $this->evaluateData($this->element);
	}

	function evaluateData( $data, array $variables = array() )
	{
		if ( $data instanceof Math_Formula_InternalString) {
			return $data->getContent();
		} elseif ( $data instanceof Math_Formula_Element ) {
			$op = $this->getOperation($data);
			
			$current = $this->variables;
			if ( ! empty($variables) ) {
				$this->variables = array_merge($this->variables, $variables);
			}
			$out = $op->evaluateTemplate($data, array( $this, 'evaluateData' ));
			$this->variables = $current;

			return $out;
		} elseif ( is_numeric($data) ) {
			return (double) $data;
		} elseif ( isset($this->variables[$data]) ) {
			return $this->variables[$data];
		} elseif (false !== $value = $this->findVariable(explode('.', $data), $this->variables)) {
			return $value;
		} else {
			throw new Math_Formula_Exception(tr('Variable not found "%0".', $data));
		}
	}

	private function findVariable($path, $variables)
	{
		if (!count($path)) {
			return $variables;
		}

		$first = array_shift($path);

		if (isset($variables[$first])) {
			return $this->findVariable($path, $variables[$first]);
		} else {
			return false;
		}
	}

	private function inspectElement( $element )
	{
		$op = $this->getOperation($element);

		$op->evaluateTemplate($element, array( $this, 'inspectData' ));
	}

	function inspectData( $data )
	{
		if ( $data instanceof Math_Formula_Element ) {
			$this->inspectElement($data);
		} elseif ( ! is_numeric($data) ) {
			$this->collected[] = $data;
		}

		return 0;
	}

	private function getElement( $element )
	{
		if ( is_string($element) ) {
			$parser = new Math_Formula_Parser;
			$element = $parser->parse($element);
		}

		return $element;
	}

	private function getOperation( $element )
	{
		$name = $element->getType();

		if (isset($this->known[$name])) {
			return $this->known[$name];
		}

		foreach ( $this->sources as $factory ) {
			if ($function = $factory($name)) {
				return $this->known[$name] = $function;
			}
		}

		throw new Math_Formula_Runner_Exception(tr('Unknown operation "%0".', $element->getType()));
	}

	private function getPrefixFactory($prefix)
	{
		return function ($functionName) use ($prefix) {
			$filter = new Zend\Filter\Word\DashToCamelCase;

			// Workaround Deprecated errors showing from Zend lib
			if (error_reporting() & E_DEPRECATED) {
				$old_error_reporting = error_reporting();
				error_reporting($old_error_reporting - E_DEPRECATED);
			}

			$ucname = $filter->filter(ucfirst($functionName));

			if (isset($old_error_reporting)) {
				error_reporting($old_error_reporting);
			}

			$class = $prefix . $ucname;

			if (class_exists($class)) {
				return new $class;
			}
		};
	}

	function mockFunction($functionName, $function)
	{
		$this->known[$functionName] = $function;
	}
}

