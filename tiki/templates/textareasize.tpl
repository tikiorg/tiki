{* $Header: /cvsroot/tikiwiki/tiki/templates/textareasize.tpl,v 1.6 2004-02-09 18:20:22 mose Exp $ *}
{* \brief: the 4 buttoms to change a textarea size (only one per form)
  * \param: $area_name = the textarea id
  * \param: $formId = the form id
  * the form needs 2 hidden input named 'rows' and 'cols' to remember the settings for a preview
  *}

<a href="javascript:textareasize('{$area_name}', +10, 0, '{$formId}')" title="{tr}Enlarge area height{/tr}"><img src="img/icons2/enlargeH.gif" border="0" alt="{tr}Enlarge area height{/tr}" /></a>
<a href="javascript:textareasize('{$area_name}', -10, 0, '{$formId}')" title="{tr}Reduce area height{/tr}"><img src="img/icons2/reduceH.gif" border="0" alt="{tr}Reduce area height{/tr}" /></a>
