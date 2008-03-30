{* $Id$ *}

{if $prefs.feature_wiki eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Random Pages{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="random_pages" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modRandomPages}
<tr><td   class="module"><a class="linkmodule" href="tiki-index.php?page={$modRandomPages[ix]|escape:'url'}">{$modRandomPages[ix]}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}
