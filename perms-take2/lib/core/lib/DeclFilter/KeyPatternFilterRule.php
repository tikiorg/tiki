<?php

require_once 'lib/core/lib/DeclFilter/FilterRule.php';
require_once 'lib/core/lib/TikiFilter.php';

class DeclFilter_KeyPatternFilterRule extends DeclFilter_FilterRule
{
	private $rules;

	function __construct( $rules )
	{
		$this->rules = $rules;
	}

	private function getMatchingPattern( $key )
	{
		foreach( $this->rules as $pattern => $filter ) {
			if( preg_match( $pattern, $key ) ) {
				return $pattern;
			}
		}

		return false;
	}

	function match( $key )
	{
		return false !== $this->getMatchingPattern( $key );
	}

	function getFilter( $key )
	{
		$pattern = $this->getMatchingPattern( $key );
		return TikiFilter::get( $this->rules[$pattern] );
	}
}
