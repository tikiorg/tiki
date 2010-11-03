<?php

interface Search_Formatter_Plugin_Interface
{
	function getFields();

	function prepareEntry($entry);

	function renderEntries($entries);
}

