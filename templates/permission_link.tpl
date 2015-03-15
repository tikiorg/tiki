{if $permission_link.mode eq 'text'}
	<a class="{if $permission_link.active}text-success{else}text-warning{/if}{if !empty($permission_link.addclass)} {$permission_link.addclass}{/if}" href="{$permission_link.url|escape}">
		{icon name="permission"} {$permission_link.label|escape}
		{if $permission_link.count}
			<span class="badge">{$permission_link.count|escape}</span>
		{/if}
	</a>
{elseif $permission_link.mode eq 'button'}
	<a class="{if $permission_link.active and $permission_link.type ne 'file gallery'}btn btn-success{else}btn btn-default{/if}{if !empty($permission_link.addclass)} {$permission_link.addclass}{/if}" href="{$permission_link.url|escape}">
		{$permission_link.label|escape}
		{if $permission_link.count}
			<span class="badge">{$permission_link.count|escape}</span>
		{/if}
	</a>
{elseif $permission_link.mode eq 'button_link'}
	<a class="btn btn-link{if !empty($permission_link.addclass)} {$permission_link.addclass}{/if}" href="{$permission_link.url|escape}">
		{$permission_link.label|escape}
		{if $permission_link.count}
			<span class="badge">{$permission_link.count|escape}</span>
		{/if}
	</a>
{elseif $permission_link.mode eq 'link'}
	<a class="link{if !empty($permission_link.addclass)} {$permission_link.addclass}{/if}" href="{$permission_link.url|escape}">
		{$permission_link.label|escape}
		{if $permission_link.count}
			<span class="badge">{$permission_link.count|escape}</span>
		{/if}
	</a>
{elseif $permission_link.mode eq 'icon'}
	{strip}
	<a class="tips btn {if $permission_link.active}btn-warning {else} btn-link{/if} btn-sm{if !empty($permission_link.addclass)} {$permission_link.addclass}{/if}" href="{$permission_link.url|escape}" title=":{$permission_link.label}">
		{icon name="permission"}
		<span class="sr-only">{$permission_link.label|escape}</span>
		{if $permission_link.count}
			<span class="badge">{$permission_link.count|escape}</span>
		{/if}
	</a>
	{/strip}
{else}
	<a class="{if $permission_link.active}text-success{else}text-warning{/if}{if !empty($permission_link.addclass)} {$permission_link.addclass}{/if}" href="{$permission_link.url|escape}" title="{$permission_link.label|escape}">
		{icon name="permission"}
		<span class="sr-only">{$permission_link.label|escape}</span>
		{if $permission_link.count}
			<span class="badge">{$permission_link.count|escape}</span>
		{/if}
	</a>
{/if}
