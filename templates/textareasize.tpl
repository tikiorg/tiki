{* $Header: /cvsroot/tikiwiki/tiki/templates/textareasize.tpl,v 1.5 2004-02-05 11:44:04 damosoft Exp $ *}
{* \brief: the 4 buttoms to change a textarea size (only one per form)
  * \param: $area_name = the textarea id
  * \param: $formId = the form id
  * the form needs 2 hidden input named 'rows' and 'cols' to remember the settings for a preview
  *}

<a title="{tr}Enlarge area height{/tr}" href="javascript:textareasize('{$area_name}', +10, 0, '{$formId}')"><img src="img/icons2/enlargeH.gif" border="0" alt="{tr}Enlarge area height{/tr}" /></a>
<a title="{tr}Reduce area height{/tr}" href="javascript:textareasize('{$area_name}', -10, 0, '{$formId}')"><img src="img/icons2/reduceH.gif" border="0" alt="{tr}Reduce area height{/tr}" /></a>

<input type="image" src="img/icons2/reduceW.gif" name="reduceW" value="reduce" alt="{tr}Reduce area width{/tr}" />
<input type="image" src="img/icons2/enlargeW.gif" name="enlargeW" value="enlarge" alt="{tr}Enlarge area width{/tr}" />
