{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	<form method="post" action="{service controller=user action=manage_groups}">
		<fieldset>
			<div class="form-group">
				<label for="userlist" class="control-label">
					{if $users|count eq 1}
						{tr}For this user:{/tr}
					{else}
						{tr}For these selected users:{/tr}
					{/if}
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
				<label for="add_remove" class="control-label">
					{tr}Add to or remove from:{/tr}
				</label>
				<div class="radio">
					<label style="margin-right:20px">
						<input type="radio" name="add_remove" id="add" value="add" checked="">
						{tr}Add to{/tr}
					</label>
					<label>
						<input type="radio" name="add_remove" id="remove" value="remove">
						{tr}Remove from{/tr}
					</label>
				</div>
			</div>
			<div class="form-group">
				<label for="select_groups" class="control-label">
					{tr}These groups:{/tr}
				</label>
				<select name="checked_groups[]" id="select_groups" multiple="multiple" size="{$countgrps}" class="form-control">
					{section name=ix loop=$all_groups}
						{if $all_groups[ix] != 'Anonymous' && $all_groups[ix] != 'Registered'}
							<option value="{$all_groups[ix]|escape}">{$all_groups[ix]|escape}</option>
						{/if}
					{/section}
				</select>
				{if $chosenpref neq 'y'}
					<div class="help-block">
						{tr}Use Ctrl+Click or Command+Click to select multiple options{/tr}
					</div>
				{/if}
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