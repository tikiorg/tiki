<?php

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
	}

	function offsetGet( $key )
	{
		if( is_array( $this->stored[$key] ) ) {
			$this->stored[$key] = new self( $this->stored[$key] );
			if( $this->defaultFilter )
				$this->stored[$key]->setDefaultFilter( $this->defaultFilter );
		}

		$filter = null;

		// Composed objects go through
		if( $this->stored[$key] instanceof self )
			return $this->stored[$key];

		// Specified filters take precedence
		elseif( array_key_exists( $key, $this->filters ) )
			$filter = $this->filters[$key];

		// Default filters apply
		elseif( $this->defaultFilter )
			$filter = $this->defaultFilter;

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
		if( $value instanceof self )
			return $this->stored[$key] = $value->stored;
		else
			return $this->stored[$key] = $value;
	}

	function __toString()
	{
		return (string) $this->stored;
	}

	function setDefaultFilter( $filter )
	{
		if( ! $filter instanceof Zend_Filter_Interface )
			$filter = $this->mapFilter( $filter );

		$this->defaultFilter = $filter;
	}

	function replaceFilter( $key, $filter )
	{
		if( ! $filter instanceof Zend_Filter_Interface )
			$filter = $this->mapFilter( $filter );

		$this->filters[$key] = $filter;
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

	static function mapFilter( $name )
	{
		switch( $name )
		{
		case 'alpha':
			require_once 'Zend/Filter/Alpha.php';
			return new Zend_Filter_Alpha;
		case 'alnum':
			require_once 'Zend/Filter/Alnum.php';
			return new Zend_Filter_Alnum;
		case 'digits':
			require_once 'Zend/Filter/Digits.php';
			return new Zend_Filter_Digits;
		case 'striptags':
			require_once 'Zend/Filter/StripTags.php';
			return new Zend_Filter_StripTags;
		case 'word':
			require_once 'JitFilter/Word.php';
			return new JitFilter_Word;
		case 'xss':
			require_once 'JitFilter/PreventXss.php';
			return new JitFilter_PreventXss;
		default:
			trigger_error( 'Filter not found: ' . $name, E_USER_WARNING );
			return new JitFilter_PreventXss;
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
}

?>
