{* $Header: /cvsroot/tikiwiki/tiki/templates/textareaSize.tpl,v 1.7 2003-11-23 23:30:55 sylvieg Exp $ *}
{* \brief: the 4 buttoms to change a textarea size (only one per form)
  * \param: $area_name = the textarea id
  * \param: $formId = the form id
  * the form needs 2 hidden input named 'rows' and 'cols' to remember the settings for a preview
  *}

<a href="javascript:textareaSize('{$area_name}', +10, 0, '{$formId}')" alt="{tr}Enlarge area height{/tr}" title="{tr}Enlarge area height{/tr}"><img src="img/icons2/enlargeH.gif" border="0" /></a>
<a href="javascript:textareaSize('{$area_name}', -10, 0, '{$formId}')" alt="{tr}Reduce area height{/tr}" title="{tr}Reduce area height{/tr}"><img src="img/icons2/reduceH.gif" border="0" /></a>

<input type="image" src="img/icons2/reduceW.gif" name="reduceW" value="reduce" alt="{tr}Reduce area width{/tr}" />
<input type="image" src="img/icons2/enlargeW.gif" name="enlargeW" value="enlarge" alt="{tr}Enlarge area width{/tr}" />
