<?php

class UniversalReports_Builder
{
	var $type = '';
	var $definition = array();
	
	static function load($type)
	{
		$me = new self();
		$me->type = ucwords($type);
		$me->definition();
		
		return $me;
	}
	
	private function definition()
	{
		$class = "UniversalReports_Definition_{$this->type}";
		$this->definition = call_user_func(array($class, "definition"));
	}
	
	function addOption($option) {
		
	}
	
	function replace()
	{
	
	}
	
	function get()
	{
	
	}
	
	function toSheet()
	{
	
	}
	
	function toCSV()
	{
	
	}
	
	function toChart()
	{
	
	}
}