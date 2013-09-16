{if $removed}
	{tr}The rule has been removed.{/tr}
{else}
	<form class="simple" method="post" action="{service controller=managestream action=delete}">
		<p>{tr}Are you certain you want to delete this rule?{/tr}</p>
		<div>{$rule.notes|escape}</div>
		<pre>{$rule.rule|escape}</pre>
		<div class="submit">
			<input type="submit" class="btn btn-default" value="{tr}Delete{/tr}"/>
			<input type="hidden" name="ruleId" value="{$rule.ruleId|escape}"/>
		</div>
	</form>
{/if}
