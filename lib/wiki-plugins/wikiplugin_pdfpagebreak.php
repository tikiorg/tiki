<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin-pivottable.php 57956 2016-03-17 19:58:12Z jonnybradley $

function wikiplugin_pdfpagebreak_info()
{
	 return array(
                'name' => tra('PluginPDF Page Break'),
                'documentation' => 'PluginPDFPageBreak',
                'description' => tra('Helpful to format PDF files created, plugin adds page break in PDF file generated.'),
                'tags' => array( 'basic' ),
				'iconname' => 'pdf',
                'prefs' => array( 'wikiplugin_pdf' ),
				'introduced' => 17,
                   
        );
}

function wikiplugin_pdfpagebreak()
{
	return "<pagebreak/>";
}