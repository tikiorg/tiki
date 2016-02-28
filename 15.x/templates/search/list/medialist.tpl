{* $Id$ *}
<ul class="media-list">
	{foreach from=$results item=row}
		<li class="media">
			{if $icon and $icon.field}
				<img class="media-object pull-left" src="{$row[$icon.field]}">
			{/if}
			<div class="media-body">
				<h4 class="media-heading">{object_link type=$row.object_type id=$row.object_id}</h4>
				{if $body and $body.field}
					{if $body.mode eq 'raw'}
						{$row[$body.field]}
					{else}
						{$row[$body.field]|escape}
					{/if}
				{/if}
			</div>
		</li>
	{/foreach}
</ul>
{pagination_links resultset=$results}{/pagination_links}
