{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	{include file='access/include_items.tpl'}
	<form method="post" id="confirm-action" class="confirm-action" action="{service controller=$confirmController action=$confirmAction}">
		{include file='access/include_hidden.tpl'}
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
			<select name="checked_groups[]" multiple="multiple" size="{$countgrps}" class="form-control">
				{section name=ix loop=$all_groups}
					{if $all_groups[ix] != 'Anonymous' && $all_groups[ix] != 'Registered'}
						<option value="{$all_groups[ix]|escape}">{$all_groups[ix]|escape}</option>
					{/if}
				{/section}
			</select>
			{if $prefs.jquery_ui_chosen !== 'y'}
				<div class="help-block">
					{tr}Use Ctrl+Click or Command+Click to select multiple options{/tr}
				</div>
			{/if}
		</div>
	</form>
	{include file='access/include_footer.tpl'}
{/block}