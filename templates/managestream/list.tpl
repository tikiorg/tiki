<h3>Rule List</h3>
<ol>
	{foreach from=$rules item=rule}
		<li>
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
