{* $Header: /cvsroot/tikiwiki/tiki/templates/textareaSize.tpl,v 1.4 2003-11-19 02:35:18 gongo Exp $ *}
{* the 4 buttoms to change a textarea size (only one in a form)
  * the textarea id = $area_name
  * the form id must be editpageform ( a variable could be good)
  * the form needs 2 hidden input named rows and cols *}
<a href="javascript:textareaSize('{$area_name}', +10, 0, 'editpageform')" alt="{tr}Enlarge area height{/tr}" title="{tr}Enlarge area height{/tr}"><img src="img/icons2/enlargeH.gif" border="0" /></a>
<a href="javascript:textareaSize('{$area_name}', -10, 0, 'editpageform')" alt="{tr}Reduce area height{/tr}" title="{tr}Reduce area height{/tr}"><img src="img/icons2/reduceH.gif" border="0" /></a>
<a href="javascript:textareaSize('{$area_name}', 0, +10, 'editpageform')" alt="{tr}Enlarge area width{/tr}" title="{tr}Enlarge area width{/tr}"><img src="img/icons2/enlargeW.gif" border="0" /></a>
<a href="javascript:textareaSize('{$area_name}', 0, -10, 'editpageform')" alt="{tr}Reduce area width{/tr}" title="{tr}Reduce area width{/tr}"><img src="img/icons2/reduceW.gif" border="0" /></a>