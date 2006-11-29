FCK Editor for Tikiwiki
-----------------------

This directory includes code source extracted from FCK Editor,
for a full version of the original package, check oout

    http://fckeditor.net


Tikiwiki integration
--------------------

This libaray is tuned up to work with tikiwiki v1.10 or above.
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



Translation status
-------------------

If the "Detect Browser Language" is enabled, FCK will try to fit that 
choice. Available languages are for now:
ar bg bn bs ca cs da de el en-au en-ca en en-uk eo es et eu fa fi fo fr
gl he hi hr hu it ja km ko lt lv mn ms nb nl no pl pt-br pt ro ru sk sl
sr sr-latn sv th tr uk vi zh-cn zh

in tikiwiki we have :
ar ca cn cs da de dk el en en-uk es fj fr gl he hr hu it ja ko nl no pl
po pt pt-br ru sb sk sp sr sr-latn sv sw tv tw uk

So, we still miss FCKeditor transation for :
cn dk fj po sb sp sw tv tw


---
EOF
