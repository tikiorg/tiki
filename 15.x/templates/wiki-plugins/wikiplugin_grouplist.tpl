{* $Id$ *}
{if empty($groups)}
	&mdash;
{else}
	<ul>
	{foreach from=$groups item=group}
		<li>
		{if $params.linkhome eq 'y' && !empty($group.groupHome)}
			<a href="{$group.groupHome|sefurl:wiki}">
			{assign var=link value='y'}
		{/if}
		{$group.groupName|escape}
		{if !empty($link)}
			</a>
		{/if}
		</li>
	{/foreach}
	</ul>
{/if}
