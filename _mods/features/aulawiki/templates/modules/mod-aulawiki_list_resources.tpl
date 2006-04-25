{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{eval var="{tr}$title{/tr}" assign="tpl_module_title"}

{tikimodule title=$tpl_module_title name="aulawiki_list_resources" flip=$module_params.flip decorations=$module_params.decorations}
{include file="aulawiki-module_error.tpl" error=$error_msg}
{include file="aulawiki-list_resources.tpl" resources=$resources viewType="n"} 

{/tikimodule}