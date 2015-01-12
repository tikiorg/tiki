{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	<div>
		<h5>{$customMsg}</h5>
		<ul>
			{foreach from=$items item=name}
				<li>{$name|escape}</li>
			{/foreach}
		</ul>
	</div>
	<form id='confirm' action="{$confirmAction}" method="post">
		<fieldset>
			{* the below query function returns the $items and $extra variables *}
			{query _type='form_input'}
			<input type="hidden" name="ticket" value="{$ticket}">
			<input type="hidden" name="daconfirm" value="y">
			<div class="submit">
				<button type='submit' name="confirm" class="btn btn-primary">
					{$confirmButton}
				</button>
			</div>
		</fieldset>
	</form>
{/block}
