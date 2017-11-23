<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

function wikiplugin_pdfpage_info()
{
	//including prefs to set global print settings as default value of parameters
	global $prefs;
	 return [
				'name' => 'PDFPage',
				'documentation' => 'PDFPage',
				'description' => tra('Insert page with new settings in pdf'),
				'tags' => [ 'advanced' ],
				'body' => tra('Page Content'),
				'prefs' => [ 'wikiplugin_pdf' ],
				'format' => 'wiki',
				'iconname' => 'pdf',
				'introduced' => 18,
				'params' => [

					'orientation' => [
						'name' => tra('PDF Orientation'),
						'description' => tra('Landscape or Portrait'),
						'type' => 'list',
						'default' => $prefs['print_pdf_mpdf_orientation'],
						'options' => [
							['text' => 'Default','value' => ''],
							['text' => 'Portrait','value' => 'P'],
							['text' => 'Landscape','value' => 'L'],
						],

					],
					'pagesize' => [
					'name' => tra('PDF page size'),
					'description' => tra('ISO Standard sizes: A0, A1, A2, A3, A4, A5 or North American paper sizes: Letter, Legal, Tabloid/Ledger (for ledger, select landscape orientation)'),
					'type' => 'list',
					'options' => [
						['text' => 'Default','value' => ''],
						['text' => 'Letter','value' => 'Letter'],
						['text' => 'Legal','value' => 'Legal'],
						['text' => 'Tabloid/Ledger','value' => 'Tabloid/Ledger'],
						['text' => 'A0','value' => 'A0'],
						['text' => 'A1','value' => 'A1'],
						['text' => 'A2','value' => 'A2'],
						['text' => 'A3','value' => 'A3'],
						['text' => 'A4','value' => 'A4'],
						['text' => 'A5','value' => 'A5'],
						['text' => 'A6','value' => 'A6']
						]
					],

					'header' => [
						'name' => tra('PDF header text'),
						'description' => tra('Format: <code>Left text| Center Text | Right Text</code>. Possible values: <code>Custom text</code>, <code>{PAGENO}</code>, <code>{DATE j-m-Y}</code>, <code> Page {PAGENO} of {NB}</code>. Set header value as <code>off</code>, to turn off header from page'),
						'tags' => ['basic'],
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_header'],
						'shorthint' => 'Left text |Center Text| Right Text'
					],
					'footer' => [
						'name' => tra('PDF footer text'),
						'description' => tra('Possible values: <code>Custom text</code>, <code>{PAGENO}</code>, <code>{DATE j-m-Y}</code>. For example: <code>{PAGETITLE}|Center Text|{PAGENO}</code>, <code> Page {PAGENO} of {NB}</code>. Set footer value as <code>off</code>, to remove footer from page'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_footer'],
					],
					'margin_left' => [
						'name' => tra('Left margin'),
						'description' => tra('Numeric value.For example 10'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_margin_left'],
						'size' => '2',
						'filter' => 'digits',
					],
					'margin_right' => [
						'name' => tra('Right margin'),
						'description' => tra('Numeric value, no need to add px. For example 10'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_margin_right'],
						'size' => '2',
						'filter' => 'digits',
					],
					'margin_top' => [
						'name' => tra('Top margin'),
						'description' => tra('Numeric value, no need to add px. For example 10'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_margin_top'],
						'size' => '2',
						'filter' => 'digits',
					],
					'margin_bottom' => [
						'name' => tra('Bottom margin'),
						'description' => tra('Numeric value, no need to add px. For example 10'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_margin_bottom'],
						'size' => '2',
						'filter' => 'digits',
					],
					'margin_header' => [
						'name' => tra('Header margin from top of document'),
						'description' => tra('Only applicable if header is set. Numeric value only, no need to add px.Warning: Header can overlap text if top margin is not set properly'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_margin_header'],
						'size' => '2',
						'filter' => 'digits',

					],
					'margin_footer' => [
						'name' => tra('Footer margin from bottom of document'),
						'description' => tra('Only applicable if footer is set.Numeric value only, no need to add px. Warning: Footer can overlap text if bottom margin is not set properly'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_margin_footer'],
						'size' => '2',
						'filter' => 'digits',
					],
					'hyperlinks' => [
						'name' => tra('Hyperlink behaviour in PDF'),
						'description' => tra(''),
						'tags' => ['advanced'],
						'type' => 'list',
						'default' => '',
						'options' => [
							['text' => 'Default','value' => ''],
							['text' => 'Off (Links will be removed)','value' => 'off'],
							['text' => 'Add as footnote (Links will be listed at end of document)','value' => 'footnote'],
						]
					],
					'columns' => [
						'name' => tra('Number of columns'),
						'description' => tra(''),
						'tags' => ['advanced'],
						'type' => 'list',
						'default' => '',
						'options' => [
							['text' => 'Default - 1 Column','value' => ''],
							['text' => '2 Columns','value' => '2'],
							['text' => '3 Columns','3'],
							['text' => '4 Columns','4'],
						]
					],
					'watermark' => [
						'name' => tra('Watermark text for this page. Set value as "off", to turn off watermark of the page'),
						'description' => tra('Watermark text value, for example: Confidential, Draft etc.'),
						'type' => 'text',
						'default' => '',
					],
					'watermark_image' => [
						'name' => tra('Watermark image, enter full url'),
						'description' => tra('To turn off watermark image on the page, set value as <code>off</code>'),
						'type' => 'text',
						'default' => 'Full URL of watermark image',
					],
					'background' => [
						'name' => tra('Page background color'),
						'description' => tra('Enter color code'),
						'type' => 'text',
						'default' => '',
					],
					'background_image' => [
						'name' => tra('Page background image'),
						'description' => tra('Enter complete URL'),
						'type' => 'text',
						'default' => '',
					],

				],
		];
}

function wikiplugin_pdfpage($data, $params)
{
	//included globals to check mpdf selection as pdf generation engine
	global $prefs;

	//checking if mdpf is default pdf generation engine, since plugin is only set for mpdf.
	if ($prefs['print_pdf_from_url'] != 'mpdf') {
		return WikiParser_PluginOutput::internalError(tr('For pluginPDF, please select mpdf as default PDF engine from Print Settings.'));
	}
	$paramList = '';
	//creating string of data paramaters set by user
	foreach ($params as $paramName => $param) {
		$paramList .= $paramName . "='" . $param . "' ";
	}
	return "<pdfpage " . $paramList . ">" . $data . "</pdfpage>";
}
