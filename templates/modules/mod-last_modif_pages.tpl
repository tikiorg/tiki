{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_modif_pages.tpl,v 1.9 2003-08-07 20:56:53 zaufi Exp $ *}

{if $feature_wiki eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Last changes{/tr}" module_name="last_modif_pages"}
</div>
<div class="box-data">
{section name=ix loop=$modLastModif}
<tt class="linkmodule">{$smarty.section.ix.index_next|string_format:"%'.-3s"}</tt>
<a class="linkmodule" href="tiki-index.php?page={$modLastModif[ix].pageName|escape:"url"}" 
{if (strlen($modLastModif[ix].pageName) > $maxlen) && ($maxlen > 0)}title="{$modLastModif[ix].pageName}"{/if}>
{if $maxlen > 0}
{$modLastModif[ix].pageName|truncate:$maxlen:"...":true}
{else}
{$modLastModif[ix].pageName}
{/if}
</a><br />
{/section}
</div>
</div>
{/if}
