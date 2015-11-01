{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	<form role="form" class="form" method="post" action="{service controller=managestream action=change_rule_type}">
		<div class="form-group">
			<label class="control-label">
				{tr}Description{/tr}
			</label>
			<textarea class="form-control" readonly>{$rule.notes|escape}</textarea>
		</div>
		<div class="form-group">
			<label class="control-label">
				{tr}Rule{/tr}
			</label>
			<pre>{$rule.rule|escape}</pre>
		</div>
		<div class="form-group">
			<label class="control-label">
				{tr}Current Type{/tr}
			</label>
			<pre>{$currentRuleType|escape}</pre>
		</div>
		<div class="form-group">
			<label class="control-label">
				{tr}New Type{/tr}
			</label>
			<select name="ruleType" class="form-control">
				{foreach from=$ruleTypes key=ruleKey item=ruleType}
					<option value="{$ruleKey}">
						{$ruleType|escape}
					</option>
				{/foreach}
			</select>
		</div>
		<div class="submit">
			<input type="hidden" name="confirm" value="1"/>
			<input type="hidden" name="ruleId" value="{$rule.ruleId|escape}"/>
			<input type="submit" class="btn btn-primary" value="{tr}Change{/tr}"/>
		</div>
	</form>
{/block}