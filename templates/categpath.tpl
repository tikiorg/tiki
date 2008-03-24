{if $tiki_p_view_categories ne 'n'}
<span class="categpath">
{foreach name=u key=k item=i from=$catp}
<a class="categpath" href="tiki-browse_categories.php?parentId={$k}" title="{tr}browse category{/tr}">{$i|tr_if|replace:' ':'&nbsp;'}</a>{if !$smarty.foreach.u.last}&nbsp;{$prefs.site_crumb_seper|escape:"html"}&nbsp;{/if}
{/foreach}
</span>
{/if}
