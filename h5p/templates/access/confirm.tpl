{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	<form id="confirm-action" class='confirm-action' action="{service controller="$confirmController" action="$confirmAction"}" method="post">
		{include file='access/include_items.tpl'}
		{include file='access/include_hidden.tpl'}
	</form>
	{if !empty($help)}
		<span class="help-block">
			{$help|escape}
		</span>
	{/if}
	{include file='access/include_footer.tpl'}
{/block}
