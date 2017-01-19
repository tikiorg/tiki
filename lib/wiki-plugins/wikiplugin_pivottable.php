<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin-pivottable.php 57956 2016-03-17 19:58:12Z jonnybradley $

function wikiplugin_pivottable_info()
{
	return array(
		'name' => tr('Pivot table'),
		'description' => tr('Create and display data in pivot table for reporting'),
		'prefs' => array('wikiplugin_pivottable'),
		'body' => tra('Leave one space in the box below to allow easier editing of current values with the plugin popup helper later on'),
		'format' => 'html',
		'iconname' => 'table',
		'introduced' => 10,
		'params' => array(
			'data' => array(
				'name' => tr('Data source'),
				'description' => tr('For example tracker:1'),
				'required' => true,
				'default' => 0,
				'filter' => 'text',
				'profile_reference' => 'tracker',
				'separator' => ':',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tr('Width of pivot table. Units: % or px.'),
				'since' => '',
				'filter' => 'word',
				'default' => '100%',
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tr('Height of pivot table. Units: px'),
				'since' => '',
				'filter' => 'word',
				'default' => '400px',
			),
			'rows' => array(
				'required' => false,
				'name' => tra('Pivot table Rows'),
				'description' => tr('Will be derived from data, if left blank, first parameter found in data will be used. ') . ' ' . tr('Use permanentNames in case of tracker fields.') . ' ' . tr('Separated by colon (:) if more than one.'),
				'since' => '',
				'filter' => 'text',
				'default' => '',
			),
			'cols' => array(
				'required' => false,
				'name' => tra('Pivot table Columns'),
				'description' => tr('Will be derived from data, if left blank, second parameter found in data will be used.') . ' ' . tr('Use permanentNames in case of tracker fields.') . ' ' . tr('Separated by colon (:) if more than one.'),
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
					array('text' => tra('Scatter Chart'), 'value' => 'Scatter Chart'),
					array('text' => tra('Treemap'), 'value' => 'Treemap')
				)
			),
			'aggregatorName' => array(
				'name' => tr('Aggregator Name'),
				'description' => tr('Function to apply on the numeric values from the variables selected.'),
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
				'name' => tr('Values'),
				'description' => tr('Variable with numeric values or tracker field permNames, on which the formula from the aggregator is applied. It can be left empty if aggregator is related to Counts.') . ' ' . tr('Use permanentNames in case of tracker fields, separated by : in case of multiple fields function.'),
				'since' => '',
				'required' => false,
				'filter' => 'text',
			),
			'inclusions' => array(
				'name' => tr('Inclusions'),
				'description' => tr('Filter values for fields in rows or columns. Contains JSON encoded object of arrays of strings.'),
				'since' => '',
				'required' => false,
				'filter' => 'text',
			),
			'menuLimit' => array(
				'name' => tr('Filter list limit'),
				'description' => tr('Pivottable menuLimit option override - number of entries to consider the menu list too big when filtering on a particular column or row.'),
				'since' => '16.2',
				'required' => false,
				'filter' => 'digits',
			),
			'aggregateDetails' => array(
				'name' => tr('Aggregate details'),
				'description' => tr('When enabled, clicking a table cell will popup all items that were aggregated into that cell. Specify the name of the field to use to display the details.'),
				'since' => '16.2',
				'required' => false,
				'filter' => 'text',
			),
		),
	);
}

function wikiplugin_pivottable($data, $params)
{
	
	//included globals for permission check
	global $prefs, $page, $wikiplugin_included_page;

	//checking if vendor files are present 
	if (!file_exists('vendor/nicolaskruchten/pivottable/')) {
		return WikiParser_PluginOutput::internalError(tr('Missing required files, please make sure plugin files are installed at vendor/nicolaskruchten/pivottable. <br/><br /> To install, please run composer or download from following url:<a href="https://github.com/nicolaskruchten/pivottable/archive/master.zip" target="_blank">https://github.com/nicolaskruchten/pivottable/archive/master.zip</a>'));
	}

	static $id = 0;
	$id++;

	$headerlib = TikiLib::lib('header');
	$headerlib->add_cssfile('vendor/nicolaskruchten/pivottable/dist/pivot.css');
	$headerlib->add_jsfile('vendor/nicolaskruchten/pivottable/dist/pivot.js', true);
	$headerlib->add_jsfile('vendor/nicolaskruchten/pivottable/dist/c3_renderers.js', true);
	$headerlib->add_jsfile('lib/jquery_tiki/wikiplugin-pivottable.js', true);
	
	//checking data type
	if( empty($params['data']) || !is_array($params['data']) ) {
		return WikiParser_PluginOutput::internalError(tr('Missing data parameter with format: source:ID, e.g. tracker:1'));
	}
	if($params['data'][0] === "tracker") {
		$trackerId = $params['data'][1];
	} else {
		$trackerId = 0;
	}
	
	$definition = Tracker_Definition::get($trackerId);
	if (! $definition) {
		return WikiParser_PluginOutput::userError(tr('Tracker data source not found.'));
	}

	$perms = Perms::get(array('type' => 'tracker', 'object' => $trackerId));

	$fields = $definition->getFields();

	if( !$perms->admin_trackers ) {
		$hasFieldPermissions = false;
		foreach( $fields as $key => $field ) {
			$isHidden = $field['isHidden'];
			$visibleBy = $field['visibleBy'];

			if( $isHidden != 'n' || !empty($visibleBy) ) {
				$hasFieldPermissions = true;
			}

			if ($isHidden == 'c') {
				// creators can see their own items coming from the search index
			} elseif ($isHidden == 'y') {
				// Visible by administrator only
				unset($fields[$key]);
			} elseif( !empty($visibleBy) ) {
				// Permission based on visibleBy apply
				$commonGroups = array_intersect($visibleBy, $perms->getGroups());
				if( count($commonGroups) == 0 ) {
					unset($fields[$key]);
				}
			}
		}
		if( !$hasFieldPermissions && !$perms->view_trackers ) {
			return WikiParser_PluginOutput::userError(tr('You do not have rights to view tracker data.'));
		}
	}

	$fields[] = array(
		'name' => 'creation_date',
		'permName' => 'creation_date',
		'type' => 'f'
	);
	$fields[] = array(
		'name' => 'modification_date',
		'permName' => 'modification_date',
		'type' => 'f'
	);
	$fields[] = array(
		'name' => 'tracker_status',
		'permName' => 'tracker_status',
		'type' => 't'
	);
	
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

	$query = new Search_Query;
	$query->filterType('trackeritem');
	$query->filterContent($trackerId, 'tracker_id');

	$unifiedsearchlib = TikiLib::lib('unifiedsearch');
	$unifiedsearchlib->initQuery($query);

	$matches = WikiParser_PluginMatcher::match($data);

	$builder = new Search_Query_WikiBuilder($query);
	$builder->apply($matches);

	if (! $index = $unifiedsearchlib->getIndex()) {
		return WikiParser_PluginOutput::userError(tr('Unified search index not found.'));
	}

	$query->setRange(0, TikiLib::lib('trk')->get_nb_items($trackerId));

	$result = $query->search($index);
	$result->setId('wppivottable-' . $id);

	$resultBuilder = new Search_ResultSet_WikiBuilder($result);
	$resultBuilder->apply($matches);

	$columnsListed = false;
	foreach( $matches as $match ) {
		if( $match->getName() == 'display' || $match->getName() == 'column' ) {
			$columnsListed = true;
		}
	}
	if( $columnsListed ) {
		$plugin = new Search_Formatter_Plugin_ArrayTemplate($data);
		$usedFields = array_keys($plugin->getFields());
		foreach( $fields as $key => $field ) {
			if( !in_array('tracker_field_'.$field['permName'], $usedFields)
				&& !in_array($field['permName'], $usedFields) ) {
				unset($fields[$key]);
			}
		}
		$fields = array_values($fields);
		$plugin->setFieldPermNames($fields);
	} else {
		$plugin = new Search_Formatter_Plugin_ArrayTemplate(implode("", array_map(
			function($f){
				if( in_array($f['permName'], array('creation_date', 'modification_date', 'tracker_status')) ) {
					return '{display name="'.$f['permName'].'" default=" "}';
				} else {
					return '{display name="tracker_field_'.$f['permName'].'" default=" "}';
				}
			}, $fields)));
		$plugin->setFieldPermNames($fields);
	}

	$builder = new Search_Formatter_Builder;
	$builder->setId('wppivottable-' . $id);
	$builder->setCount($result->count());
	$builder->apply($matches);
	$builder->setFormatterPlugin($plugin);

	$formatter = $builder->getFormatter();
	$entries = $formatter->getPopulatedList($result, false);
	$entries = $plugin->renderEntries($entries);

	$pivotData = array();
	foreach( $entries as $entry ) {
		$row = array();
		foreach( $entry as $fieldName => $value ) {
			if( $field = $definition->getFieldFromPermName($fieldName) ) {
				$row[$field['name']] = $value;
			} else {
				$row[$fieldName] = $value;
			}
		}
		$pivotData[] = $row;
	}
	
	//translating permName to field name for columns and rows
	$cols = array();
	if (!empty($params['cols'])) {
		$colNames = explode(":", $params['cols']);
		foreach($colNames as $colName)
		{
			if( $field = $definition->getFieldFromPermName(trim($colName)) ) {
				$cols[] = $field['name'];
			} else {
				$cols[] = $colName;
			}
		}
	} elseif( !empty($fields) ) {
		$cols[] = $fields[0]['name'];
	}
	
	$rows = array();
	if (!empty($params['rows'])) {
		$rowNames = explode(":", $params['rows']);
		foreach($rowNames as $rowName)
		{
			if( $field = $definition->getFieldFromPermName(trim($rowName)) ) {
				$rows[] = $field['name'];
			} else {
				$rows[] = $rowName;
			}
		}
	} elseif( isset($fields[1]) ) {
		$rows[] = $fields[1]['name'];
	}

	$vals = array();
	if (!empty($params['vals'])) {
		$valNames = explode(":", $params['vals']);
		foreach($valNames as $valName)
		{
			if( $field = $definition->getFieldFromPermName(trim($valName)) ) {
				$vals[] = $field['name'];
			} else {
				$vals[] = $valName;
			}
		}
	}

	$inclusions = !empty($params['inclusions']) ? $params['inclusions'] : '{}';

	// parsing array to hold permNames mapped with field names for save button
	// and list of date fields for custom sorting
	$fieldsArr=array();
	$dateFields = array();
	foreach($fields as $field)
	{
		$fieldsArr[$field['name']] = $field['permName'];
		if( $field['type'] == 'f' ) {
			$dateFields[] = $field['name'];
		}
	}

	if (!empty($params['aggregateDetails'])) {
		if ($field = $definition->getFieldFromPermName(trim($params['aggregateDetails']))) {
			$aggregateDetails = $field['name'];
		} else {
			$aggregateDetails = trim($params['aggregateDetails']);
		}
	} else {
		$aggregateDetails = null;
	}
	
	//checking if user can see edit button
	if (!empty($wikiplugin_included_page)) {
		$sourcepage = $wikiplugin_included_page;
	} else {
		$sourcepage = $page;
	}
	//checking if user has edit permissions on the wiki page using the current permission library to obey global/categ/object perms
	$objectperms = Perms::get( array( 'type' => 'wiki page', 'object' => $sourcepage ) );
	if( $objectperms->edit ) {
		$showControls = TRUE;
	} else {
		$showControls = FALSE;
	}

	$out = str_replace( array('~np~', '~/np~'), '', $formatter->renderFilters() );

	$smarty = TikiLib::lib('smarty');
	$smarty->assign('pivottable', array(
		'id' => 'pivottable' . $id,
		'trows'=>$rows,
		'tcolumns'=>$cols,
		'trackerId' => $trackerId,
		'data' => $pivotData,
		'rendererName'=>$rendererName,
		'aggregatorName'=>$aggregatorName,
		'vals'=>$vals,
		'width'=>$width,
		'height'=>$height,
		'showControls'=>$showControls,
		'page'=>$sourcepage,
		'fieldsArr'=>$fieldsArr,
		'dateFields' => $dateFields,
		'inclusions' => $inclusions,
		'menuLimit' => empty($params['menuLimit']) ? null : $params['menuLimit'],
		'aggregateDetails' => $aggregateDetails,
		'index'=>$id
	));
	
	$out .= $smarty->fetch('wiki-plugins/wikiplugin_pivottable.tpl');

	return $out;
}

