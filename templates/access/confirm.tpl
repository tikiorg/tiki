{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	<div>
		<h5>{$customMsg}</h5>
		{if isset($items) && $items|count > 0}
			{if $items|count < 16}
				<ul>
					{foreach $items as $name}
						<li>
							{$name|escape}
						</li>
					{/foreach}
				</ul>
			{else}
				{foreach $items as $name}
					{$name|escape}{if !$name@last}, {/if}
				{/foreach}
			{/if}
		{/if}<br>
	</div>
	<form id='confirm' action="{$confirmAction}" method="post">
		<fieldset>
			{* the below query function returns the $items and $extra and $ticket variables *}
			{query _type='form_input'}
			<input type="hidden" name="daconfirm" value="y">
			<div class="submit">
				<button
						onclick="confirmAction(this, {ldelim}'closest':'form'{rdelim});"
						type='button' name="confirm" id="confirm" class="btn btn-primary">
					{$confirmButton}
				</button>
			</div>
		</fieldset>
	</form>
{/block}
