{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	<form id="confirm-action" class='confirm-action' action="{service controller="$confirmController" action="$confirmAction"}" method="post">
		{include file='access/include_items.tpl'}
		{include file='access/include_hidden.tpl'}
	</form>
	{include file='access/include_footer.tpl'}
{/block}
