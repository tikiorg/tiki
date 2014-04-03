{if $permission_link.mode eq 'text'}
	<a class="{if $permission_link.active}text-success{else}text-warning{/if}" href="{$permission_link.url|escape}">{glyph name="lock"} {$permission_link.label|escape}</a>
{elseif $permission_link.mode eq 'button'}
	<a class="{if $permission_link.active}btn btn-success{else}btn btn-default{/if}" href="{$permission_link.url|escape}">{$permission_link.label|escape}</a>
{elseif $permission_link.mode eq 'button_link'}
	<a class="btn btn-link" href="{$permission_link.url|escape}">{$permission_link.label|escape}</a>
{elseif $permission_link.mode eq 'link'}
	<a class="link" href="{$permission_link.url|escape}">{$permission_link.label|escape}</a>
{elseif $permission_link.mode eq 'icon'}
	{strip}
	<a class="link" href="{$permission_link.url|escape}">
		{if $permission_link.active}
			{icon _id=key_active title=$permission_link.label}
		{else}
			{icon _id=key title=$permission_link.label}
		{/if}
	</a>
	{/strip}
{else}
	<a class="{if $permission_link.active}text-success{else}text-warning{/if}" href="{$permission_link.url|escape}" title="{$permission_link.label|escape}">{glyph name="lock"}</a>
{/if}
