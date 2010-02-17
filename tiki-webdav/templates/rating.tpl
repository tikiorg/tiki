{if $rating_canvote}
	<p>
		<input type="hidden" name="rating_key" value="{$rating_key|escape}" />
		{foreach from=$rating_options item=v}
			<input type="radio" name="rating_value" value="{$v|escape}" id="{$rating_key|escape}_{$v|escape}" />
			<label for="{$rating_key|escape}_{$v|escape}">{$v|escape}</label>
		{/foreach}
		<input type="submit" value="{tr}Rate{/tr}"/>
	</p>
{else}
	{tr}You already voted on this topic.{/tr}
{/if}
