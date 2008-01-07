{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y' and isset($freetags.data[0])}
<div class="freetaglist">{tr}Tags{/tr}: 
{foreach from=$freetags.data item=taginfo}
<a class="freetag" href="tiki-browse_freetags.php?tag={$taginfo.tag}">{$taginfo.tag}</a>
{if isset($deleteTag) and $tiki_p_admin eq 'y'}
(<a href="{$smarty.server.REQUEST_URI}{if strstr($smarty.server.REQUEST_URI, '?')}&amp;{else}?{/if}delTag={$taginfo.tag|escape:'url'}">x</a>)
{/if}
{/foreach}
</div>
{/if}

