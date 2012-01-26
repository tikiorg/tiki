{* $Id: wikiplugin_grouplist.tpl 33949 2011-04-14 05:13:23Z chealer $ *}
{if empty($groups)}
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