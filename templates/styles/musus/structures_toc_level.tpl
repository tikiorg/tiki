<li class="toclevel">
	<a title="" href="tiki-index.php?page_ref_id={$that.page_ref_id}">{$that.pageName}</a>
</li>
{if $that.sub}<ul>
	{section name=xitem loop=$that.sub}
		{include file="structures_toc_level.tpl" that=$that.sub[xitem]}
	{/section}
</ul>{/if}