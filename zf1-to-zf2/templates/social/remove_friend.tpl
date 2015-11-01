{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
{if $status eq 'DONE'}
	{tr _0=$friend|userlink}%0 has been removed from your friends{/tr}
{else}
	<form method="post" action="{service controller=social action=remove_friend}">
		<p>{tr _0=$friend|userlink}Do you really want to remove %0?{/tr}</p>
		<div class="submit">
			<input type="hidden" name="friend" value="{$friend|escape}"/>
			<input type="submit" class="btn btn-default btn-sm" value="{tr}Confirm{/tr}"/>
		</div>
	</form>
{/if}
{/block}
