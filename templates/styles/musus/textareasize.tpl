{* \brief: the 4 buttons to change a textarea size (only one per form)
  * \param: $area_name = the textarea id
  * \param: $formId = the form id
  * the form needs 2 hidden input named 'rows' and 'cols' to remember the settings for a preview
  *}

<a href="javascript:textareasize('{$area_name}', +10, 0, '{$formId}')" title="{tr}Click here to enlarge the textarea height{/tr}"><img src="img/icons2/enlargeH.gif" border="0" alt="{tr}Enlarge textarea height{/tr}" /></a>
<a href="javascript:textareasize('{$area_name}', -10, 0, '{$formId}')" title="{tr}Click here to reduce the textarea height{/tr}"><img src="img/icons2/reduceH.gif" border="0" alt="{tr}Reduce textarea height{/tr}" /></a>

<input type="image" src="img/icons2/reduceW.gif" name="reduceW" value="reduce" alt="{tr}Reduce textarea width{/tr}" />
<input type="image" src="img/icons2/enlargeW.gif" name="enlargeW" value="enlarge" alt="{tr}Enlarge textarea width{/tr}" />