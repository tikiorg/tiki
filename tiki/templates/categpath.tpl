<span class="categpath">
{foreach name=u key=k item=i from=$catp}
<a class="categpath" href="tiki-browse_categories.php?parentId={$k}">{$i|replace:' ':'&nbsp;'}</a>{if !$smarty.foreach.u.last}&nbsp;&gt;&nbsp;{/if}
{/foreach}
</span><br />
