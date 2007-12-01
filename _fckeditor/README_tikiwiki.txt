FCK Editor for Tikiwiki
-----------------------

This directory includes code source extracted from FCK Editor,
for a full version of the original package, check oout

    http://fckeditor.net


Tikiwiki integration
--------------------

This library is tuned up to work with tikiwiki v1.10 or above.
3 files in Tikiwiki manage the integration:
* lib/tikifck.php is the main class for creating fckeditor edit forms 
* lib/smarty_tiki/function.editform.php is used in templates
* templates/fck_edit.tpl is the template used by the smarty plugin


How to use it ?
---------------

In any template file, use the smarty plugin:

    {editform InstanceName='edit'}

The only required parameter is InstanceName, that will also be the
name of the input element it replaces in the form.
Optional parameters are:
Meat : the default content of the edit zone
Width and Height : self explanatory
ToolbarSet : make choice of the toolbar used in fckeditor


Patches for specific Tikiwiki use
---------------------------------

* Filemanager MultiTiki patch
File to patch get similar changes, in browser and upload connectors:
lib/fckeditor/editor/filemanager/connectors/php/config.php
===================================================================
20a21,38
> $tikiroot = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
> $tikidomain = '';
> if (is_file($tikiroot.'db/virtuals.inc')) {
> 	if (isset($_SERVER['TIKI_VIRTUAL']) and is_file($tikiroot.'/db/'.$_SERVER['TIKI_VIRTUAL'].'/local.php')) {
> 		$tikidomain = $_SERVER['TIKI_VIRTUAL'];
> 	} elseif (isset($_SERVER['SERVER_NAME']) and is_file($tikiroot.'/db/'.$_SERVER['SERVER_NAME'].'/local.php')) {
> 		$tikidomain = $_SERVER['SERVER_NAME'];
> 	} elseif (isset($_SERVER['HTTP_HOST']) and is_file($tikiroot.'/db/'.$_SERVER['HTTP_HOST'].'/local.php')) {
> 		$tikidomain = $_SERVER['HTTP_HOST'];
> 	}
> }
> if ($tikidomain) $tikidomain.= '/';
> if ($tikiroot != $_SERVER['DOCUMENT_ROOT']) {
> 	$tikipath = strrchr($tikiroot,$_SERVER['DOCUMENT_ROOT']).'/';
> } else {
> 	$tikipath = '/';
> }
> 
27c45
< $Config['UserFilesPath'] = 'img/wiki_up/' ;
---
> $Config['UserFilesPath'] = $tikipath.'img/wiki_up/'.$tikidomain ;

* Also enable the filemanager connector with this line:  $Config['Enabled'] = true ;


Translation status
-------------------

If the "Detect Browser Language" is enabled, FCK will try to fit that 
choice. Available languages are for now:
af ar bg bn bs ca cs da de el en-au en-ca en en-uk eo es et eu fa fi fo
fr gl he hi hr hu it ja km ko lt lv mn ms nb nl no pl pt-br pt ro ru sk
sl sr sr-latn sv th tr uk vi zh-cn zh

in tikiwiki we have :
ar ca cn cs da de dk el en en-uk es fj fr gl he hr hu it ja ko nl no pl
po pt pt-br ru sb sk sp sr sr-latn sv sw tv tw uk

So, we still miss FCKeditor translation for :
cn dk fj po sb sp sw tv tw


---
EOF
