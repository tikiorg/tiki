<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Tabular\Writer;

class CsvWriter
{
	private $file;

	function __construct($outputFile)
	{
		$this->file = new \SplFileObject($outputFile, 'w');
	}

	function sendHeaders($filename = 'tiki-tracker-tabular-export.csv')
	{
		header('Content-Type: text/csv; charset=utf8');
		header("Content-Disposition:attachment;filename=$filename");
	}

	function write(\Tracker\Tabular\Source\SourceInterface $source)
	{
		$schema = $source->getSchema();
		$schema = $schema->getPlainOutputSchema();
		$schema->validate();

		$columns = $schema->getColumns();
		$headers = [];
		foreach ($columns as $column) {
			$headers[] = $column->getEncodedHeader();
		}
		$this->file->fputcsv($headers);

		foreach ($source->getEntries() as $entry) {
			$row = [];

			foreach ($columns as $column) {
				$row[] = $entry->render($column);
			}
			
			$this->file->fputcsv($row);
		}
	}
}

