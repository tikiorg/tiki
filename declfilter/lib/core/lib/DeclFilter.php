<?php

require_once 'Zend/Filter/Interface.php';

/**
 * Declarative filters are to be defined prior to filtering. Various rules can
 * be defined in sequence. The first filter that applies will be used.
 */
class DeclFilter implements Zend_Filter_Interface
{
	private $rules = array();
	
	function filter( $data )
	{
		$keys = array_keys( $data );

		foreach( $keys as $key ) {
			// Loop until a matching filter is found
			foreach( $this->rules as $rule ) {
				if( $rule->match( $key ) ) {
					$rule->apply( $data, $key );
					break;
				}
			}
		}

		return $data;
	}

	function addStaticKeyFilters( array $filters )
	{
		require_once 'DeclFilter/StaticKeyFilterRule.php';
		$rule = new DeclFilter_StaticKeyFilterRule( $filters );

		$this->rules[] = $rule;
	}

	function addStaticKeyFiltersForArrays( $filters )
	{
		require_once 'DeclFilter/StaticKeyFilterRule.php';
		$rule = new DeclFilter_StaticKeyFilterRule( $filters );
		$rule->applyOnElements();

		$this->rules[] = $rule;
	}

	function addCatchAllFilter( $filter )
	{
		require_once 'DeclFilter/CatchAllFilterRule.php';
		$rule = new DeclFilter_CatchAllFilterRule( $filter );
		$rule->applyOnElements();

		$this->rules[] = $rule;
	}
}

?>
