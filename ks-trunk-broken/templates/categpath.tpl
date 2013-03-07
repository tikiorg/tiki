<span class="categpath">
{foreach name=u key=k item=i from=$catp}
	{if $catpathShowLink}
	<a class="categpath" href="{$k|sefurl:category:'':'':y:$i}" title="{tr}Browse Category{/tr}">{$i|tr_if|escape|replace:' ':'&nbsp;'}</a>{if !$smarty.foreach.u.last}&nbsp;{$prefs.site_crumb_seper|escape:"html"}&nbsp;{/if}
	{else}
	{$i|tr_if|escape|replace:' ':'&nbsp;'} {if !$smarty.foreach.u.last}&nbsp;{$prefs.site_crumb_seper|escape:"html"}&nbsp;{/if}
	{/if}
{/foreach}
</span>
