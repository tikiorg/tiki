<?php // -*- coding:utf-8 -*-
// parameters:
// lang=xx    : only tranlates language 'xx',
//              if not given all languages are translated
// comments   : generate all comments (equal to close&module)
// close      : look for similar strings that are allready translated and
//              generate a commet if a 'match' is made
// module     : generate comments that describes in which .php and/or .tpl
//              module(s) a certain string was found (useful for checking
//              translations in context)
// patch      : looks for the file 'language.patch' in the same directory
//              as the corresponding language.php and overrides any strings
//              in language.php - good if a user does not agree with
//              some translations or if only changes are sent to the maintaner
// spelling   : generates a file 'spellcheck_me.txt' that contains the
//              words used in the translation.It is then easy to check this
//              file for spelling errors (corrections must be done in
 //              'language.php, however)
// groupwrite : Sets the generated files permissions to allow the generated
//              language.php also be group writable. This is good for
//              translators if they do not have root access to tiki but
//              are in the same group as the webserver. Please remember
//              to have write access removed when translation is finished
//              for security reasons. (Run script again without this
//              parameter)
// Examples:
// http://www.neonchart.com/get_strings.php?lang=sv
// Will translate langauage 'sv' and (almost) avoiding comment generation

// http://www.neonchart.com/get_strings.php?lang=sv&comments
// Will translate langauage 'sv' and generate all possible comments.
// This is the most usefull mode when working on a translation.

// http://www.neonchart.com/get_strings.php?lang=sv&nohelp&nosections
// These options will only provide the minimal amout of comments.
// Usefull mode when preparing a translation for distribution.

// http://www.neonchart.com/get_strings.php?nohelp&nosections
// Prepare all languages for release 


$lang=Array(
"the background color, use #rrvvbb color types.\n" => "the background colour, use #rrvvbb colour types.\n",
"color of the border\n" => "colour of the border\n",
"background color of the node\n" => "background colour of the node\n",
"color for links (called edges here)\n" => "colour for links (called edges here)\n",
"Colored text" => "Coloured text",
"Will display using the indicated HTML color" => "Will display using the indicated HTML colour",
"Display Tiki objects that have not been categorized" => "Display Tiki objects that have not been categorised",
"categorize this object" => "categorise this object",
"Categorize" => "Categorise",
"\n<b>Note 1</b>: if you allow your users to configure modules then assigned\nmodules won't be reflected in the screen until you configure them\nfrom MyTiki->modules.<br />\n<b>Note 2</b>: If you assign modules to groups make sure that you\nhave turned off the option 'display modules to all groups always'\nfrom Admin->General\n" => "\n<b>Notes</b>\n<ul><li>If you allow your users to configure modules then assigned modules won't be reflected in the screen until you configure them from MyTiki->modules.</li><li>If you assign modules to groups make sure that you have turned off the option 'display modules to all groups always' from Admin->General.",
// ### end of untranslated words
// ###

// ###
// ### start of possibly untranslated words
// ###

// ###
// ### end of possibly untranslated words
// ###

"###end###"=>"###end###");
?>
