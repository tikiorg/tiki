{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	<form method="post" action="{service controller=user action=email_wikipage}">
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
				<label class="control-label">{tr}Email this wiki page{/tr}</label>
				<div>
					<input class="form-control" type="text" name="wikiTpl">
					<div class="help-block">{tr}Enter page name.
							The wiki page must have a page description, which is used as the subject of the email.
							Enable the page descriptions feature at Control Panels &gt; Wiki.{/tr}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label">{tr}Bcc{/tr}</label>
				<div>
					<input class="form-control" type="text" name="bcc">
					<div class="help-block">{tr}Enter a valid email to send a blind copy to (optional).{/tr}</div>
				</div>
			</div>
			<div class="submit">
				<button
						id="email-wikipage"
						name="email-wikipage"
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
			</div>
		</fieldset>
	</form>
{/block}