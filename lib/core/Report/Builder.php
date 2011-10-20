<?php

class UniversalReports_Builder
{
	var $type = '';
	var $id = null;
	var $input = array();
	var $name = '';
	var $description = '';
	var $values = array();
	
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
		//to come
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
	
	function replace($page, $index)
	{
		//to come
	}
	
	function setValues($values = array())
	{
		$this->values = $values;
		return $this;
	}
	
	static function loadFromWikiSyntax($lines = "")
	{
		$parsedValues = array();
		
		foreach(explode("\n", $lines) as $values) {
			$values = trim($values);
			if (!empty($values)) {
				$value = explode(":", $values);
				$parsedValues[trim($value[0])] = trim($value[1]);
			}
		}
		$me = new self();
		$me->type = $parsedValues['type'];
		unset($parsedValues['type']);
		return $me->setValues(TikiFilter_PrepareInput::delimiter('_')->prepare($parsedValues));
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
	
	function outputSheet($name = "")
	{
		$sheetlib = TikiLib::lib("sheet");
		
		if (empty($name)) {
			$name = $this->type;
		}
		
		$handler = new TikiSheetSimpleArrayHandler(array(
			"values"=>$this->outputArray(),
			"name"=>$name
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
	
	function outputWiki()
	{
		$result = 'type : ' . $this->type . "\n";
		foreach(TikiFilter_PrepareInput::delimiter('_')->flatten($this->values) as $key => $value) {
			$result .= $key .' : '. $value . "\n";
		}
		return $result; 
	}
}