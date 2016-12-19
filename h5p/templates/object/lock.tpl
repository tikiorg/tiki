{* $Id: $ *}
{strip}
<span class="lock_block">
	<a class="lock_button" id="lock_{$data.instance}" data-type="{$data.type}" data-object="{$data.object}" data-is_locked="{$data.is_locked}"
			{if $data.is_locked} title="{tr _0=$data.lockedby|username}Locked by %0{/tr}"{/if} href="#">
		{if $data.is_locked}
			{icon name='lock'}
		{else}
			{icon name='unlock'}
		{/if}
	</a>
	{if not $data.object}
		<input type='hidden' name='locked' value=''>
	{/if}
</span>

{if $data.can_change}
	{jq}
		$("#lock_{{$data.instance}}").click(function () { objectLockToggle(this); });
	{/jq}
{else}
	{jq}
		$("#lock_{{$data.instance}}").css("cursor", "default");
	{/jq}
{/if}
{/strip}
