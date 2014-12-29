{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	{if $selectedTopics|count > 0}
		<form id="move_topic" method="post" action="{service controller=forum action=move_topic}">
			<fieldset>
				<div class="form-group">
					<label for="movefrom" class="control-label">
						{if $selectedTopics|count > 1}
							{tr}Move these topics:{/tr}
						{else}
							{tr}Move this topic:{/tr}
						{/if}
					</label><br>
					<div id="movefrom">
						<ul>
							{foreach from=$selectedTopics key=id item=name}
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
						<button type='submit' id="success" name="success" class="btn btn-primary success">
							{tr}Move{/tr}
						</button>
						{$encodedTopics = json_encode($selectedTopics)}
						<input type='hidden' name='forumtopic' value="{$encodedTopics|escape}">
						{$encodedForums = json_encode($toList)}
						<input type='hidden' name='toList' value="{$encodedForums|escape}">
						<input type='hidden' name='forumId' value="{$forumId|escape}">
					</div>
			</fieldset>
		</form>
	{else}
		<div class="alert alert-warning">
			<h4>{tr}Oops!{/tr}</h4>
			<p>
				{tr}
					No topics have been selected. Please check the topics you wish to move before clicking the move button.
				{/tr}
			</p>
		</div>
	{/if}
{/block}
