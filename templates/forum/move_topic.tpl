{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	<form id="move_topic" method="post" action="{service controller=forum action=move_topic}">
		<fieldset>
			<div class="form-group">
				<label for="movefrom" class="control-label">
					{if $items|count > 1}
						{tr}Move these topics:{/tr}
					{else}
						{tr}Move this topic:{/tr}
					{/if}
				</label><br>
				<div id="movefrom">
					<ul>
						{foreach from=$items key=id item=name}
							<li>{$name|escape}</li>
						{/foreach}
					</ul><br>
				</div>
				<label for="toId" class="control-label">
					{tr _0=$forumName _1="<em>" _2="</em>"}
						From the %1%0%2 forum to this forum:
					{/tr}
				</label><br><br>
				<div class="col-lg-7">
					<select class="form-control" name="toId">
						{foreach from=$toList key=id item=name}
							{if $id ne $forumId}
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
							id="move-topics"
							name="move-topics"
							type='button'
							class="btn btn-primary"
							onclick="confirmAction(this, {ldelim}'closest':'form'{rdelim});">
						{tr}Move{/tr}
					</button>
					{$encodedItems = json_encode($items)}
					<input type='hidden' name='items' value="{$encodedItems|escape}">
					{$encodedForums = json_encode($toList)}
					<input type='hidden' name='toList' value="{$encodedForums|escape}">
					<input type='hidden' name='forumId' value="{$forumId|escape}">
					<input type='hidden' name='ticket' value="{$ticket}">
					<input type='hidden' name='daconfirm' value="y">
				</div>
		</fieldset>
	</form>
{/block}
