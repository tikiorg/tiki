<?php

class WikiParser_PluginDefinition implements ArrayAccess, Countable
{
	private $repository;
	private $data;

	function __construct( $repository, $data )
	{
		$this->repository = $repository;
		$this->data = $data;
	}
	
	function offsetExists( $offset )
	{
		return isset( $this->data[$offset] );
	}

	function offsetGet( $offset )
	{
		return $this->data[$offset];
	}

	function offsetSet( $offset, $value )
	{
		// Immutable
		return $this->offsetGet( $offset );
	}

	function offsetUnset( $offset )
	{
		// Immutable
	}

	function count()
	{
		return count( $this->data );
	}
}

?>
