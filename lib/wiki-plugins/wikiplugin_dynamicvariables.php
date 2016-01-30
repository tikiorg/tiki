<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_dynamicvariables.php 44444 2013-01-05 21:24:24Z changi67 $

function wikiplugin_dynamicvariables_info()
{
	return array(
		'name' => tra('List of dynamic variables'),
		'documentation' => 'PluginDynamicvariables',
		'description' => tra('Show dynamic variables and their values.'),
		'prefs' => array( 'wikiplugin_dynamicvariables' ),
		'iconname' => 'code',
		'introduced' => 15,
		'params' => array(
			'layout' => array(
				'required' => false,
				'name' => tra('Layout'),
				'description' => tra('Set to table to show results in a table (not shown in a table by default)'),
				'since' => '15.0',
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Table'), 'value' => 'table')
				)
			),
			'linesep' => array(
				'required' => false,
				'name' => tra('Separator'),
				'description' => tra('String to use between elements of the list if table layout is not used'),
				'since' => '15.0',
				'filter' => 'xss',
				'default' => ', ',
			),
/*
			'sort' => array(
				'required' => false,
				'name' => tra('Sort order'),
				'description' => tra('Set to sort in ascending or descending order (unsorted by default'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Ascending'), 'value' => 'asc'), 
					array('text' => tra('Descending'), 'value' => 'desc')
				)
			),
*/
		)
	);
}

function wikiplugin_dynamicvariables($data, $params)
{
	global $prefs;
	$tikilib = TikiLib::lib('tiki');

	$layout = isset($params['layout']) ? $params['layout'] : '';
	$linesep = isset($params['linesep']) ? $params['linesep'] : '';

	if (!isset($linesep)) {
		$linesep='<br>';
	}
	if (!isset($max)) {
		$numRows = -1;
	} else {
		$numRows = (int) $max;
	}

	$filter = '';
	$bindvars = array();

	$pre=''; $post='';
	$oparens = '(';
	$cparens = ')';
	if (isset($layout) && $layout=='table') {
		$pre='<table class=\'table table-striped table-hover\' id=\''.$tikilib->now.'\'><tr>'
			. '<th>'.tra('Parsed result').'</th>'
			. '<th>'.tra('Syntax').'</th>'
			. '<th>'.tra('Value per database').'</th>'
			. '</tr><tr><td>';
		$linesep = '</td></tr><tr><td>';
		$sep = '</td><td>';
		$post='</td></tr></table>';
		$oparens = '';
		$cparens = '';
	}

	$query = 'SELECT * FROM `tiki_dynamic_variables` ' . $filter ; 

	$result = $tikilib->query($query, $bindvars, $numRows);
	$ret = array();
	$tag = '';
	if ($prefs['wiki_dynvar_style'] == 'single') {
		$tag = '%';
	} elseif ($prefs['wiki_dynvar_style'] == 'double') {
		$tag = '%%';
	}
	while ($row = $result->fetchRow()) {
		$res = $tag . $row['name'] . $tag . $sep . ' ' . $oparens . '~np~' . $tag . $row['name'] . $tag . '~/np~'
			. $cparens . ' ' . $sep . $row['data'] ;
		$ret[] = $res;
	}

	return $pre.implode($linesep, $ret).$post;
}
