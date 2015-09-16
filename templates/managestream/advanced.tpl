{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	{if $rule.ruleId && $rule.ruleType neq 'advanced'}
		{remarksbox title="{tr}Operation not reversible{/tr}"}
			{tr}This action is currently of a basic type. Using the advanced editor will prevent the simple editor to be used.{/tr}
		{/remarksbox}
	{/if}
	<form role="form" class="form form-horizontal" method="post" action="{service controller=managestream action=advanced}">
		<div class="form-group clearfix">
			<label for="event" class="control-label col-md-3">
				{tr}Event{/tr}
			</label>
			<div class="col-md-9">
				<select name="event" class="form-control">
					{foreach from=$eventTypes item=eventName}
						<option value="{$eventName|escape}"{if $rule.eventType eq $eventName} selected{/if}>{$eventName|escape}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="form-group clearfix">
			<label for="notes" class="control-label col-md-3">
				{tr}Description{/tr}
			</label>
			<div class="col-md-9">
				<textarea name="notes" class="form-control" rows="3">{$rule.notes|escape}</textarea>
			</div>
		</div>
		<div class="form-group clearfix">
			<label for="rule" class="control-label col-md-3">
				{tr}Rule{/tr}
			</label>
			<div class="col-md-9">
				<textarea name="rule" class="form-control" rows="3">{$rule.rule|escape}</textarea>
			</div>
		</div>
		<div class="submit">
			<input type="hidden" name="ruleId" value="{$rule.ruleId|escape}"/>
			<input type="submit" class="btn btn-primary" value="{tr}Save{/tr}"/>
		</div>
	</form>
{/block}