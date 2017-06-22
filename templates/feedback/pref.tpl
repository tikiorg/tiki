{* $Id$ *}
{if $fb|count > 0}
	{if $fb|count == 1}
		{$title = "{tr}The following change has been applied{/tr}"}
	{else}
		{$title = "{tr}The following changes have been applied{/tr}"}
	{/if}

	{remarksbox type='feedback' title="$title"}
		<ul class="list-unstyled">
			{foreach $fb as $item}
				<li>
					{if $item.st eq 0}
						{icon name='disable'}
					{elseif $item.st eq 1}
						{icon name='ok'}
					{elseif $item.st eq 2}
						{icon name='edit'}
					{elseif $item.st eq 4}
						{icon name='undo'}
					{else}
						{icon name='information'}
					{/if}
					{if $item.st ne 3}{tr}Preference{/tr} {/if}<strong>{tr}{$item.mes[0]|stringfix}{/tr}</strong>
					{if $item.st ne 3}<small>({tr}Preference name:{/tr} {$item.name})</small>{/if}
				</li>
			{/foreach}
		</ul>
	{/remarksbox}
{/if}
