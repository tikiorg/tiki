{* $Id$ *}

{if isset($objects) && count($objects) gt 0}
<ul class="freetagged clearfix">
	{foreach item=row from=$objects}
		<li class="{$row.type|stringfix:' ':'_'}">
			{if $h_level gt 0}<h{$h_level}>{/if}<a href="{$row.href|escape}">{$row.name|escape}</a>{if $h_level gt 0}</h{$h_level}>{/if}
			{if !empty($row.description) or !empty($row.img)}<p>
				<em>{$row.description}</em>
				{$row.img}
			</p>{/if}
			{if !empty($row.date)}<footer class="help-block editdate">
				{$row.date|tiki_short_datetime}
			</footer>{/if}
		</li>
	{/foreach}
</ul>
{if isset($more) && $more eq 'y'}
	<a class="freetagged" href="{$moreurl}">{tr}{$moretext|escape}{/tr}…</a>
{/if}
{/if}
