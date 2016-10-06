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
				'description' => tr('For example tracker:1'),
			    'required' => true,
				'default' => 0,
				'filter' => 'text',
				
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
				'description' => tr('Display format of data'),
				'since' => '',
				'required' => false,
				'filter' => 'text',
				'default' => 'Table',
				'options' => array(
					array('text' => 'Table', 'value' => 'Table'),
					array('text' => tra('Table Barchart'), 'value' => 'Table Barchart'),
					array('text' => tra('Heatmap'), 'value' => 'Heatmap'),
				    array('text' => tra('Row Heatmap'), 'value' => 'Row Heatmap'),
					array('text' => tra('Col Heatmap'), 'value' => 'Col Heatmap'),
					array('text' => tra('Line Chart'), 'value' => 'Line Chart'),
					array('text' => tra('Bar Chart'), 'value' => 'Bar Chart'),
					array('text' => tra('Stacked Bar Chart'), 'value' => 'Stacked Bar Chart'),
					array('text' => tra('Area Chart'), 'value' => 'Area Chart'),
					array('text' => tra('Scatter Chart'), 'value' => 'Scatter Chart')
				)
				
			),
			'aggregatorName' => array(
				'name' => tr('Aggregator Name'),
				'description' => tr('Options: Count, Average'),
				'since' => '',
				'required' => false,
				'filter' => 'text',
				'default' => 'Count',
                'options' => array(
					array('text' => 'Count', 'value' => 'Count'),
					array('text' => tra('Count Unique Values'), 'value' => 'Count Unique Values'),
					array('text' => tra('List Unique Values'), 'value' => 'List Unique Values'),
				    array('text' => tra('Sum'), 'value' => 'Sum'),
					array('text' => tra('Integer Sum'), 'value' => 'Integer Sum'),
					array('text' => tra('Average'), 'value' => 'Average'),
					array('text' => tra('Minimum'), 'value' => 'Minimum'),
					array('text' => tra('Maximum'), 'value' => 'Maximum'),
					array('text' => tra('Sum over Sum'), 'value' => 'Sum over Sum'),
					array('text' => tra('80% Upper Bound'), 'value' => '80% Upper Bound'),
					array('text' => tra('80% Lower Bound'), 'value' => '80% Lower Bound'),
					array('text' => tra('Sum as Fraction of Total'), 'value' => 'Sum as Fraction of Total'),
					array('text' => tra('Sum as Fraction of Rows'), 'value' => 'Sum as Fraction of Rows'),
					array('text' => tra('Sum as Fraction of Columns'), 'value' => 'Sum as Fraction of Columns'),
					array('text' => tra('Count as Fraction of Total'), 'value' => 'Count as Fraction of Total'),
					array('text' => tra('Count as Fraction of Rows'), 'value' => 'Count as Fraction of Rows'),
					array('text' => tra('Count as Fraction of Columns'), 'value' => 'Count as Fraction of Columns')
					
					
				)
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
	$headerlib->add_jsfile('vendor/etdsolutions/pivottable/pivot.js', true);
	$headerlib->add_jsfile('vendor/etdsolutions/pivottable/c3_renderers.js', true);

	//checking data type
	
	$dataId=split(":",$params['data']);
	if($dataId[0]=="tracker")
	  {
		  $trackerId=$dataId[1];
		  
		  }
	   

	$jit = new JitFilter($params);
	
	$definition = Tracker_Definition::get($trackerId);
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
	
	//checking if rows and cols are passed
	if(empty($params['cols']) || empty($params['rows']))
	   {
		   $fields=$definition->getFields();
		   
		   }
	
	//translating permName to field name for columns and rows
	
	if (!empty($params['cols'])) {
		$cols='';
		$colNames=split(",",$params['cols']);
		foreach($colNames as $colName)
		{
		   	
		  $field = $definition->getFieldFromPermName(trim($colName));
		  if($field)
		  {
			 if($cols!='')
			   $cols.=', ';
	        $cols.='"'.$field['name'].'"';
		  }
		}
		
	} else {
		$cols='"'.$fields[0]['name'].'"';	
	}
	
	if (!empty($params['rows'])) {
	    $rows='';
		$rowNames=split(",",$params['rows']);
		foreach($rowNames as $rowName)
		{
		   	
		  $field = $definition->getFieldFromPermName(trim($rowName));
		  if($field)
		  {
			 if($rows!='')
			   $rows.=', ';
	        $rows.='"'.$field['name'].'"';
		  }
		}	
	} else {
		$rows='"'.$fields[1]['name'].'"';	
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

