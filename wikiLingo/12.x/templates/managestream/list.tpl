<h3>{tr}Rule List{/tr}</h3>
<ol>
	{foreach from=$rules item=rule}
		<li>
			<span style="float: right">
				<a class="rule-delete" href="{service controller=managestream action=delete id=$rule.ruleId}" data-rule-id="{$rule.ruleId|escape}">{icon _id=cross alt="{tr}Delete{/tr}"}</a>
			</span>
			<strong>{$ruleTypes[$rule.ruleType]|escape}:</strong> {$rule.eventType|escape}
			<div>{$rule.notes|escape}</div>
			{if $rule.ruleType neq 'advanced'}
				<button class="rule-edit" data-rule-type="{$rule.ruleType|escape}" data-rule-id="{$rule.ruleId|escape}">{tr}Edit{/tr}</button>
			{/if}
			<button class="rule-edit" data-rule-type="advanced" data-rule-id="{$rule.ruleId|escape}">{tr}Advanced{/tr}</button>
		</li>
	{/foreach}
</ol>
<h4>{tr}Add Rules{/tr}</h4>
{foreach from=$ruleTypes key=rule item=label}
	<button class="rule-add" data-rule-type="{$rule|escape}">{$label|escape}</button>
{/foreach}
