{if $permission_link.mode eq 'text'}
	<a class="{if $permission_link.active}text-success{else}text-warning{/if}" href="{$permission_link.url|escape}">{glyph name="lock"} {tr}Permissions{/tr}</a>
{elseif $permission_link.mode eq 'icon'}
	{strip}
	<a class="link" href="{$permission_link.url|escape}">
		{if $permission_link.active}
			{icon _id=key_active title="{tr}Permissions{/tr}"}
		{else}
			{icon _id=key title="{tr}Permissions{/tr}"}
		{/if}
	</a>
	{/strip}
{else}
	<a class="{if $permission_link.active}text-success{else}text-warning{/if}" href="{$permission_link.url|escape}" title="{tr}Permissions{/tr}">{glyph name="lock"}</a>
{/if}
