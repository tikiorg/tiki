{if $permission_link.mode eq 'text'}
	<a class="{if $permission_link.active}text-success{else}text-warning{/if}" href="{$permission_link.url|escape}">
		{glyph name="lock"}
		{$permission_link.label|escape}
		{if $permission_link.count}
			<span class="badge">{$permission_link.count|escape}</span>
		{/if}
	</a>
{elseif $permission_link.mode eq 'button'}
	<a class="{if $permission_link.active and $permission_link.type ne 'file gallery'}btn btn-success{else}btn btn-default{/if}" href="{$permission_link.url|escape}">
		{$permission_link.label|escape}
		{if $permission_link.count}
			<span class="badge">{$permission_link.count|escape}</span>
		{/if}
	</a>
{elseif $permission_link.mode eq 'button_link'}
	<a class="btn btn-link" href="{$permission_link.url|escape}">
		{$permission_link.label|escape}
		{if $permission_link.count}
			<span class="badge">{$permission_link.count|escape}</span>
		{/if}
	</a>
{elseif $permission_link.mode eq 'link'}
	<a class="link" href="{$permission_link.url|escape}">
		{$permission_link.label|escape}
		{if $permission_link.count}
			<span class="badge">{$permission_link.count|escape}</span>
		{/if}
	</a>
{elseif $permission_link.mode eq 'icon'}
	{strip}
	<a class="link" href="{$permission_link.url|escape}">
		{if $permission_link.active}
			{icon _id=key_active title=$permission_link.label}
		{else}
			{icon _id=key title=$permission_link.label}
		{/if}
		<span class="sr-only">{$permission_link.label|escape}</span>
		{if $permission_link.count}
			<span class="badge">{$permission_link.count|escape}</span>
		{/if}
	</a>
	{/strip}
{else}
	<a class="{if $permission_link.active}text-success{else}text-warning{/if}" href="{$permission_link.url|escape}" title="{$permission_link.label|escape}">
		{glyph name="lock"}
		<span class="sr-only">{$permission_link.label|escape}</span>
		{if $permission_link.count}
			<span class="badge">{$permission_link.count|escape}</span>
		{/if}
	</a>
{/if}
