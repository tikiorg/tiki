{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{if $title==""}
{assign var=title value="{tr}View Structure{/tr}"}
{/if}
{tikimodule title=$title name="aulawiki_viewstructure" flip=$module_params.flip decorations=$module_params.decorations}

{include file="aulawiki-structure_tree.tpl" subtree=$subtree}
{/tikimodule}
