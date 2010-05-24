{* $Id$ *}

{if isset($objects) && count($objects) gt 0}
<ul class="freetagged clearfix">
	{foreach item=row from=$objects}
		<li class="{$row.type|stringfix:' ':'_'}">
			<h{$h_level}><a href="{$row.href|escape}">{$row.name|escape}</a></h{$h_level}>
			<p>
				<em>{$row.description}</em>
				{$row.img}
			</p>
			{if !empty($row.date)}<p class="editdate">
				{$row.date|tiki_short_datetime}
			</p>{/if}
		</li>
	{/foreach}
</ul>
{/if}
