<?php

require_once 'lib/core/lib/DeclFilter/Rule.php';

abstract class DeclFilter_FilterRule implements DeclFilter_Rule
{
	private $composite = false;

	abstract function getFilter( $key );

	function apply( array &$data, $key )
	{
		$filter = $this->getFilter( $key );
		
		if( $this->composite ) {
			$this->applyRecursive( $data[$key], $filter );
		} else {
			$data[$key] = $filter->filter( $data[$key] );
		}
	}

	function applyOnElements()
	{
		$this->composite = true;
	}

	private function applyRecursive( &$data, $filter )
	{
		if( is_array( $data ) ) {
			foreach( $data as &$value ) {
				$this->applyRecursive( $value, $filter );
			}
		} else {
			$data = $filter->filter( $data );
		}
	}
}

?>
