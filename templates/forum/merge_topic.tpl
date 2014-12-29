{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	{if $selectedTopics|count > 0}
		<form id="merge_topic" method="post" action="{service controller=forum action=merge_topic}">
			<fieldset>
				<div class="form-group">
					<label for="mergefrom" class="control-label">
						{if $selectedTopics|count > 1}
							{tr}Merge these topics:{/tr}
						{else}
							{tr}Merge this topic:{/tr}
						{/if}
					</label><br>
					<div id="mergefrom">
						<ul>
							{foreach from=$selectedTopics key=id item=name}
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
								{if !array_key_exists($id, $selectedTopics)}
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
							{tr}Merge{/tr}
						</button>
						{$encodedTopics = json_encode($selectedTopics)}
						<input type='hidden' name='forumtopic' value="{$encodedTopics|escape}">
						{$encodedList = json_encode($toList)}
						<input type='hidden' name='toList' value="{$encodedList|escape}">
					</div>
			</fieldset>
		</form>
	{else}
		<div class="alert alert-warning">
			<h4>{tr}Oops!{/tr}</h4>
			<p>
				{tr}
					No topics have been selected. Please check the topics you wish to merge before clicking the merge button.
				{/tr}
			</p>
		</div>
	{/if}
{/block}
