{* $Id$ *}
{if $carousel and $carousel.id}{$containerId = $carousel.id}{else}{$containerId = 'wp_list_carousel'}{/if}
<div id="{$containerId}" class="carousel slide" data-ride="carousel"{if $carousel and $carousel.interval} data-interval="{$carousel.interval}"{/if}{if $carousel and isset($carousel.pause)} data-pause="{$carousel.pause}"{/if}>
	{* Indicators *}
	<ol class="carousel-indicators">
		{foreach from=$results item=row}
			<li data-target="#{$containerId}" data-slide-to="{$row@index}"{if $row@index eq 0} class="active"{/if}></li>
		{/foreach}
	</ol>

	{* Wrapper for slides *}
	<div class="carousel-inner">
		{foreach from=$results item=row}
			<div class="item{if $row@index eq 0} active{/if}">
				{if $body and $body.field}
					{if $body.mode eq 'raw'}
						{$row[$body.field]}
					{else}
						{$row[$body.field]|escape}
					{/if}
				{/if}

				<div class="carousel-caption">
					{if $caption and $caption.field}
						{if $caption.mode eq 'raw'}
							{$row[$caption.field]}
						{else}
							{$row[$caption.field]|escape}
						{/if}
					{/if}
				</div>
			</div>
		{/foreach}
	</div>

	{* Controls *}
	<a class="left carousel-control" href="#{$containerId}" role="button" data-slide="prev">
		{icon name='chevron-left'}
		<span class="sr-only">Previous</span>
	</a>
	<a class="right carousel-control" href="#{$containerId}" role="button" data-slide="next">
		{icon name='chevron-right'}
		<span class="sr-only">Next</span>
	</a>
</div>
{pagination_links resultset=$results}{/pagination_links}
