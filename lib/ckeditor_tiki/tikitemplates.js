/* 
 * $Id$
 * (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
 * 
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 * 
 * Ckeditor templates definition for Tiki 6
 * 
 * TODO: Needs tying in to the content templates somehow - placeholder here for now...
 */

// Register a template definition set named "default".
CKEDITOR.addTemplates('default',
{
    // Not used. The name of the subfolder that contains the preview images of the templates.
    // imagesPath: '', // CKEDITOR.getUrl(''),

    // Template definitions.
    templates: getContentTemplates()
})

function getContentTemplates() {

    var myTitle = 'Hello world';
    var myDescription = 'This is a dummy template. Collecting the wiki content templates is pending';
    var myHTML = '<b>Hello world! This is a dummy.</b>';
    return [{
        title: myTitle,
        html: myHTML,
	    description: myDescription,
    }];
}