<?php

interface Search_Formatter_Plugin_Interface
{
	const FORMAT_WIKI = 'wiki';
	const FORMAT_HTML = 'html';

	function getFields();

	function getFormat();

	function prepareEntry($entry);

	function renderEntries($entries, $count, $offset, $maxRecords);
}

