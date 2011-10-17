<?php

class UniversalReports_Parser
{
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
	
	function apply($vals)
	{
		return TikiFilter_PrepareInput::delimiter('_')->prepare($vals);
	}
}