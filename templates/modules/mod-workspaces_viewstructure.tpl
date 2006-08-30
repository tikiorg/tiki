{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{if $title==""}
{assign var=title value="{tr}View Structure{/tr}"}
{/if}
{tiki_workspaces_module title=$title name="workspaces_viewstructure" flip=$module_params.flip decorations=$module_params.decorations style_title=$style_title style_data=$style_data}
{include file="tiki-workspaces_module_error.tpl" error=$error_msg}
{include file="tiki-workspaces_structure_tree.tpl" subtree=$subtree}
{/tiki_workspaces_module}
