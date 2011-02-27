<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter
{
	private $plugin;
	private $subFormatters = array();
	private $dataSource;

	function __construct(Search_Formatter_Plugin_Interface $plugin)
	{
		$this->plugin = $plugin;
	}

	function setDataSource(Search_Formatter_DataSource_Interface $dataSource)
	{
		$this->dataSource = $dataSource;
	}

	function addSubFormatter($name, $formatter)
	{
		$this->subFormatters[$name] = $formatter;
	}

	function format($list)
	{
		$defaultValues = $this->plugin->getFields();

		$fields = array_keys($defaultValues);
		$subDefaults = array();
		foreach ($this->subFormatters as $key => $plugin) {
			$subDefault[$key] = $plugin->getFields();
			$fields = array_merge($fields, array_keys($subDefault[$key]));
		}

		if ($this->dataSource) {
			$list = $this->dataSource->getInformation($list, $fields);
		}

		if (in_array('highlight', $fields) && $list instanceof Search_ResultSet) {
			foreach ($list as & $entry) {
				$entry['highlight'] = $list->highlight($entry);
			}
		}

		$data = array();

		foreach ($list as $row) {
			// Clear blank values so the defaults prevail
			$row = array_filter($row);
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
			$out = $rawOutput;
		} elseif($target == Search_Formatter_Plugin_Interface::FORMAT_WIKI && $pluginFormat == Search_Formatter_Plugin_Interface::FORMAT_HTML) {
			$out = "~np~$rawOutput~/np~";
		} elseif($target == Search_Formatter_Plugin_Interface::FORMAT_HTML && $pluginFormat == Search_Formatter_Plugin_Interface::FORMAT_WIKI) {
			$out = "~/np~$rawOutput~np~";
		}

		$out = str_replace(array('~np~~/np~', '~/np~~np~'), '', $out);
		return $out;
	}
}

