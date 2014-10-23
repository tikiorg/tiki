{extends "layout_view.tpl"}

{block name="content"}
	<table class="table">
		<tr>
			<th>{tr}Count{/tr}</th>
			<th>{tr}Label{/tr}</th>
		</tr>
		{foreach $conditions as $key => $condition}
			<tr>
				<td>
					{if $condition.operator eq 'atLeast'}
						&ge;
					{else}
						&le;
					{/if}
					{$condition.count|escape}
				</td>
				<td>
					<a class="edit" href="#" data-element="{$key|escape}">{$condition.label|escape}</a>
					{if $condition.hidden}
						<span class="label label-info">{tr}Hidden{/tr}</span>
					{/if}
					<a class="delete pull-right text-danger" href="#" data-element="{$key|escape}">{icon name="delete"} {tr}Delete{/tr}</a>
				</td>
			</tr>
		{foreachelse}
			<tr>
				<td colspan="2">{tr}No conditions yet!{/tr}</td>
			</tr>
		{/foreach}
	</table>
	<button class="btn btn-default add pull-right">{icon name="add"} {tr}Add Condition{/tr}</button>
	<input type="hidden" name="conditions" value="{$conditions|json_encode|escape}">
{/block}
