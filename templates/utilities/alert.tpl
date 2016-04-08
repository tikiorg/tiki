<div id="alert-wrapper">
	{remarksbox type="{$ajaxtype}" close="{if $ajaxdismissible eq 'n'}n{else}y{/if}" title="{$ajaxheading}"}
		{if !empty($ajaxmsg)}
			{$ajaxmsg|escape}
		{/if}
		{if isset($ajaxitems) && $ajaxitems|count > 0}
			<ul>
				<li>
					{foreach $ajaxitems as $name}
						{$name|escape}{if !$name@last}, {/if}
					{/foreach}
				</li>
			</ul>
		{/if}
		{if !empty($ajaxtoMsg)}
			<br>
			{$ajaxtoMsg|escape}
		{/if}
		{if isset($ajaxtoList) && $ajaxtoList|count > 0}
			<ul>
				<li>
					{foreach $ajaxtoList as $toName}
						{$toName|escape}{if !$toName@last}, {/if}
					{/foreach}
				</li>
			</ul>
		{/if}
		{if !empty($ajaxtimeoutMsg)}
			<h5>
				{$ajaxtimeoutMsg|escape}
			</h5>
		{/if}
		{if !empty($ajaxtimer)}
			<div style="text-align: center">
				<em>{tr}Redirecting in {/tr}</em>
				<span id="timer-seconds">
						<em>
							{$ajaxtimer}
						</em>
				</span> <em>{tr}seconds{/tr}</em>
			</div>
		{/if}
	{/remarksbox}
</div>