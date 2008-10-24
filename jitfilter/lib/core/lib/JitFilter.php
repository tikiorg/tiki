<?php

class JitFilter implements ArrayAccess, Iterator, Countable
{
	private $stored;
	private $defaultFilter;
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
	}

	function offsetGet( $key )
	{
		if( is_array( $this->stored[$key] ) ) {
			$this->stored[$key] = new self( $this->stored[$key] );
			if( $this->defaultFilter )
				$this->stored[$key]->setDefaultFilter( $this->defaultFilter );
		}

		// Composed objects go through
		if( $this->stored[$key] instanceof self )
			return $this->stored[$key];

		// Specified filters take precedence
		elseif( array_key_exists( $key, $this->filters ) )
			return $this->filters[$key]->filter( $this->stored[$key] );

		// Default filters apply
		elseif( $this->defaultFilter )
			return $this->defaultFilter->filter( $this->stored[$key] );

		// No filtering has no special behavior
		else
			return $this->stored[$key];
	}

	function offsetSet( $key, $value )
	{
		if( $value instanceof self )
			return $this->stored[$key] = $value->stored;
		else
			return $this->stored[$key] = $value;
	}

	function __toString()
	{
		return (string) $this->stored;
	}

	function setDefaultFilter( Zend_Filter_Interface $filter )
	{
		$this->defaultFilter = $filter;
	}

	function replaceFilter( $key, Zend_Filter_Interface $filter )
	{
		$this->filters[$key] = $filter;
	}

	function replaceFilters( $filters )
	{
		foreach( $filters as $key => $values ) {
			if( is_array( $values ) 
				&& $this->offsetExists( $key ) 
				&& $this->offsetGet( $key ) instanceof self ) {

				$this->offsetGet($key)->replaceFilters( $values );
			} elseif( $values instanceof Zend_Filter_Interface ) {
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
		// TODO : This may not be right
		if( next( $this->stored ) )
			return $this->current();
		else
			return false;
	}

	function rewind()
	{
		reset( $this->stored );
		return $this->current();
	}

	function key()
	{
		return key( $this->stored );
	}

	function valid()
	{
		// TODO : Find out what to do with this
		return current( $this->stored );
	}

	function count()
	{
		return count( $this->stored );
	}
}

?>
