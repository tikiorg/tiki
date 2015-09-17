{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	<form role="form" class="form" method="post" action="{service controller=managestream action=change_rule_status}">
		{if $status eq "enabled"}
			{remarksbox type="tip" title="{tr}Disable rule?{/tr}" close="n"}{/remarksbox}
		{elseif $status eq "disabled"}
			{remarksbox type="tip" title="{tr}Enable rule?{/tr}" close="n"}{/remarksbox}
		{else}
			{remarksbox type="warning" title="{tr}Unknown status{/tr}" close="n"}{/remarksbox}
		{/if}
		<pre>{$rule.rule}</pre>
		<div class="submit">
			<input type="hidden" name="confirm" value="1"/>
			<input type="hidden" name="ruleId" value="{$rule.ruleId|escape}"/>
			<input type="submit" class="btn btn-primary" value="{tr}Confirm{/tr}"/>
		</div>
	</form>
{/block}