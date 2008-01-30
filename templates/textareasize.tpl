{* $Header: /cvsroot/tikiwiki/tiki/templates/textareasize.tpl,v 1.7.2.1 2008-01-30 15:33:47 nyloth Exp $ *}
{* \brief: the 4 buttoms to change a textarea size (only one per form)
  * \param: $area_name = the textarea id
  * \param: $formId = the form id
  * the form needs 2 hidden input named 'rows' and 'cols' to remember the settings for a preview
  *}

<a href="javascript:textareasize('{$area_name}', +10, 0, '{$formId}')" title="{tr}Enlarge area height{/tr}">{icon _id='arrow_out' alt="{tr}Enlarge area height{/tr}"}</a> &nbsp;
<a href="javascript:textareasize('{$area_name}', -10, 0, '{$formId}')" title="{tr}Reduce area height{/tr}">{icon _id='arrow_in' alt="{tr}Reduce area height{/tr}"}</a>
