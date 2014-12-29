{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
{if $status eq 'DONE'}
	{tr _0=$username|userlink}%0 has been accepted{/tr}
{else}
	<form method="post" action="{service controller=social action=approve_friend}">
		<p>{tr _0=$username|userlink}Do you really want to approve %0?{/tr}</p>
		<div class="submit">
			<input type="hidden" name="friend" value="{$username|escape}"/>
			<input type="submit" class="btn btn-default btn-sm" value="{tr}Confirm{/tr}"/>
		</div>
	</form>
{/if}
{/block}
