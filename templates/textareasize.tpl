{* $Header: /cvsroot/tikiwiki/tiki/templates/textareasize.tpl,v 1.7.2.2 2008-02-05 02:16:06 ricks99 Exp $ *}
{* \brief: the 4 buttoms to change a textarea size (only one per form)
  * \param: $area_name = the textarea id
  * \param: $formId = the form id
  * the form needs 2 hidden input named 'rows' and 'cols' to remember the settings for a preview
  *}

<a href="javascript:textareasize('{$area_name}', +10, 0, '{$formId}')" onclick="javascript:needToConfirm = false" title="{tr}Enlarge area height{/tr}">{icon _id='arrow_out' alt="{tr}Enlarge area height{/tr}"}</a> &nbsp;
<a href="javascript:textareasize('{$area_name}', -10, 0, '{$formId}')" onclick="javascript:needToConfirm = false" title="{tr}Reduce area height{/tr}">{icon _id='arrow_in' alt="{tr}Reduce area height{/tr}"}</a>
