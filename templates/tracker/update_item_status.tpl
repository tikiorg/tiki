{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	{if $status neq 'DONE'}
		<div>
			<form method="post" action="{service controller=tracker
			action=update_item_status
			trackerId={$trackerId}
			itemId={$itemId}
			item_label={$item_label}
			status={$status}
			redirect={$redirect}
			confirm=1
			}">
				<p>
					{if $confirmation_message}
						{tr}{$confirmation_message}{/tr}
					{elseif $status eq "o"}
						{tr _0="{$item_label}"}Are you sure you want to set this %0 to "open"?{/tr}
					{elseif $status eq "p"}
						{tr _0="{$item_label}"}Are you sure you want to set this %0 to "pending"?{/tr}
					{elseif $status eq "c"}
						{tr _0="{$item_label}"}Are you sure you want to set this %0 to "closed"?{/tr}
					{/if}
				</p>
				<div class="submit">
					<input type="submit" class="btn btn-action" value="{$button_label}" >
				</div>
			</form>
		</div>
	{else}
		<div id="success-status">
			<div class="alert alert-info"><strong>Success!</strong><br>Your requested action has been completed.</div>
		</div>
		{jq}
			$("#success-status").closest(".bs-modal").on('hidden.bs.modal', function () {
			window.location.replace("{{$redirect|escape:"url"}}");
			});
		{/jq}
	{/if}
{/block}
