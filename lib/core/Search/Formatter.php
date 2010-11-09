<?php

class Search_Formatter
{
	private $plugin;
	private $subFormatters = array();

	function __construct(Search_Formatter_Plugin_Interface $plugin)
	{
		$this->plugin = $plugin;
	}

	function addSubFormatter($name, $formatter)
	{
		$this->subFormatters[$name] = $formatter;
	}

	function format($list)
	{
		$defaultValues = $this->plugin->getFields();

		$subDefaults = array();
		foreach ($this->subFormatters as $key => $plugin) {
			$subDefault[$key] = $plugin->getFields();
		}

		$data = array();

		foreach ($list as $row) {
			$row = array_merge($defaultValues, $row);

			$subEntries = array();
			foreach ($this->subFormatters as $key => $plugin) {
				$subInput = new Search_Formatter_ValueFormatter(array_merge($subDefault[$key], $row));
				$subEntries[$key] = $this->render($plugin, array($plugin->prepareEntry($subInput)), $this->plugin->getFormat(), $list);
			}

			$row = array_merge($row, $subEntries);

			$data[] = $this->plugin->prepareEntry(new Search_Formatter_ValueFormatter($row));
		}

		return $this->render($this->plugin, $data, Search_Formatter_Plugin_Interface::FORMAT_WIKI, $list);
	}
	
	private function render($plugin, $data, $target, $resultSet)
	{
		$count = count($resultSet);
		$maxRecords = $count;
		$offset = 0;

		if ($resultSet instanceof Search_ResultSet) {
			$offset = $resultSet->getOffset();
			$maxRecords = $resultSet->getMaxRecords();
		}

		$pluginFormat = $plugin->getFormat();
		$rawOutput = $plugin->renderEntries($data, $count, $offset, $maxRecords);

		if ($target == $pluginFormat) {
			return $rawOutput;
		} elseif($target == Search_Formatter_Plugin_Interface::FORMAT_WIKI && $pluginFormat == Search_Formatter_Plugin_Interface::FORMAT_HTML) {
			return "~np~$rawOutput~/np~";
		} elseif($target == Search_Formatter_Plugin_Interface::FORMAT_HTML && $pluginFormat == Search_Formatter_Plugin_Interface::FORMAT_WIKI) {
			return "~/np~$rawOutput~np~";
		}
	}
}

