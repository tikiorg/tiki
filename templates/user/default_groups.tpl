{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	<form method="post" action="{service controller=$confirmController action=$confirmAction}">
		<fieldset>
			<div class="form-group">
				<label for="userlist" class="control-label">
					{tr}For these selected users:{/tr}
				</label>
				<textarea
					id="userlist"
					class="form-control"
					disabled=""
					cols="10"
					rows="{$rows}"
					wrap="hard">{foreach $users as $name}{$name|escape}{if !$name@last}, {/if}{/foreach}</textarea>
			</div>
			<div class="form-group">
				<label for="select_groups" class="control-label">
					{tr}Make this the default group:{/tr}
				</label>
				<select name="checked_groups[]" id="select_groups" class="form-control">
					{section name=ix loop=$all_groups}
						{if $all_groups[ix] != 'Anonymous'}
							<option value="{$all_groups[ix]|escape}">{$all_groups[ix]|escape}</option>
						{/if}
					{/section}
				</select>
			</div>
			<div class="submit">
				<button
					id="manage-groups"
					name="manage-groups"
					type='button'
					class="btn btn-primary"
					onclick="confirmAction(this, {ldelim}'closest':'form'{rdelim});">
						{tr}OK{/tr}
				</button>
				{$encodedItems = json_encode($users)}
				<input type='hidden' name='users' value="{$encodedItems|escape}">
				{$encodedExtra = json_encode($extra)}
				<input type='hidden' name='extra' value="{$encodedExtra|escape}">
				<input type='hidden' name='daconfirm' value="y">
				<input type='hidden' name='ticket' value="{$ticket}">
			</div>
		</fieldset>
	</form>
{/block}