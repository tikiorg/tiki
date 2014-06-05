<div class="table-responsive">
<table class="table table-bordered">
	{if $params.caption}
		<caption>{$params.caption|escape}</caption>
	{/if}
	<tr>
		<th>{tr}Accept{/tr}</th>
		<th>{tr}Undecided{/tr}</th>
		<th>{tr}Reject{/tr}</th>
	</tr>
	<tr>
		{foreach item=voters from=$counts}
		<td>{$voters|@count}</td>
		{/foreach}
	</tr>
	<tr id="plugin-proposal-votelist{$passes}">
		{foreach item=voters from=$counts}
			<td>
				{if $voters|@count}
				<ul>
					{foreach from=$voters item=name}
						<li>{$name|escape}</li>
					{/foreach}
				</ul>
				{/if}
			</td>
		{/foreach}
	</tr>
	{if $available_votes}
		<tr>
		{foreach from=$available_votes item=body key=label}
			<td>
				<form method="post" action="tiki-wikiplugin_edit.php">
					<div>
						<input type="hidden" name="page" value="{$page|escape}">
						<input type="hidden" name="content" value="{$body|escape}">
						<input type="hidden" name="index" value="{$passes|escape}">
						<input type="hidden" name="type" value="proposal">
						<input type="submit" class="btn btn-default btn-sm" value="{$label|escape}">
					</div>
				</form>
			</td>
		{/foreach}
		</tr>
	{/if}
</table>
</div>