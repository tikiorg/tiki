{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	{include file='access/include_items.tpl'}
	<form method="post" id="confirm-action" class="confirm-action" action="{service controller=$confirmController action=$confirmAction}">
		{include file='access/include_hidden.tpl'}
		<div class="form-group">
			<label class="control-label" for="remove_users">{tr}Remove users{/tr}</label>
			<div>
				<input class="form-control" type="checkbox" id="remove_users" name="remove_users" checked="checked" disabled="disabled">
				<div class="help-block">
					{tr}Remove these user accounts{/tr}
				</div>
			</div>
		</div>
		{if $prefs.feature_wiki_userpage == 'y'}
			<div class="form-group">
				<label class="control-label" for="remove_pages">{tr}Remove the user pages{/tr}</label>
				<div>
					<input class="form-control" type="checkbox" id="remove_pages" name="remove_pages">
					<div class="help-block">
						{tr}Remove the user pages belonging to these users{/tr}
					</div>
				</div>
			</div>
		{/if}
		{if $prefs.feature_banning eq 'y'}
			<div class="form-group">
				<label class="control-label" for="ban_users">{tr}Ban users{/tr}</label>
				<div>
					<input class="form-control" type="checkbox" id="ban_users" name="ban_users">
					<div class="help-block">
						{tr}Ban these users{/tr}
					</div>
				</div>
			</div>
		{/if}
	</form>
	{include file='access/include_footer.tpl'}
{/block}
