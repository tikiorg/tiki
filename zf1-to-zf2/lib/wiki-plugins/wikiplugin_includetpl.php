<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_includetpl_info()
{
	return array(
		'name' => tra('Include TPL files to the wiki page'),
		'description' => tra('Include tpl files to all the wiki pages.'),
		'format' => 'html',
		'validate' => 'all',
		'filter' => 'wikicontent',
		'tags' => array('advanced'),
		'params' => array(
			'filename' => array(
				'name' => tr('TPL file name'),
				'description' => tr('If you need to include tpl files.'),
				'required' => false,
				'filter' => 'text'
			),
			'values' => array(
				'name' => tr('values passed to the TPL'),
				'description' => tr('Values can be passed to tpl file eg: values=var1:val1&var2:val2'),
				'required' => false,
				'filter' => 'text'
			),
		),
	);
}

function wikiplugin_includetpl($data, $params)
{
	global $smarty;
	if(stripos($params["values"],'&')){
		$paramvalues = explode('&', $params["values"]);
		foreach ($paramvalues as $key => $value) {
			$tempvalues = explode(':', $value);
			$defvalues[$tempvalues[0]]=$tempvalues[1];
		}
	}else{
		$tempvalues = explode(':', $params["values"]);
		$defvalues[$tempvalues[0]]=$tempvalues[1];
	}

	$smarty->assign('values', $defvalues);
	return $smarty->fetch($params["filename"]);
}
