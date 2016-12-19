{* $Id$ *}

<div id="alert-wrapper">
	{foreach $fb as $item}
		{remarksbox type="{$item.type}" title="{$item.title}"}
			{if !empty($item.mes)}
				{$item.mes|escape}
			{/if}
			{if isset($item.items) && $item.items|count > 0}
				<ul>
					<li>
						{foreach $item.items as $name}
							{$name|escape}{if !$name@last}, {/if}
						{/foreach}
					</li>
				</ul>
			{/if}
			{if !empty($item.toMsg)}
				<br>
				{$item.toMsg|escape}
			{/if}
			{if isset($item.toList) && $item.toList|count > 0}
				<ul>
					<li>
						{foreach $item.toList as $toName}
							{$toName|escape}{if !$toName@last}, {/if}
						{/foreach}
					</li>
				</ul>
			{/if}
			{if !empty($item.timeoutMsg)}
				<h5>
					{$item.timeoutMsg|escape}
				</h5>
			{/if}
			{if !empty($item.timer)}
				<div style="text-align: center">
					<em>{tr _0='<span id="timer-seconds">'|cat:$item.timer|cat:'</span>'}Redirecting in %0 seconds{/tr}</em>
				</div>
			{/if}
		{/remarksbox}
	{/foreach}
</div>