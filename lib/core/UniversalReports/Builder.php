<?php

class UniversalReports_Builder
{
	var $type = '';
	var $id = null;
	var $input = array();
	var $name = '';
	var $description = '';
	
	function __construct() {
		$this->name = 'report';
		$this->description = '';
	}
	
	static function load($type)
	{
		$me = new self();
		$me->type = ucwords($type);
		
		$class = "UniversalReports_Definition_{$me->type}";
		$definition = new $class;
		$me->input = $definition->input();
		
		return $me;
	}
	
	static function open($id)
	{
		$me = new self();
		$me->id = $id;
		
		$object = TikiLib::lib('object')->get_object_via_objectid($me->id);
		$me->name = $object['name'];
		$me->description = $object['description'];
		
		$attributes = TikiLib::lib('attribute')->get_attributes('universal_reports', $me->id); 
		print_r($object);
		print_r($attributes);
		die;
		return $me;
	}
	
	static function listDefinitions()
	{
		$files = array();
		
		foreach(scandir('lib/core/UniversalReports/Definition') as $fileName) {
			if (preg_match('/[.]php/', $fileName)) {
				$files[] = str_replace('.php', '', $fileName);
			}
		}
		
		return $files;
	}
	
	static function listType($type)
	{
		
	}
	
	static function listTypes($type)
	{
		
	}
	
	function replace($name = '', $description = '')
	{
		$this->name = $name;
		$this->description = $description;
		
		$this->id = TikiLib::lib('object')->add_object('universal_reports', $this->id, TRUE, $this->description, $this->name);
		TikiLib::lib('attribute')->set_attribute('universal_reports', $id, 'type', $this->type);
		TikiLib::lib('attribute')->set_attribute('universal_reports', $id, 'values', $this->values);
		return $this->id;
	}
	
	function setValues($values = array())
	{
		$this->values = $values;
		return $this;
	}
	
	function setValuesFromRequest($values)
	{
		$parsedValues = array();
		foreach($values as $value) {
			$value = (array)$value; //was having trouble with downloading csv
			
			if (preg_match('/\[\]/', $value['name'])) {
				$value['name'] = str_replace('[]', '', $value['name']);
				$parsedValues[$value['name']][] = array(
					"value" => $value['value']
				);
			} else {
				$parsedValues[$value['name']] = array(
					"value" => $value['value']
				);
			}
		}
		
		return $this->setValues(TikiFilter_PrepareInput::delimiter('_')->prepare($parsedValues));
	}
	
	function outputArray()
	{
		$class = "UniversalReports_Definition_{$this->type}";
		$definition = new $class;
		return $definition->output($this->values);
	}
	
	function outputSheet()
	{
		$sheetlib = TikiLib::lib("sheet");
		
		$handler = new TikiSheetSimpleArrayHandler(array(
			"values"=>$this->outputArray(),
			"name"=>$this->name
		));
		
		$grid = new TikiSheet();
		$grid->import( $handler );
		
		return $grid->getTableHtml();
	}
	
	function outputCSV($auto = false)
	{
		$output = '';
		
		$header = false;
		
		foreach($this->outputArray() as $row) {
			if ($header == false) {
				$header = true;
				$headerNames = array();
				foreach($row as $headerName=>$col) {
					$headerNames[] = tr(ucwords($headerName));
				}
				
				$output .= '"' . implode('","', $headerNames) . '"'. "\n";
			}
			$output .= '"' . implode('","', $row) . '"'. "\n";
		}
		
		if ($auto == true) {
			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename=" . $this->name . ".csv");
			header("Pragma: no-cache");
			header("Expires: 0");
			echo $output;
			return '';
		}
		
		return $output;
	}
	
	function outputChart()
	{
		$output = $this->outputArray();
	}
}