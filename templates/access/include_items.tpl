<div class="form-group">
	<h5>{$customMsg}</h5>
	{if isset($items) && $items|count > 0}
		{if $items|count < 16}
			<ul>
				{foreach $items as $name}
					<li>
						{$name|escape}
					</li>
				{/foreach}
			</ul>
		{else}
			{foreach $items as $name}
				{$name|escape}{if !$name@last}, {/if}
			{/foreach}
		{/if}
	{/if}
</div>
