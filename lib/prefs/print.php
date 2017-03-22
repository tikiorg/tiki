<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_print_list()
{
	return array(
		'print_pdf_from_url' => array(
			'name' => tra('PDF from URL'),
			'description' => tra('Using external tools, generate PDF documents from URLs.'),
			'type' => 'list',
			'options' => array(
				'none' => tra('Disabled'),
				'webkit' => tra('WebKit (wkhtmltopdf)'),
				'weasyprint' => tra('WeasyPrint'),
				'webservice' => tra('Webservice'),
				'mpdf' => tra('mPDF'),
			),
			'default' => 'none',
			'help' => 'PDF',
		),
		'print_pdf_webservice_url' => array(
			'name' => tra('Webservice URL'),
			'description' => tra('URL to a service that takes a URL as the query string and returns a PDF document.'),
			'type' => 'text',
			'size' => 50,
			'dependencies' => array('auth_token_access'),
			'default' => '',
		),
		'print_pdf_webkit_path' => array(
			'name' => tra('WebKit path'),
			'description' => tra('Full path to the wkhtmltopdf executable to generate the PDF document with.'),
			'type' => 'text',
			'size' => 50,
			'help' => 'wkhtmltopdf',
			'dependencies' => array('auth_token_access'),
			'default' => '',
		),
		'print_pdf_weasyprint_path' => array(
			'name' => tra('WeasyPrint path'),
			'description' => tra('Full path to the weasyprint executable to generate the PDF document with.'),
			'type' => 'text',
			'size' => 50,
			'help' => 'weasyprint',
			'dependencies' => array('auth_token_access'),
			'default' => '',
		),
		'print_pdf_mpdf_path' => array(
			'name' => tra('mPDF path'),
			'description' => tra('Path to of the mPDF install.'),
			'type' => 'text',
			'size' => 50,
			'help' => 'mPDF',
			'dependencies' => array('auth_token_access'),
			'default' => 'vendor_custom/mpdf/',
		),
		'print_pdf_mpdf_printfriendly' => array(
			'name' => tra('Print Friendly PDF'),
			'description' => tra('Useful for dark themes, Enabling this option will change theme background color to white and text / headings color to black. If turned off, theme colors will be retained in pdf'),
			'type' => 'flag',
			'default' => 'y'
			
		),
		
		'print_pdf_mpdf_orientation' => array(
			'name' => tra('PDF Orientation'),
			'description' => tra('Landscape or Portrait'),
			'tags' => array('advanced'),
			'type' => 'list',
			'options' => array(
				'P' => tra('Portrait'),
				'L' => tra('Landscape'),
			),
			'default' => 'P',
		),
		'print_pdf_mpdf_size' => array(
			'name' => tra('PDF page size'),
			'description' => tra('ISO Standard sizes: A0, A1, A2, A3, A4, A5 or North American paper sizes: Letter, Legal, Tabloid/Ledger (for ledger, select landscape orientation)'),
			'tags' => array('advanced'), 
			'type' => 'list',
			'options' => array(
				'Letter' => tra('Letter'),
				'Legal' => tra('Legal'),
				'Tabloid'=>tra('Tabloid/Ledger'),
				'A0' => tra('A0'),
				'A1' => tra('A1'),
				'A2' => tra('A2'),
				'A3' => tra('A3'),
				'A4' => tra('A4'),
				'A5' => tra('A5'),
				'A6' => tra('A6'),
			),
			'default' => 'A4',
		),
		'print_pdf_mpdf_header' => array(
			'name' => tra('PDF header text'),
			'description' => tra('Possible values, custom text, {PAGENO},{PAGETITLE},{DATE j-m-Y}'),
			'tags' => array('basic'),
			'type' => 'text',
			'default' => '',
			'shorthint'=>'Left text |Center Text| Right Text'
		),
		'print_pdf_mpdf_footer' => array(
			'name' => tra('PDF footer text'),
			'description' => tra('Possible values, custom text, {PAGENO}, {DATE j-m-Y} For example:Document Title|Center Text|{PAGENO}'),
			'tags' => array('basic'),
			'type' => 'text',
			'default' => '',
		),
		'print_pdf_mpdf_margin_left' => array(
			'name' => tra('Left margin'),
			'description' => tra('Numeric value.For example 10'),
			'tags' => array('advanced'),
			'type' => 'text',
			'default' => '10',
			'size' => '2',
			'filter' => 'digits',
			
		),
		'print_pdf_mpdf_margin_right' => array(
			'name' => tra('Right margin'),
			'description' => tra('Numeric value, no need to add px. For example 10'),
			'tags' => array('advanced'),
			'type' => 'text',
			'default' => '10',
			'size' => '2',
			'filter' => 'digits',
			
		),
		'print_pdf_mpdf_margin_top' => array(
			'name' => tra('Top margin'),
			'description' => tra('Numeric value, no need to add px. For example 10'),
			'tags' => array('advanced'),
			'type' => 'text',
			'default' => '10',
			'size' => '2',
			'filter' => 'digits',
			
		),
		'print_pdf_mpdf_margin_bottom' => array(
			'name' => tra('Bottom margin'),
			'description' => tra('Numeric value, no need to add px. For example 10'),
			'tags' => array('advanced'),
			'type' => 'text',
			'default' => '10',
			'size' => '2',
			'filter' => 'digits',
		
		),
		'print_pdf_mpdf_margin_header' => array(
			'name' => tra('Header margin from top of document'),
			'description' => tra('Only applicable if header is set. Numeric value, no need to add px. For example 10'),
			'tags' => array('advanced'),
			'type' => 'text',
			'default' => '5',
			'size' => '2',
			'filter' => 'digits',
			'shorthint' => tra('Warning: Header can overlap text if top margin is not set properly')
		),
		'print_pdf_mpdf_margin_footer' => array(
			'name' => tra('Footer margin from bottom of document'),
			'description' => tra('Only applicable if footer is set.Numeric value, no need to add px. For example 10'),
			'tags' => array('advanced'),
			'type' => 'text',
			'default' => '5',
			'size' => '2',
			'filter' => 'digits',
			'shorthint' => tra('Warning: Footer can overlap text if bottom margin is not set properly')
		),
		'print_pdf_mpdf_password' => array(
			'name' => tra('PDF password for viewing'),
			'description' => tra('Password protect generated PDF'),
			'tags' => array('advanced'),
			'type' => 'password',
			'default' => '',
		),
		'print_wiki_authors' => array(
			'name' => tra('Print wiki authors'),
			'description' => tra('Include wiki authors and date in wiki page print outs.'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_wiki',
			),
			'default' => 'n',
		),
		'print_original_url_wiki' => array(
			'name' => tra('Print original wiki URL'),
			'description' => tra('Include orginal wiki page URL in print outs.'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_wiki',
			),
			'default' => 'y',
		),
		'print_original_url_tracker' => array(
			'name' => tra('Print original tracker item URL'),
			'description' => tra('Include orginal wiki page URL in print outs.'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_trackers',
			),
			'default' => 'y',
		),
		'print_original_url_forum' => array(
			'name' => tra('Print original forum post URL'),
			'description' => tra('Include orginal forum post URL in print outs.'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_forums',
			),
			'default' => 'y',
		),
	);
}

