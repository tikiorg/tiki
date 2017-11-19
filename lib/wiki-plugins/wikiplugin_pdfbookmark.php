<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id $

function wikiplugin_pdfbookmark_info()
{
			return [
				'name' => 'PluginPDF Bookmark',
				'documentation' => 'PluginPDFBookmark',
				'description' => tra('Manual bookmark entry for PDF'),
				'tags' => [ 'advanced' ],
				'iconname' => 'pdf',
				'prefs' => [ 'wikiplugin_pdf' ],
				'introduced' => 18,
				'params' => [
					'content' => [
						'name' => tra('Bookmark Label'),
						'description' => tra(''),
						'tags' => ['advanced'],
						'type' => 'text',
						'default' => ''
					],
					'level' => [
						'name' => tra('Bookmark level'),
						'description' => tra(''),
						'tags' => ['advanced'],
						'type' => 'text',
						'default' => '0',
						'options' => [
							['text' => '0','value' => '0'],
							['text' => '1','value' => '1'],
							['text' => '2','value' => '2'],
						],
					],
				]
			];
}
function wikiplugin_pdfbookmark($data, $params)
{
	foreach ($params as $paramName => $param) {
		$paramList .= $paramName . "='" . $param . "' ";
	}
	return "<bookmark " . $paramList . " />";
}
