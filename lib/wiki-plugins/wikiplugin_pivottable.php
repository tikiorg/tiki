<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

function wikiplugin_pivottable_info()
{
	return array(
		'name' => tr('Pivot table'),
		'description' => tr('Create and display data in pivot table for reporting'),
		'prefs' => array('wikiplugin_pivottable'),
		'format' => 'html',
		'iconname' => 'table',
		'introduced' => 10,
		'params' => array(
			'data' => array(
				'name' => tr('Fetch data from'),
				'description' => tr('For example trackerId_1'),
			    'required' => true,
				'default' => 0,
				'filter' => 'word',
				
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tr('Width of pivot table'),
				'since' => '',
				'filter' => 'word',
				'default' => '100%',
				
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tr('Height of pivot table'),
				'since' => '',
				'filter' => 'word',
				'default' => '400px',
				
			),
			'rows' => array(
				'required' => false,
				'name' => tra('Pivot table Rows'),
				'description' => tr('Will be drived from data, if left blank, first parameter found in data will be used'),
				'since' => '',
				'filter' => 'text',
				'default' => '',
				
			),
			'cols' => array(
				'required' => false,
				'name' => tra('Pivot table Columns'),
				'description' => tr('Will be drived from data, if left blank, second parameter found in data will be used'),
				'since' => '',
				'filter' => 'text',
				'default' => '',
				
			),
			'rendererName' => array(
				'name' => tr('Renderer Name'),
				'description' => tr('Options: Table, Area Chart, Bar chart'),
				'since' => '',
				'required' => false,
				'filter' => 'text',
			),
			'aggregatorName' => array(
				'name' => tr('Aggregator Name'),
				'description' => tr('Options: Count, Average'),
				'since' => '',
				'required' => false,
				'filter' => 'text',
			),
			'vals' => array(
				'name' => tr('Vals'),
				'description' => tr(''),
				'since' => '',
				'required' => false,
				'filter' => 'word',
			),
			
			
			
		),
	);
}

function wikiplugin_pivottable($data, $params)
{
	
    if (!file_exists('vendor/etdsolutions/pivottable/')) {
		return WikiParser_PluginOutput::internalError(tr('Missing required files, please make sure plugin files are installed at vendor/etdsolutions/pivottable. <br/><br /> To install, please run composer or download from following url:<a href="https://github.com/nicolaskruchten/pivottable/archive/master.zip" target="_blank">https://github.com/nicolaskruchten/pivottable/archive/master.zip</a>'));
	}

	
	static $id = 0;
	$headerlib = TikiLib::lib('header');
	$headerlib->add_cssfile('vendor/etdsolutions/pivottable/pivot.css');
	$headerlib->add_cssfile('https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.11/c3.min.css');
	$headerlib->add_jsfile('vendor/etdsolutions/pivottable/pivot.js', true);
	$headerlib->add_jsfile('vendor/etdsolutions/pivottable/c3_renderers.js', true);
	$headerlib->add_jsfile('https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js');
	$headerlib->add_jsfile('https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.11/c3.min.js');

	//checking data type
	
	$dataId=split("_",$params['data']);
	
	if($dataId[0]=="trackerId")
	  {
		  $trackerId=$dataId[1];
		  
		  }
	   

	$jit = new JitFilter($params);
	
	$definition = Tracker_Definition::get($trackerId);
	$itemObject = Tracker_Item::newItem($trackerId);
	if (! $definition) {
		return WikiParser_PluginOutput::userError(tr('Tracker not found.'));
	}

	if (!empty($params['rendererName'])) {
	    $rendererName=$params['rendererName'];	
	} else {
		$rendererName="Table";	
	}
	
	if (!empty($params['aggregatorName'])) {
	    $aggregatorName=$params['aggregatorName'];	
	} else {
		$aggregatorName="Count";	
	}
	
	if (!empty($params['width'])) {
	    $width=$params['width'];	
	} else {
		$width="100%";	
	}
	
	if (!empty($params['height'])) {
	    $height=$params['height'];	
	} else {
		$height="1000px";	
	}
	
	if (!empty($params['cols'])) {
	    $cols=$params['cols'];	
	} else {
		$cols="";	
	}
	
	if (!empty($params['rows'])) {
	    $rows=$params['rows'];	
	} else {
		$rows="";	
	}
	
	
	
	
	
	$smarty = TikiLib::lib('smarty');
	$smarty->assign(
		'pivottable',
		array(
			'id' => 'pivottable' . ++$id,
			'trows'=>$rows,
			'tcolumns'=>$cols,
			'trackerId' => $trackerId,
			'body' => $data,
			'rendererName'=>$rendererName,
			'aggregatorName'=>$aggregatorName,
			'width'=>$width,
			'height'=>$height,

		)
	);
	
	
	return $smarty->fetch('wiki-plugins/pivottable.tpl');
}



function wikiplugin_pivottable_get_resources($field)
{
	$db = TikiDb::get();

	return $db->fetchAll('SELECT DISTINCT LOWER(value) as id, value as name FROM tiki_tracker_item_fields WHERE fieldId = ? ORDER BY  value', $field['fieldId']);
}

