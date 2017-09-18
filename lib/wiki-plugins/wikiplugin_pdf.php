<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

function wikiplugin_pdf_info()
{
	//including prefs to set global print settings as default value of parameters
	global $prefs;
	 return array(
                'name' => 'PluginPDF',
                'documentation' => 'PluginPDF',
                'description' => tra('For customized pdf generation, to override global pdf settings.'),
                'tags' => array( 'basic' ),
                'prefs' => array( 'wikiplugin_pdf' ),
				'format' => 'html',
				'iconname' => 'pdf',
				'introduced' => 17,
                'params' => array(
					'printfriendly' => array(
						'name' => tra('Print Friendly PDF'),
						'description' => tra('Print friendly option will change theme background color to white and text /headings color to black. If set to \'n\', theme colors will be retained in pdf'),
						'type' => 'list',
						'default' => '',
						'options' => array(
							array('text'=>'Default','value'=>''),
							array('text'=>'Yes','value'=>'y'),
							array('text'=>'No','value'=>'n'),
						),			
					),
					'orientation' => array(
						'name' => tra('PDF Orientation'),
						'description' => tra('Landscape or Portrait'),
						'type' => 'list',
						'default'=>$prefs['print_pdf_mpdf_orientation'],
						'options' => array(
							array('text'=>'Default','value'=>''),
							array('text'=>'Portrait','value'=>'P'),
							array('text'=>'Landscape','value'=>'L'),
						),
						
					),
					'pagesize' => array(
					'name' => tra('PDF page size'),
					'description' => tra('ISO Standard sizes: A0, A1, A2, A3, A4, A5 or North American paper sizes: Letter, Legal, Tabloid/Ledger (for ledger, select landscape orientation)'),
					'type' => 'list',
					'options' => array(
						array('text'=>'Default','value'=>''),
						array('text'=>'Letter','value'=>'Letter'),
						array('text'=>'Legal','value'=>'Legal'),
						array('text'=>'Tabloid/Ledger','value'=>'Tabloid/Ledger'),
						array('text'=>'A0','value'=>'A0'),
						array('text'=>'A1','value'=>'A1'),
						array('text'=>'A2','value'=>'A2'),
						array('text'=>'A3','value'=>'A3'),
						array('text'=>'A4','value'=>'A4'),
						array('text'=>'A5','value'=>'A5'),
						array('text'=>'A6','value'=>'A6')
						)
					),
					'toc' => array(
						'name' => tra('Generate table of contents'),
						'description' => tra('Set if table of contents will be autogenerated before PDF content'),
						'type' => 'list',
						'default'=>$prefs['print_pdf_mpdf_toc'],
						'options' => array(
							array('text'=>'Default','value'=>''),
							array('text'=>'On','value'=>'y'),
							array('text'=>'Off','value'=>'n'),
						),			
					),
					'toclinks' => array(
						'name' => tra('Link TOC with content'),
						'description' => tra('Link TOC headings with content on PDF document'),
						'type' => 'list',
						'default'=>'n',
						'options' => array(
							array('text'=>'Default','value'=>''),
							array('text'=>'Yes','value'=>'y'),
							array('text'=>'No','value'=>'n'),
						),			
					),
					'tocheading' => array(
						'name' => tra('TOC heading'),
						'description' => tra('Heading to be appeared before table of content is printed'),
						'tags' => array('advanced'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_tocheading'],
						'shorthint'=>'For example:Table of contents'
					),
										
					'toclevels' => array(
						'name' => tra('TOC levels'),
						'description' => tra('Will be autopicked from content of document, for example:<code>H1|H2|H3</code>'),
						'tags' => array('advanced'),
						'type' => 'text',
						'default' => "H1|H2|H3",
						'shorthint'=>''
					),
					'pagetitle' => array(
						'name' => tra('Show Page title'),
						'description' => tra('Print wiki page title with pdf'),
						'tags' => array('advanced'),
						'type' => 'list',
						'default'=>'',
						'options' => array(
							array('text'=>'Default','value'=>''),
							array('text'=>'Yes','value'=>'y'),
							array('text'=>'No','value'=>'n')
						)	
					),
					'header' => array(
						'name' => tra('PDF header text'),
						'description' => tra('Format: <code>Left text| Center Text | Right Text</code>. Possible values,<code>custom text</code>, {PAGENO},{PAGETITLE},{DATE j-m-Y}.'),
						'tags' => array('basic'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_header'],
						'shorthint'=>'Left text |Center Text| Right Text'
					),
					'footer' => array(
						'name' => tra('PDF footer text'),
						'description' => tra('Possible values, custom text, {PAGENO}, {DATE j-m-Y} For example:<code>{PAGETITLE}|{DATE j-m-Y}|{PAGENO}</code>'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_footer'],
					),
					'margin_left' => array(
						'name' => tra('Left margin'),
						'description' => tra('Numeric value.For example 10'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_margin_left'],
						'size' => '2',
						'filter' => 'digits',
					),
					'margin_right' => array(
						'name' => tra('Right margin'),
						'description' => tra('Numeric value, no need to add px. For example 10'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_margin_right'],
						'size' => '2',
						'filter' => 'digits',
					),
					'margin_top' => array(
						'name' => tra('Top margin'),
						'description' => tra('Numeric value, no need to add px. For example 10'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_margin_top'],
						'size' => '2',
						'filter' => 'digits',
					),
					'margin_bottom' => array(
						'name' => tra('Bottom margin'),
						'description' => tra('Numeric value, no need to add px. For example 10'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_margin_bottom'],
						'size' => '2',
						'filter' => 'digits',
					),
					'margin_header' => array(
						'name' => tra('Header margin from top of document'),
						'description' => tra('Only applicable if header is set. Numeric value only, no need to add px.Warning: Header can overlap text if top margin is not set properly'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_margin_header'],
						'size' => '2',
						'filter' => 'digits',
						
					),
					'margin_footer' => array(
						'name' => tra('Footer margin from bottom of document'),
						'description' => tra('Only applicable if footer is set.Numeric value only, no need to add px. Warning: Footer can overlap text if bottom margin is not set properly'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_margin_footer'],
						'size' => '2',
						'filter' => 'digits',
					),
					'hyperlinks' => array(
						'name' => tra('Hyperlink behaviour in PDF'),
						'description' => tra(''),
						'tags' => array('advanced'),
						'type' => 'list',
						'default'=>'',
						'options' => array(
							array('text'=>'Default','value'=>''),
							array('text'=>'Off (Links will be removed)','value'=>'off'),
							array('text'=>'Add as footnote (Links will be listed at end of document)','value'=>'footnote'),
						)	
					),
					'autobookmarks' => array(
						'name' => tra('Auto Bookmarks for Adobe Reader'),
						'description' => tra(''),
						'tags' => array('advanced'),
						'type' => 'list',
						'default'=>'',
						'options' => array(
							array('text'=>'Default','value'=>''),
							array('text'=>'Off','value'=>'off'),
							array('text'=>'On (Automatically generate bookmarks from h1 - h3)','value'=>'On'),
						)
					),
					'columns' => array(
						'name' => tra('Number of columns'),
						'description' => tra(''),
						'tags' => array('advanced'),
						'type' => 'list',
						'default'=>'',
						'options' => array(
							array('text'=>'Default - 1 Column','value'=>''),
							array('text'=>'2 Columns','value'=>'2'),
							array('text'=>'3 Columns','3'),
							array('text'=>'4 Columns','4'),							
						)	
					),					
					'password' => array(
						'name' => tra('PDF password for viewing'),
						'description' => tra('Secure confidential PDF with password, leave blank if password protected is not needed'),
						'type' => 'password',
						'default' => $prefs['print_pdf_mpdf_password'],
					),
					'watermark' => array(
						'name' => tra('Watermark text'),
						'description' => tra('Watermark text value, for example: Confidential, Draft etc. '),
						'type' => 'text',
						'default' => '',
					),
					'watermark_image' => array(
						'name' => tra('Watermark image, enter full URL'),
						'description' => tra('Full URL of watermark image'),
						'type' => 'text',
						'default' => '',
					),
					'background' => array(
						'name' => tra('Page background color'),
						'description' => tra('Enter color code'),
						'type' => 'text',
						'default' => '',
					),
					'background_image' => array(
						'name' => tra('Page background image'),
						'description' => tra('Enter complete URL'),
						'type' => 'text',
						'default' => '',
					),					
					'coverpage_text_settings' => array(
						'name' => tra('CoverPage text settings'),
						'description' => tra('<code>Heading|Subheading|Text Alignment|Background color|Text color|Page border|Border color</code>. Enter settings separated by <code>|</code>,sequence is important,leave blank for default. For example <code>{PAGETITLE}|Tikiwiki|Center|#fff|#000|1|#ccc</code>'),
						'type' => 'text',
						'default' => '',
					),
					'coverpage_image_settings' => array(
						'name' => tra('Coverpage image URL'),
						'description' => tra('Enter complete URL'),
						'type' => 'text',
						'default' => '',
					),
					
                ),
        );
}

function wikiplugin_pdf($data, $params)
{
	//included globals to check mpdf selection as pdf generation engine
	global $prefs;
	
	//checking if mdpf is default PDF generation engine, since plugin is only set for mpdf. 
	if($prefs['print_pdf_from_url'] != 'mpdf')
		return WikiParser_PluginOutput::internalError(tr('For pluginPDF, please select mpdf as default PDF engine from Print Settings.'));
	$paramList='';
	//creating string of data paramaters set by user
 	foreach($params as $paramName=>$param)
	{
		$paramList.=$paramName."='".$param."' ";
	}
	return "<pdfsettings ".$paramList."></pdfsettings>";
}