{if $feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y' and isset($freetags.data[0])}
<div class="freetaglist">{tr}Tags{/tr}: 
{foreach from=$freetags.data item=taginfo}
<a class="freetag" href="tiki-browse_freetags.php?tag={$taginfo.tag}">{$taginfo.tag}</a>
{/foreach}
</div>
{/if}

