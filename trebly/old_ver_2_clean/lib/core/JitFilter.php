<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/core/TikiFilter.php';

class JitFilter implements ArrayAccess, Iterator, Countable
{
	private $stored;
	private $defaultFilter;
	private $lastUsed = array();
	private $filters = array();

	function __construct( $data )
	{
		$this->stored = $data;
	}

	function offsetExists( $offset )
	{
		return isset( $this->stored[$offset] );
	}

	function offsetUnset( $offset )
	{
		unset( $this->stored[$offset] );
		unset( $this->lastUsed[$offset] );
		unset( $this->filters[$offset] );
	}

	function offsetGet( $key )
	{
		// Composed objects go through
		if( $this->stored[$key] instanceof self )
			return $this->stored[$key];

		$filter = $this->getFilter( $key );

		if( is_array( $this->stored[$key] ) ) {
			$this->stored[$key] = new self( $this->stored[$key] );

			if( $filter ) {
				$this->stored[$key]->setDefaultFilter( $filter );
			}

			return $this->stored[$key];
		}

		if( $filter ) {
			if( isset( $this->lastUsed[$key] ) && $this->lastUsed[$key][0] == $filter )
				return $this->lastUsed[$key][1];


			$this->lastUsed[$key] = array( $filter, $filter->filter( $this->stored[$key] ) );
			return $this->lastUsed[$key][1];
		} else {
			// No filtering has no special behavior
			return $this->stored[$key];
		}
	}

	function offsetSet( $key, $value )
	{
		unset($this->lastUsed[$key]);

		if( $value instanceof self )
			return $this->stored[$key] = $value->stored;
		else
			return $this->stored[$key] = $value;
	}

	function asArray( $key = false, $separator = false )
	{
		if( $key === false ) {
			$ret = array();
			foreach( array_keys( $this->stored ) as $k ) {
				$ret[$k] = $this->offsetGet($k);
				if( $ret[$k] instanceof self )
					$ret[$k] = $ret[$k]->asArray();
			}

			return $ret;

		} elseif( isset( $this->stored[$key] ) ) {
			$value = $this->stored[$key];

			if( $value instanceof self || is_array( $value ) )
				return $this->offsetGet( $key )->asArray();
			elseif( $separator === false )
				return array( $this->offsetGet( $key ) );
			else {
				$jit = new self( explode( $separator, $value ) );
				$jit->setDefaultFilter( $this->getFilter( $key ) );

				return $jit->asArray();
			}
		} else {
			return array();
		}
	}

	function subset( $keys )
	{
		$jit = new self( array() );
		$jit->defaultFilter = $this->defaultFilter;
		$jit->filters = $this->filters;
		
		foreach( $keys as $key ) {
			if( isset($this->stored[$key]) )
				$jit->stored[$key] = $this->stored[$key];
			if( isset($this->lastUsed[$key]) )
				$jit->lastUsed[$key] = $this->lastUsed[$key];
		}

		return $jit;
	}

	function isArray( $key )
	{
		return isset($this->stored[$key]) && $this->offsetGet($key) instanceof self;
	}

	function keys()
	{
		return array_keys( $this->stored );
	}

	private function getFilter( $key )
	{
		if( array_key_exists( $key, $this->filters ) )
			return $this->filters[$key];
		elseif( $this->defaultFilter )
			return $this->defaultFilter;

		return null;
	}

	function setDefaultFilter( $filter )
	{
		$this->defaultFilter = TikiFilter::get( $filter );
	}

	function replaceFilter( $key, $filter )
	{
		$filter = TikiFilter::get( $filter );

		$this->filters[$key] = $filter;

		if( isset($this->stored[$key]) && $this->stored[$key] instanceof self ) {
			$this->stored[$key]->setDefaultFilter( $filter );
		}
	}

	function replaceFilters( $filters )
	{
		foreach( $filters as $key => $values ) {
			if( is_array( $values ) 
				&& $this->offsetExists( $key ) 
				&& $this->offsetGet( $key ) instanceof self ) {

				$this->offsetGet($key)->replaceFilters( $values );
			} else {
				$this->replaceFilter( $key, $values );
			}
		}
	}

	function current()
	{
		$key = key( $this->stored );
		return $this->offsetGet( $key );
	}

	function next()
	{
		next( $this->stored );
	}

	function rewind()
	{
		reset( $this->stored );
	}

	function key()
	{
		return key( $this->stored );
	}

	function valid()
	{
		return false !== current( $this->stored );
	}

	function count()
	{
		return count( $this->stored );
	}

	function __get( $key )
	{
		require_once 'JitFilter/Element.php';
		if( ! isset( $this->stored[$key] ) )
			return new JitFilter_Element( null );

		if( $this->stored[$key] instanceof self || is_array( $this->stored[$key] ) )
			return $this->offsetGet( $key );

		return new JitFilter_Element( $this->stored[$key] );
	}

	function filter( $filter )
	{
		$jit = new self( $this->stored );
		$jit->setDefaultFilter( $filter );
		return $jit->asArray();
	}

	function __call( $name, $arguments )
	{
		return $this->filter( $name );
	}
}
