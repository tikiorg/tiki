<span class="categpath">
{foreach name=u key=k item=i from=$catp}
<a class="categpath" href="tiki-browse_categories.php?parentId={$k}">{$i}</a>
{if !$smarty.foreach.u.last} &gt; {/if}
{/foreach}
</span><br />
