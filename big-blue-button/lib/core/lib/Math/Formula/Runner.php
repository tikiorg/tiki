<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_Runner
{
	private $sources;
	private $collected = array();
	private $element;
	private $known = array();
	private $variables = array();

	function __construct( array $sources ) {
		$this->sources = $sources;
	}

	function setFormula( $element ) {
		$this->element = $this->getElement( $element );
		$this->collected = array();
	}

	function setVariables( array $variables ) {
		$this->variables = $variables;
	}

	function inspect() {
		if( $this->element ) {
			$this->inspectElement( $this->element );		
			return $this->collected;
		} else {
			require_once 'Math/Formula/Runner/Exception.php';
			throw new Math_Formula_Runner_Exception( tra('No formula provided.') );
		}
	}

	function evaluate() {
		return $this->evaluateData( $this->element );
	}

	function evaluateData( $data ) {
		if( $data instanceof Math_Formula_Element ) {
			$op = $this->getOperation( $data );
			return $op->evaluateTemplate( $data, array( $this, 'evaluateData' ) );
		} elseif( is_numeric( $data ) ) {
			return (double) $data;
		} elseif( isset( $this->variables[$data] ) ) {
			return $this->variables[$data];
		} else {
			require_once 'Math/Formula/Exception.php';
			throw new Math_Formula_Exception( tr('Variable not found "%0".', $data) );
		}
	}

	private function inspectElement( $element ) {
		$op = $this->getOperation( $element );

		$op->evaluateTemplate( $element, array( $this, 'inspectData' ) );
	}

	function inspectData( $data ) {
		if( $data instanceof Math_Formula_Element ) {
			$this->inspectElement( $data );
		} elseif( ! is_numeric( $data ) ) {
			$this->collected[] = $data;
		}

		return 0;
	}

	private function getElement( $element ) {
		if( is_string( $element ) ) {
			require_once 'Math/Formula/Parser.php';
			$parser = new Math_Formula_Parser;
			$element = $parser->parse( $element );
		}

		return $element;
	}

	private function getOperation( $element ) {
		$name = $element->getType();

		if( isset( $this->known[$name] ) ) {
			return $this->known[$name];
		}

		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter = new Zend_Filter_Word_DashToCamelCase;
		$ucname = $filter->filter( ucfirst( $name ) );

		foreach( $this->sources as $prefix => $path ) {
			$class = $prefix . $ucname;
			$file = "$path/$ucname.php";

			if( file_exists( $file ) ) {
				require_once $file;
				if( class_exists( $class ) ) {
					return $this->known[$name] = new $class;
				}
			}
		}

		require_once 'Math/Formula/Runner/Exception.php';
		throw new Math_Formula_Runner_Exception( tr('Unknown operation "%0".', $element->getType() ) );
	}
}

