/* 
 * $Id$
 * (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
 * 
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 * 
 * Ckeditor templates definition for Tiki 
 */


// Register a template definition set named "default".
CKEDITOR.addTemplates('default',
{
    // Not used. The name of the subfolder that contains the preview images of the templates.
    // imagesPath: '', // CKEDITOR.getUrl(''),

    // Template definitions.
    templates: getContentTemplates()
});

function getContentTemplates() {

    // Do a synchronous call, to be able to fill the listbox. 
    // Can lockup the GUI a bit. Especially if the are many, large content templates defined.
    //  Updating the element "on success" would be better, but I am not sure how at the moment...Arild
    var result = $.ajax({
        type: 'GET',
        url: 'tiki-ajax_services.php?',
        dataType: 'json',
        data: {
            controller: 'contenttemplate',
            action: 'list'
            },
        async: false,       // Synchronous call
        success: function(data) {

		    var content = data["data"];
		    var cant = data["cant"];

            result = [];
            for (var i = 0; i < cant; i++) {
                result.push(
                    [{
                        title: content[i]['title'],
                        html: content[i]['html']
                    }]);
            }
            return result;
        }
    });

    if(result.status == 200) {
        var ret = $.parseJSON(result.responseText);
        return ret['data'];
    } else {
        return [];
    }
}
