{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	{if $removed}
		{tr}The rule has been removed.{/tr}
	{else}
		<form role="form" class="form form-horizontal" method="post" action="{service controller=managestream action=delete}">
			{remarksbox type="warning" close="n" title="{tr}Are you sure you want to delete this rule?{/tr}"}{/remarksbox}
			<div class="form-group clearfix">
				<label class="control-label col-md-3">
					{tr}Description{/tr}
				</label>
				<div class="col-md-9">
					{$rule.notes|escape}
				</div>
			</div>
			<div class="form-group clearfix">
				<label class="control-label col-md-3">
					{tr}Rule{/tr}
				</label>
				<div class="col-md-9">
					<pre>{$rule.rule|escape}</pre>
				</div>
			</div>
			<div class="submit">
				<input type="hidden" name="ruleId" value="{$rule.ruleId|escape}"/>
				<input type="submit" class="btn btn-warning" value="{tr}Delete{/tr}"/>
			</div>
		</form>
	{/if}
{/block}