{* $Header: /cvsroot/tikiwiki/tiki/templates/textareaSize.tpl,v 1.5 2003-11-20 17:51:07 sylvieg Exp $ *}
{* the 4 buttoms to change a textarea size (only one in a form)
  * the textarea id = area_name
  * the form id = formId
  * the form needs 2 hidden input named rows and cols *}
<a href="javascript:textareaSize('{$area_name}', +10, 0, '{$formId}')" alt="{tr}Enlarge area height{/tr}" title="{tr}Enlarge area height{/tr}"><img src="img/icons2/enlargeH.gif" border="0" /></a>
<a href="javascript:textareaSize('{$area_name}', -10, 0, '{$formId}')" alt="{tr}Reduce area height{/tr}" title="{tr}Reduce area height{/tr}"><img src="img/icons2/reduceH.gif" border="0" /></a>
<a href="javascript:textareaSize('{$area_name}', 0, +20, '{$formId}')" alt="{tr}Enlarge area width{/tr}" title="{tr}Enlarge area width{/tr}"><img src="img/icons2/enlargeW.gif" border="0" /></a>
<a href="javascript:textareaSize('{$area_name}', 0, -20, '{$formId}')" alt="{tr}Reduce area width{/tr}" title="{tr}Reduce area width{/tr}"><img src="img/icons2/reduceW.gif" border="0" /></a>