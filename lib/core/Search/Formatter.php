<?php

class Search_Formatter
{
	private $plugin;

	function __construct(Search_Formatter_Plugin_Interface $plugin)
	{
		$this->plugin = $plugin;
	}

	function format($list)
	{
		$defaultValues = $this->plugin->getFields();

		$data = array();

		foreach ($list as $row) {
			$row = array_merge($defaultValues, $row);

			$data[] = $this->plugin->prepareEntry(new Search_Formatter_ValueFormatter($row));
		}

		return $this->plugin->renderEntries($data);
	}
}

