{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}

{tiki_workspaces_module title=$title name="workspaces_list_resources" flip=$module_params.flip decorations=$module_params.decorations style_title=$style_title style_data=$style_data}
{include file="tiki-workspaces_module_error.tpl" error=$error_msg}
{include file="tiki-workspaces_list_resources.tpl" resources=$resources showType="$showType" showDesc="$showDesc" showCreationDate="$showCreationDate" showButtons="$showButtons"} 

{/tiki_workspaces_module}