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

