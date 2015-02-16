{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	<form id="merge_topic" method="post" action="{service controller=forum action=merge_topic}">
		<fieldset>
			<div class="form-group">
				<label for="mergefrom" class="control-label">
					{if $items|count > 1}
						{tr _0=$object}Merge these %0:{/tr}
					{else}
						{tr _0=$object}Merge this %0:{/tr}
					{/if}
				</label><br>
				<div id="mergefrom">
					<ul>
						{foreach from=$items key=id item=name}
							<li>{$name|escape}</li>
						{/foreach}
					</ul><br>
				</div>
				<label for="toId" class="control-label">
					{tr}
						With this topic:
					{/tr}
				</label><br><br>
				<div class="col-lg-7">
					<select class="form-control" name="toId">
						{foreach from=$toList key=id item=name}
							{if !array_key_exists($id, $items)}
								<option value="{$id|escape}">
									{$name|escape}
								</option>
							{/if}
						{/foreach}
					</select>
				</div>
			</div>
				<div class="submit">
					<button
							id="merge-topics"
							name="merge-topics"
							type='button'
							class="btn btn-primary"
							onclick="confirmAction(this, {ldelim}'closest':'form'{rdelim});">
						{tr}Merge{/tr}
					</button>
					{$encodedItems = json_encode($items)}
					<input type='hidden' name='items' value="{$encodedItems|escape}">
					{$encodedList = json_encode($toList)}
					<input type='hidden' name='toList' value="{$encodedList|escape}">
					{$encodedExtra = json_encode($extra)}
					<input type='hidden' name='extra' value="{$encodedExtra|escape}">
					<input type='hidden' name='ticket' value="{$ticket}">
					<input type='hidden' name='daconfirm' value="y">
				</div>
		</fieldset>
	</form>
{/block}
