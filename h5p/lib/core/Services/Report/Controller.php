<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Report_Controller
{
	function setUp()
	{
	}

	function action_edit($input)
	{
		global $reportFullscreen, $index, $values;
		$headerlib = TikiLib::lib('header');
		$tikilib = TikiLib::lib('tiki');
		$access = TikiLib::lib('access');

		$reportFullscreen = true;
		$index = $input->index->int();
		$values = $input->values->text();
		
		include_once 'tiki-edit_report.php';
	}
	
	function action_load($input)
	{
		return Report_Builder::load($input->type->string())->input;
	}
	
	function action_preview($input)
	{
		echo Report_Builder::load($input->type->string())
			->setValuesFromRequest($input->value->array())
			->outputSheet();
		exit;
	}

	function action_exportcsv($input)
	{
		echo Report_Builder::load($input->type->string())
			->setValuesFromRequest(json_decode(urldecode($input->value->string())))
			->outputCSV(true);
		exit;
	}

	function action_wikidata($input)
	{
		echo Report_Builder::load($input->type->string())
			->setValuesFromRequest($input->value->string())
			->outputWikiData();
		exit;
	}
}

