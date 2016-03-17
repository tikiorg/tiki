<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_Element implements ArrayAccess, Iterator, Countable
{
	private $type;
	private $children;

	function __construct( $type, array $children = array() )
	{
		$this->type = $type;
		$this->children = $children;
	}

	function addChild( $child )
	{
		$this->children[] = $child;
	}

	function offsetExists( $offset )
	{
		return is_int($offset) && isset($this->children[$offset]);
	}

	function offsetGet( $offset )
	{
		if ( isset($this->children[$offset]) ) {
			return $this->children[$offset];
		}
	}

	function offsetSet( $offset, $value )
	{
	}

	function offsetUnset($offset)
	{
	}

	function __get( $name )
	{
		foreach ( $this->children as $child ) {
			if ( $child instanceof Math_Formula_Element && $child->type == $name ) {
				return $child;
			}
		}
	}

	function getType()
	{
		return $this->type;
	}

	function current()
	{
		$key = key($this->children);
		return $this->children[$key];
	}

	function next()
	{
		next($this->children);
	}

	function rewind()
	{
		reset($this->children);
	}

	function key()
	{
		return key($this->children);
	}

	function valid()
	{
		return false !== current($this->children);
	}

	function count()
	{
		return count($this->children);
	}

	function getExtraValues( array $allowedKeys )
	{
		$extra = array();

		foreach ( $this->children as $child ) {
			if ( $child instanceof self ) {
				if ( ! in_array($child->type, $allowedKeys) ) {
					$extra[] = "({$child->type} ...)";
				}
			} else {
				$extra[] = $child;
			}
		}

		if ( count($extra) ) {
			return $extra;
		}
	}
}

