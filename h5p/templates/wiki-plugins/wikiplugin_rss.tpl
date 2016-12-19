{if $rsstitle and $showtitle}
	<div class="rsstitle">
		<a target="_blank" href="{$rsstitle.link|escape}">{$rsstitle.title|escape}</a>
	</div>
{/if}
<ul class="rsslist{if $ticker} rssticker{/if}">
	{foreach from=$items item=item}
		{if $icon}
			<div style="list-style:square inside url({$icon})">
		{/if}
		<li class="rssitem">
			<a target="_blank" href="{$item.url|escape}">{$item.title|escape}</a>

			{if $item.author and $showauthor and $item.publication_date and $showdate}
				&nbsp;&nbsp;&nbsp;({$item.author|escape}, <span class="rssdate">{$item.publication_date|tiki_short_date}</span>)
			{elseif $item.author and $showauthor}
				&nbsp;&nbsp;&nbsp;({$item.author|escape})
			{elseif $item.publication_date and $showdate}
				&nbsp;&nbsp;&nbsp;(<span class="rssdate">{$item.publication_date|tiki_short_date}</span>)
			{/if}

			{if $item.description && $showdesc}
				<div class="rssdescription">
					{$item.description|escape}
				</div>
			{/if}
		</li>
		{if $icon}
			</div>
		{/if}
	{/foreach}
</ul>

{if $ticker}
	{jq}
		function rsstick(){
			$('ul.rssticker li:first').slideUp( function () { $(this).appendTo($('ul.rssticker')).slideDown(); });
		}
		setInterval(function(){ rsstick() }, 5000);
	{/jq}
{/if}
