{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	{if $success}
		{remarksbox type=feedback title="{tr}Operation Completed{/tr}"}
			<p>{tr _0=$label}%0 was removed{/tr}</p>
			<a class="btn btn-success" href="{service controller=search_stored action=list}">{tr}Return to Query List{/tr}</a>
		{/remarksbox}
	{else}
		<form method="post" action="{service controller=search_stored action=delete}">
			<div class="form-group">{tr _0=$label}Do you really want to remove the %0 query?{/tr}</div>
			<div class="form-group">
				<input type="hidden" name="queryId" value="{$queryId|escape}"/>
				<input class="btn btn-danger" type="submit" value="{tr}Delete Query{/tr}"/>
			</div>
		</form>
	{/if}
{/block}
