<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

abstract class DeclFilter_FilterRule implements DeclFilter_Rule
{
	private $composite = false;

	abstract function getFilter($key);

	function apply( array &$data, $key )
	{
		$filter = $this->getFilter($key);
		
		if ( $this->composite ) {
			$this->applyRecursive($data[$key], $filter);
		} else {
			$data[$key] = $filter->filter($data[$key]);
		}
	}

	function applyOnElements()
	{
		$this->composite = true;
	}

	private function applyRecursive( &$data, $filter )
	{
		if ( is_array($data) ) {
			foreach ( $data as &$value ) {
				$this->applyRecursive($value, $filter);
			}
		} else {
			$data = $filter->filter($data);
		}
	}
}
