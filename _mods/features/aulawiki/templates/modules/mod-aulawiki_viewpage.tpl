{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{if $title==""}
{assign var=title value="{tr}View Page{/tr}"}
{/if}
{tikimodule title="$title" name="aulawiki_viewpage" flip=$module_params.flip decorations=$module_params.decorations}
{include file="aulawiki-module_error.tpl" error=$error_msg}
<div>{$pageBody}
</div>
<p align="right">
<a class="link" href="tiki-editpage.php?page={$pageName}">{tr}edit{/tr}</a>
</p>
{/tikimodule}
