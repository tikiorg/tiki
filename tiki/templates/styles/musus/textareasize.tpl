{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/textareasize.tpl,v 1.4 2004-02-01 07:43:28 musus Exp $ *}
{* \brief: the 4 buttoms to change a textarea size (only one per form)
  * \param: $area_name = the textarea id
  * \param: $formId = the form id
  * the form needs 2 hidden input named 'rows' and 'cols' to remember the settings for a preview
  *}

<a href="javascript:textareasize('{$area_name}', +10, 0, '{$formId}')" title="{tr}Enlarge area height{/tr}"><img src="img/icons2/enlargeH.gif" border="0" alt="{tr}Enlarge area height{/tr}" /></a>
<a href="javascript:textareasize('{$area_name}', -10, 0, '{$formId}')" title="{tr}Reduce area height{/tr}"><img src="img/icons2/reduceH.gif" border="0" alt="{tr}Reduce area height{/tr}" /></a>

<input type="image" src="img/icons2/reduceW.gif" name="reduceW" value="reduce" alt="{tr}Reduce area width{/tr}" />
<input type="image" src="img/icons2/enlargeW.gif" name="enlargeW" value="enlarge" alt="{tr}Enlarge area width{/tr}" />