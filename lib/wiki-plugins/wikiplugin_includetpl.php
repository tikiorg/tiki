<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_includetpl_info()
{
	return [
		'name' => tra('Include Template File'),
		'description' => tra('Include a template file in a page'),
		'format' => 'html',
		'validate' => 'all',
		'filter' => 'wikicontent',
		'tags' => ['advanced'],
		'introduced' => 15,
		'iconname' => 'code_file',
		'params' => [
			'filename' => [
				'name' => tr('TPL file name'),
				'description' => tr('If you need to include Smarty template files.'),
				'since' => '15.0',
				'required' => false,
				'filter' => 'text'
			],
			'values' => [
				'name' => tr('Values passed to the TPL'),
				'description' => tr(
					'Values to be passed to tpl file, for example %0, which can then be accessed in the Smarty template file as %1',
					'<code>values=var1:val1&var2:val2</code>',
					'<code>{$values.var1} and {$values.var2}</code>'
				),
				'since' => '15.0',
				'required' => false,
				'filter' => 'text'
			],
		],
	];
}

function wikiplugin_includetpl($data, $params)
{
	$smarty = TikiLib::lib('smarty');
	if (stripos($params["values"], '&')) {
		$paramvalues = explode('&', $params["values"]);
		foreach ($paramvalues as $key => $value) {
			$tempvalues = explode(':', $value);
			$defvalues[$tempvalues[0]] = $tempvalues[1];
		}
	} else {
		$tempvalues = explode(':', $params["values"]);
		$defvalues[$tempvalues[0]] = $tempvalues[1];
	}

	$smarty->assign('values', $defvalues);
	return $smarty->fetch($params["filename"]);
}
